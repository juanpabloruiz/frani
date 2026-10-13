<?php
declare(strict_types=1);

/**
 * El transporte recibe método, URL, headers y body, y devuelve status y body.
 * Inyectarlo permite probar todos los resultados sin publicar en Facebook.
 *
 * @return array{estado:string,post_id:?string,error:?string}
 */
function facebook_api_publicar(array $config, array $payload, ?callable $transport = null): array
{
    try {
        $config = facebook_api_configuracion($config);
        if (!is_string($payload['message'] ?? null) || !is_string($payload['link'] ?? null)
            || filter_var($payload['link'], FILTER_VALIDATE_URL) === false
            || strtolower((string) parse_url($payload['link'], PHP_URL_SCHEME)) !== 'https'
            || parse_url($payload['link'], PHP_URL_USER) !== null
            || parse_url($payload['link'], PHP_URL_PASS) !== null) {
            return facebook_api_resultado('fallido', 'El contenido de la publicación no es válido.');
        }
    } catch (Throwable $error) {
        return facebook_api_resultado('fallido', 'La configuración de Facebook no es válida.');
    }

    $body = http_build_query([
        'message' => $payload['message'],
        'link' => $payload['link'],
    ], '', '&', PHP_QUERY_RFC3986);

    try {
        $response = facebook_api_solicitud($config, 'POST', '/feed', $body, $transport);
    } catch (Throwable $error) {
        // Un corte después del envío no confirma si Facebook creó el post.
        // No se propaga el error del transporte: puede contener credenciales.
        return facebook_api_resultado('incierto', 'No se pudo confirmar la respuesta de Facebook. Revisá la página antes de reintentar.');
    }

    $data = facebook_api_json($response['body']);
    if ($data === null) {
        return facebook_api_resultado('incierto', 'Facebook respondió sin un resultado reconocible. Revisá la página antes de reintentar.');
    }

    if (isset($data['error']) && is_array($data['error'])) {
        $apiError = $data['error'];
        $code = facebook_api_codigo($apiError['code'] ?? null);
        // Los rechazos de autorización requieren corregir el acceso.
        if (in_array($code, ['10', '102', '190', '200'], true)) {
            return facebook_api_resultado('fallido', facebook_api_error_seguro($apiError));
        }
        if (($apiError['is_transient'] ?? false) === true) {
            return facebook_api_resultado('reintentar', facebook_api_error_seguro($apiError));
        }
        return facebook_api_resultado('fallido', facebook_api_error_seguro($apiError));
    }

    $postId = $data['id'] ?? null;
    if ($response['status'] >= 200 && $response['status'] < 300 && is_string($postId)
        && preg_match('/\A[1-9][0-9]*(?:_[1-9][0-9]*)?\z/', $postId) === 1) {
        return ['estado' => 'publicado', 'post_id' => $postId, 'error' => null];
    }

    // Incluye 5xx, redirects y respuestas sin ID: no hay certeza de rechazo.
    return facebook_api_resultado('incierto', 'Facebook no confirmó el identificador de la publicación. Revisá la página antes de reintentar.');
}

/** @return array{id:string,name:string} */
function facebook_api_pagina(array $config, ?callable $transport = null): array
{
    try {
        $config = facebook_api_configuracion($config);
        $response = facebook_api_solicitud($config, 'GET', '?fields=id,name', null, $transport);
        $data = facebook_api_json($response['body']);
        if ($response['status'] >= 200 && $response['status'] < 300
            && $data !== null && !isset($data['error'])
            && ($data['id'] ?? null) === $config['page_id']
            && is_string($data['name'] ?? null) && trim($data['name']) !== '') {
            return ['id' => $data['id'], 'name' => $data['name']];
        }
    } catch (Throwable $error) {
        // Siempre entregar un mensaje fijo, sin token ni respuesta de Meta.
    }
    throw new RuntimeException('No se pudo validar la página de Facebook. Revisá el ID y los permisos del token.');
}

/** @return array{page_id:string,access_token:string,version:string} */
function facebook_api_configuracion(array $config): array
{
    $pageId = $config['page_id'] ?? '';
    $token = $config['access_token'] ?? '';
    $version = $config['version'] ?? 'v26.0';
    if ((!is_string($pageId) && !is_int($pageId))
        || preg_match('/\A[1-9][0-9]*\z/', (string) $pageId) !== 1
        || !is_string($token) || trim($token) === '' || strpbrk($token, "\r\n") !== false
        || !is_string($version) || preg_match('/\Av[1-9][0-9]*\.[0-9]+\z/', $version) !== 1) {
        throw new InvalidArgumentException('La configuración de Facebook no es válida.');
    }
    return ['page_id' => (string) $pageId, 'access_token' => trim($token), 'version' => $version];
}

/** @return array{status:int,body:string} */
function facebook_api_solicitud(array $config, string $method, string $suffix, ?string $body, ?callable $transport): array
{
    $url = 'https://graph.facebook.com/' . $config['version'] . '/' . $config['page_id'] . $suffix;
    $headers = ['Accept: application/json', 'Authorization: Bearer ' . $config['access_token']];
    if ($method === 'POST') {
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    }
    $response = ($transport ?? 'facebook_api_transportar')($method, $url, $headers, $body);
    if (!is_array($response) || !is_int($response['status'] ?? null)
        || $response['status'] < 100 || $response['status'] > 599
        || !is_string($response['body'] ?? null)) {
        throw new RuntimeException('El transporte de Facebook no devolvió una respuesta válida.');
    }
    return ['status' => $response['status'], 'body' => $response['body']];
}

/** @return array{status:int,body:string} */
function facebook_api_transportar(string $method, string $url, array $headers, ?string $body): array
{
    $options = [
        'method' => $method,
        'header' => implode("\r\n", $headers),
        'timeout' => 20,
        'ignore_errors' => true,
        'follow_location' => 0,
        'max_redirects' => 0,
        'protocol_version' => 1.1,
    ];
    if ($body !== null) {
        $options['content'] = $body;
    }
    $context = stream_context_create([
        'http' => $options,
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true, 'allow_self_signed' => false],
    ]);
    $http_response_header = [];
    // El límite evita consumir memoria con una respuesta anormalmente grande.
    $responseBody = @file_get_contents($url, false, $context, 0, 1048576);
    if ($responseBody === false) {
        throw new RuntimeException('No se pudo completar la solicitud a Facebook.');
    }
    $status = 0;
    foreach ($http_response_header as $header) {
        if (preg_match('/\AHTTP\/\S+\s+([0-9]{3})(?:\s|\z)/', $header, $matches) === 1) {
            $status = (int) $matches[1];
        }
    }
    return ['status' => $status, 'body' => $responseBody];
}

function facebook_api_json(string $body): ?array
{
    try {
        $data = json_decode($body, true, 32, JSON_THROW_ON_ERROR);
        return is_array($data) ? $data : null;
    } catch (JsonException $error) {
        return null;
    }
}

function facebook_api_codigo(mixed $code): ?string
{
    if (is_int($code) && $code >= 0) {
        return (string) $code;
    }
    if (is_string($code) && preg_match('/\A[0-9]{1,9}\z/', $code) === 1) {
        return $code;
    }
    return null;
}

function facebook_api_error_seguro(array $error): string
{
    $details = [];
    $code = facebook_api_codigo($error['code'] ?? null);
    $subcode = facebook_api_codigo($error['error_subcode'] ?? null);
    if ($code !== null) {
        $details[] = 'código ' . $code;
    }
    if ($subcode !== null) {
        $details[] = 'subcódigo ' . $subcode;
    }
    // No usar message, error_user_msg o fbtrace_id recibidos de Meta.
    return 'Facebook rechazó la publicación' . ($details ? ' (' . implode(', ', $details) . ')' : '') . '.';
}

/** @return array{estado:string,post_id:null,error:string} */
function facebook_api_resultado(string $estado, string $error): array
{
    return ['estado' => $estado, 'post_id' => null, 'error' => $error];
}
