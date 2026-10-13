CREATE TABLE IF NOT EXISTS facebook_publicaciones (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    producto_id INT UNSIGNED NOT NULL,
    pagina_id VARCHAR(32) NOT NULL,
    tipo ENUM('alta', 'edicion') NOT NULL,
    contenido LONGTEXT NOT NULL,
    estado ENUM('pendiente', 'enviando', 'publicado', 'reintentar', 'incierto', 'fallido', 'omitido') NOT NULL DEFAULT 'pendiente',
    intentos SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    disponible TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    creado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modificado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    publicacion_id VARCHAR(80) DEFAULT NULL,
    ultimo_error VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_facebook_pendientes (pagina_id, estado, disponible, id),
    KEY idx_facebook_producto (producto_id, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
