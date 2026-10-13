CREATE DATABASE IF NOT EXISTS `frani`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_520_ci;

USE `frani`;

CREATE TABLE IF NOT EXISTS `categorias` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `uri` VARCHAR(200) DEFAULT NULL,
  `agregado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modificado` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_categorias_nombre` (`nombre`),
  UNIQUE KEY `uk_categorias_uri` (`uri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE IF NOT EXISTS `productos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `producto` VARCHAR(200) NOT NULL,
  `uri` VARCHAR(200) DEFAULT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `costo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `precio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock` INT NULL DEFAULT NULL,
  `id_categoria` INT UNSIGNED NOT NULL,
  `agregado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modificado` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_productos_uri` (`uri`),
  KEY `idx_productos_id_categoria` (`id_categoria`),
  CONSTRAINT `fk_productos_categorias`
    FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE IF NOT EXISTS `facturas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `efectivo` DECIMAL(10,2) DEFAULT NULL,
  `transferencia` DECIMAL(10,2) DEFAULT NULL,
  `deuda` DECIMAL(10,2) DEFAULT NULL,
  `descuento` TINYINT UNSIGNED NULL DEFAULT NULL,
  `descuento_importe` DECIMAL(10,2) NULL DEFAULT NULL,
  `detalle` TEXT NOT NULL,
  `observaciones` TEXT DEFAULT NULL,
  `agregado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modificado` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE IF NOT EXISTS `clientes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `telefono` VARCHAR(50) DEFAULT NULL,
  `agregado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modificado` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE IF NOT EXISTS `mascotas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo` VARCHAR(150) NOT NULL,
  `talle` VARCHAR(50) DEFAULT NULL,
  `precio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `creado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modificado` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `correo` VARCHAR(200) NOT NULL,
  `clave` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) DEFAULT NULL,
  `ingreso` TIMESTAMP NULL DEFAULT NULL,
  `editado` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuarios_correo` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `categorias` (`nombre`, `uri`)
SELECT 'General', 'general'
WHERE NOT EXISTS (
  SELECT 1
  FROM `categorias`
  WHERE `nombre` = 'General'
);

INSERT IGNORE INTO `usuarios` (`nombre`, `correo`, `clave`)
VALUES ('admin', 'pabloruiz1980@gmail.com', '$2y$12$emBPsJNMpsTuqm7ww9pUFuMFC5G/XwE3r/JmDndscLZ5WC8wsJGau');

-- Normalización: los campos de pago sin valor se guardan como NULL, nunca como 0.00.
UPDATE `facturas` SET `efectivo`     = NULL WHERE `efectivo`     = 0;
UPDATE `facturas` SET `transferencia` = NULL WHERE `transferencia` = 0;
UPDATE `facturas` SET `deuda`        = NULL WHERE `deuda`        = 0;
UPDATE `facturas` SET `descuento`    = NULL WHERE `descuento`    = 0;

-- Cola de publicaciones automáticas en Facebook (sin credenciales).
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
