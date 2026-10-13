<?php
declare(strict_types=1);

// Pruebas sin conexión de red, token real ni publicaciones reales.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../src/facebook_api.php';

function facebook_api_comprobar(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function facebook_api_fake(int $status, string $body): callable
{
    return static fn (string $method, string $url, array $headers, ?string $requestBody): array
        => ['status' => $status, 'body' => $body];
}

try {
    $config = ['page_id' => '555387371639380', 'access_token' => 'TOKEN_FICTICIO_PRIVADO', 'version' => 'v26.0'];
    $payload = ['message' => "Nuevo producto: Muñeca & Juguetes\nPrecio: $ 2.500 ARS", 'link' => 'https://frani.ar/juguetes/muneca'];
    $seen = [];
    $transport = static function (string $method, string $url, array $headers, ?string $body) use (&$seen): array {
        $seen = [$method, $url, $headers, $body];
        return ['status' => 200, 'body' => '{"id":"555387371639380_123456789"}'];
    };
    $result = facebook_api_publicar($config, $payload, $transport);
    facebook_api_comprobar($result === ['estado' => 'publicado', 'post_id' => '555387371639380_123456789', 'error' => null], 'Debe conservar el ID de la publicación confirmada.');
    facebook_api_comprobar($seen[0] === 'POST' && $seen[1] === 'https://graph.facebook.com/v26.0/555387371639380/feed', 'Debe publicar en el feed de la página configurada.');
    facebook_api_comprobar(in_array('Authorization: Bearer TOKEN_FICTICIO_PRIVADO', $seen[2], true), 'Debe autorizar con Bearer.');
    facebook_api_comprobar(!str_contains($seen[1], $config['access_token']) && !str_contains((string) $seen[3], $config['access_token']), 'El token no debe enviarse en URL ni body.');
    parse_str((string) $seen[3], $form);
    facebook_api_comprobar($form === $payload, 'Debe preservar el mensaje y enlace al codificar el formulario.');

    $numeric = facebook_api_publicar($config, $payload, facebook_api_fake(201, '{"id":"123456789"}'));
    facebook_api_comprobar($numeric['estado'] === 'publicado' && $numeric['post_id'] === '123456789', 'Debe aceptar un ID numérico.');

    $cases = [
        [400, '{"error":{"code":190,"error_subcode":463,"message":"TOKEN_FICTICIO_PRIVADO"}}', 'fallido'],
        [403, '{"error":{"code":200,"is_transient":true,"message":"TOKEN_FICTICIO_PRIVADO"}}', 'fallido'],
        [400, '{"error":{"code":100,"is_transient":false}}', 'fallido'],
        [503, '{"error":{"code":2,"is_transient":true,"message":"TOKEN_FICTICIO_PRIVADO"}}', 'reintentar'],
        [429, '{"error":{"code":4,"is_transient":true}}', 'reintentar'],
        [500, '<html>Server Error TOKEN_FICTICIO_PRIVADO</html>', 'incierto'],
        [503, '{"id":"123456789"}', 'incierto'],
        [200, 'JSON inválido TOKEN_FICTICIO_PRIVADO', 'incierto'],
        [200, '{}', 'incierto'],
        [200, '{"id":"no-es-un-id"}', 'incierto'],
        [200, '{"id":123456789}', 'incierto'],
        [302, '{"id":"123456789"}', 'incierto'],
    ];
    foreach ($cases as [$status, $body, $expected]) {
        $result = facebook_api_publicar($config, $payload, facebook_api_fake($status, $body));
        facebook_api_comprobar($result['estado'] === $expected && $result['post_id'] === null, 'Clasificación incorrecta de respuesta HTTP ' . $status . ': ' . $expected);
        facebook_api_comprobar(!str_contains(json_encode($result), $config['access_token']), 'El resultado no debe revelar el token de la respuesta de Meta.');
    }

    $safeError = facebook_api_publicar($config, $payload, facebook_api_fake(400, '{"error":{"code":190,"error_subcode":463,"message":"TOKEN_FICTICIO_PRIVADO","error_user_msg":"TOKEN_FICTICIO_PRIVADO","fbtrace_id":"TOKEN_FICTICIO_PRIVADO"}}'));
    facebook_api_comprobar(str_contains($safeError['error'], 'código 190') && str_contains($safeError['error'], 'subcódigo 463'), 'Debe informar únicamente los códigos seguros de Meta.');
    $unsafeCode = facebook_api_publicar($config, $payload, facebook_api_fake(400, '{"error":{"code":"TOKEN_FICTICIO_PRIVADO","error_subcode":"TOKEN_FICTICIO_PRIVADO"}}'));
    facebook_api_comprobar(!str_contains($unsafeCode['error'], $config['access_token']), 'Debe descartar códigos de error no numéricos.');

    $timeout = static function (string $method, string $url, array $headers, ?string $body): array {
        throw new RuntimeException('Timeout con TOKEN_FICTICIO_PRIVADO');
    };
    $result = facebook_api_publicar($config, $payload, $timeout);
    facebook_api_comprobar($result['estado'] === 'incierto' && !str_contains($result['error'], $config['access_token']), 'Un timeout debe quedar incierto sin filtrar excepciones.');
    $invalidTransport = static fn (string $method, string $url, array $headers, ?string $body) => false;
    facebook_api_comprobar(facebook_api_publicar($config, $payload, $invalidTransport)['estado'] === 'incierto', 'Un transporte sin respuesta válida debe quedar incierto.');
    $badConfig = $config;
    $badConfig['access_token'] = "TOKEN\r\nCabecera: inyectada";
    facebook_api_comprobar(facebook_api_publicar($badConfig, $payload, $transport)['estado'] === 'fallido', 'Debe rechazar tokens que inyecten headers.');
    $badConfig = $config;
    $badConfig['version'] = 'v26.0/../../otro';
    facebook_api_comprobar(facebook_api_publicar($badConfig, $payload, $transport)['estado'] === 'fallido', 'Debe validar la versión de la API antes de construir la URL.');
    $badConfig = $config;
    $badConfig['page_id'] = ['TOKEN_FICTICIO_PRIVADO'];
    facebook_api_comprobar(facebook_api_publicar($badConfig, $payload, $transport)['estado'] === 'fallido', 'Debe rechazar IDs de página con tipos inválidos.');
    facebook_api_comprobar(facebook_api_publicar($config, ['message' => 'Producto', 'link' => 'http://frani.ar'], $transport)['estado'] === 'fallido', 'Debe exigir un enlace HTTPS del catálogo.');

    $pageTransport = static function (string $method, string $url, array $headers, ?string $body) use (&$seen): array {
        $seen = [$method, $url, $headers, $body];
        return ['status' => 200, 'body' => '{"id":"555387371639380","name":"Frani"}'];
    };
    $page = facebook_api_pagina($config, $pageTransport);
    facebook_api_comprobar($page === ['id' => '555387371639380', 'name' => 'Frani'], 'Debe devolver la identidad validada de la página.');
    facebook_api_comprobar($seen[0] === 'GET' && $seen[1] === 'https://graph.facebook.com/v26.0/555387371639380?fields=id,name' && $seen[3] === null, 'Debe consultar la página sin body.');
    facebook_api_comprobar(!str_contains($seen[1], $config['access_token']), 'Validar la página tampoco debe incluir el token en URL.');
    foreach ([facebook_api_fake(200, '{"id":"1","name":"Otra página"}'), facebook_api_fake(400, '{"error":{"message":"TOKEN_FICTICIO_PRIVADO"}}'), $timeout, $invalidTransport] as $failedPage) {
        $thrown = false;
        try {
            facebook_api_pagina($config, $failedPage);
        } catch (RuntimeException $error) {
            $thrown = true;
            facebook_api_comprobar(!str_contains($error->getMessage(), $config['access_token']), 'La validación no debe filtrar el token por excepciones.');
        }
        facebook_api_comprobar($thrown, 'Una página incorrecta o respuesta rechazada debe fallar la validación.');
    }

    fwrite(STDOUT, "OK: publicación simulada, autorización privada, errores, incertidumbre y validación de página.\n");
} catch (Throwable $error) {
    fwrite(STDERR, 'ERROR: ' . $error->getMessage() . "\n");
    exit(1);
}
