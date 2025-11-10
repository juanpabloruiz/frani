-- phpMyAdmin SQL Dump
-- version 5.2.2deb2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 10-11-2025 a las 23:22:50
-- Versión del servidor: 8.4.6-0ubuntu3
-- Versión de PHP: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `frani`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'Librería'),
(2, 'Regalos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` int NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `detalle` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `metodo` enum('efectivo','tarjeta','transferencia') COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `total` decimal(10,2) DEFAULT '0.00',
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id`, `nombre`, `detalle`, `metodo`, `total`, `fecha`) VALUES
(1, 'Miguel', '', 'efectivo', 570.00, '2024-11-21 23:10:29'),
(2, 'Jorge', '5 (1 x 200.00), 22 (2 x 550.00)', 'transferencia', 1300.00, '2024-11-21 23:30:39'),
(3, 'Marcelo', 'Birome color común (2 x 70.00), Borrador (4 x 70.00)', 'tarjeta', 420.00, '2024-11-21 23:35:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `items_factura`
--

CREATE TABLE `items_factura` (
  `id` int NOT NULL,
  `id_factura` int NOT NULL,
  `detalle` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `cantidad` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int NOT NULL,
  `producto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `categoria` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `producto`, `costo`, `precio`, `stock`, `categoria`, `fecha`) VALUES
(1, 'Afiche', 715.00, 850.00, 20, 'Librería', '2025-02-02 21:24:41'),
(4, 'Aro metálico chico carpeta', 31.00, 735.00, 0, 'Librería', '2023-02-21 03:00:00'),
(5, 'Auricular', 120.00, 1200.00, 0, 'Otros', '2023-02-13 03:00:00'),
(6, 'Cuaderno 48 hojas', 80.00, 980.00, 0, '', '2023-02-13 03:00:00'),
(7, 'Birome borrable', 1470.00, 1700.00, 0, '', '2023-02-13 03:00:00'),
(10, 'Birome Bic azul-negra-roja', 0.00, 750.00, 0, 'Librería', '2023-03-01 03:00:00'),
(13, 'Birome cuatro colores retractil', 146.00, 290.00, 0, '', '2023-03-01 03:00:00'),
(17, 'Brillantina', 185.00, 350.00, 0, 'Librería', '2023-02-15 03:00:00'),
(23, 'Carpeta escolar para anillo ', 368.00, 480.00, 0, '', '2023-03-01 03:00:00'),
(25, 'Carpeta Número 5 con dibujos', 300.00, 2100.00, 0, '', '2023-02-15 03:00:00'),
(26, 'Carpeta tapa transparente A4', 100.00, 190.00, 0, '', '2023-02-15 03:00:00'),
(28, 'auricular River Boca', 615.00, 1400.00, 0, 'Otros', '2023-02-27 03:00:00'),
(33, 'Cinta pack mediana', 300.00, 1700.00, 0, '', '2023-02-21 03:00:00'),
(34, 'Cinta scocht chica', 70.00, 158.00, 0, 'Librería', '2023-03-01 03:00:00'),
(38, 'Collar con tachas 57 cm', 280.00, 400.00, 0, '', '2023-02-15 03:00:00'),
(39, 'Collar cuero con puas', 380.00, 500.00, 0, '', '2023-02-15 03:00:00'),
(41, 'Comedero 22 cm mediano', 300.00, 370.00, 0, '', '2023-02-15 03:00:00'),
(42, 'Copos gomaespuma', 550.00, 550.00, 0, '', '2023-02-15 03:00:00'),
(43, 'Corta uñas grande', 600.00, 690.00, 0, '', '2023-03-01 03:00:00'),
(45, 'Correa más collar fino', 120.00, 300.00, 0, '', '2023-02-15 03:00:00'),
(46, 'Corrector chico', 40.00, 80.00, 0, '', '2023-02-21 03:00:00'),
(47, 'Corrector grande', 370.00, 630.00, 0, 'Librería', '2023-02-21 03:00:00'),
(48, 'Corta uñas infantil', 100.00, 200.00, 0, '', '2023-02-15 03:00:00'),
(49, 'Cuadernillo América', 495.00, 750.00, 0, '', '2023-02-15 03:00:00'),
(50, 'Cuaderno 24 hojas', 830.00, 900.00, 0, 'Librería', '2023-02-21 03:00:00'),
(51, 'cuaderno 84 hojas', 96.00, 1050.00, 0, 'Librería', '2023-02-15 03:00:00'),
(52, 'Cuaderno Rivadavia 98 hojas', 0.00, 1900.00, 0, '', '2023-02-24 03:00:00'),
(53, 'Cuchara', 50.00, 120.00, 0, '', '2023-02-15 03:00:00'),
(54, 'Encendedor', 55.00, 380.00, 0, 'Otros', '2023-02-15 03:00:00'),
(55, 'Esmalte duo argentina', 243.00, 320.00, 0, '', '2023-02-15 03:00:00'),
(56, 'Etiquetas por 5', 9.00, 25.00, 0, '', '2023-02-15 03:00:00'),
(57, 'Fibras por 6 Filgo', 0.00, 714.00, 0, 'Librería', '2023-02-27 03:00:00'),
(58, 'fibras por 12', 100.00, 800.00, 0, '', '2023-02-15 03:00:00'),
(59, 'Folio tamaño carpeta N° 3', 10.00, 105.00, 0, 'Librería', '2023-02-15 03:00:00'),
(60, 'Forro araña azul', 220.00, 250.00, 0, '', '2023-02-21 03:00:00'),
(61, 'Goma Eva', 590.00, 680.00, 0, '', '2023-02-27 03:00:00'),
(62, 'Globo N° 12 por 50 surtidos', 495.00, 650.00, 0, '', '2023-03-01 03:00:00'),
(63, 'Hoja de block EPICA por 96', 0.00, 3000.00, 0, '', '2023-03-01 03:00:00'),
(64, 'Hoja de block Éxito por 96', 0.00, 3675.00, 0, 'Librería', '2023-03-01 03:00:00'),
(65, 'Hoja de block Gloria por 96', 0.00, 2100.00, 0, 'Librería', '2023-03-01 03:00:00'),
(67, 'Hoja de calcar', 630.00, 1100.00, 0, '', '2023-02-15 03:00:00'),
(69, 'Identificador', 55.00, 200.00, 0, '', '2023-02-15 03:00:00'),
(70, 'Inflable martillo', 290.00, 400.00, 0, '', '2023-02-15 03:00:00'),
(71, 'Invisible', 140.00, 200.00, 0, '', '2023-02-15 03:00:00'),
(72, 'Jarra plástica 2 1-4 litro', 260.00, 350.00, 0, '', '2023-02-15 03:00:00'),
(73, 'Jarro con tapa', 480.00, 520.00, 0, '', '2023-02-15 03:00:00'),
(74, 'Juego Ludo en bolsa', 250.00, 320.00, 0, '', '2023-02-15 03:00:00'),
(75, 'Labiales Argentina', 243.00, 320.00, 0, '', '2023-02-15 03:00:00'),
(76, 'lápices cortos de colores por 12', 670.00, 861.00, 0, 'Librería', '2023-02-27 03:00:00'),
(77, 'lápices largos de colores', 120.00, 1470.00, 0, 'Librería', '2023-02-15 03:00:00'),
(78, 'lápiz negro', 80.00, 150.00, 0, '', '2023-02-27 03:00:00'),
(79, 'Libro para pintar-cuentos ', 100.00, 150.00, 0, '', '2023-03-01 03:00:00'),
(80, 'Llavero destapador', 90.00, 150.00, 0, '', '2023-02-15 03:00:00'),
(81, 'Llavero día del padre', 70.00, 120.00, 0, '', '2023-02-15 03:00:00'),
(82, 'Llavero madera', 23.00, 120.00, 0, '', '2023-02-15 03:00:00'),
(84, 'Mapas', 62.00, 105.00, 0, 'Librería', '2023-02-15 03:00:00'),
(85, 'Marcador al agua Bic Negro', 200.00, 1680.00, 0, 'Librería', '2023-02-15 03:00:00'),
(87, 'Marcador Filgo por 6', 120.00, 140.00, 0, '', '2023-02-15 03:00:00'),
(88, 'Marcador lavable Ezco', 190.00, 240.00, 0, '', '2023-02-15 03:00:00'),
(89, 'Marcador permanente', 50.00, 368.00, 0, 'Librería', '2023-02-15 03:00:00'),
(90, 'Cuaderno Gloria 42 Hojas ', 135.00, 2800.00, 0, '', '2023-03-01 03:00:00'),
(91, 'Marcador permanente Filgo', 450.00, 750.00, 0, 'Librería', '2023-02-15 03:00:00'),
(92, 'Mate polímero', 630.00, 800.00, 0, '', '2023-02-15 03:00:00'),
(93, 'Muñeca bolsa', 160.00, 220.00, 0, '', '2023-02-15 03:00:00'),
(94, 'Naipe', 110.00, 150.00, 0, '', '2023-02-15 03:00:00'),
(95, 'Ojalillo', 7.00, 120.00, 0, '', '2023-03-01 03:00:00'),
(96, 'Papel Crepé', 355.00, 650.00, 0, 'Librería', '2023-03-01 03:00:00'),
(97, 'Papel glasé de lustre común', 990.00, 30.00, 0, '', '2023-02-28 03:00:00'),
(98, 'Papel glasé brillose', 217.00, 350.00, 0, 'Librería', '2023-02-28 03:00:00'),
(99, 'Papel de regalo brilloso', 25.00, 40.00, 0, '', '2023-02-15 03:00:00'),
(100, 'Papel de regalo grande', 210.00, 350.00, 0, '', '2023-02-15 03:00:00'),
(101, 'Pelota inflable futbol', 340.00, 430.00, 0, '', '2023-02-15 03:00:00'),
(102, 'Pelota inflable playera', 290.00, 380.00, 0, '', '2023-02-15 03:00:00'),
(103, 'Pelota mascota rugby', 280.00, 400.00, 0, '', '2023-02-15 03:00:00'),
(104, 'Pincel por 6 en bolsa', 170.00, 788.00, 0, 'Librería', '2023-02-15 03:00:00'),
(105, 'Pinta cara Argentina', 243.00, 320.00, 0, '', '2023-02-15 03:00:00'),
(107, 'Pintura Argentina', 180.00, 260.00, 0, '', '2023-02-15 03:00:00'),
(108, 'Pistolita de agua', 30.00, 100.00, 0, '', '2023-02-15 03:00:00'),
(109, 'Plasticola Acuarel 50 g', 72.00, 700.00, 0, '', '2023-03-01 03:00:00'),
(110, 'Porta retrato feliz día', 170.00, 300.00, 0, '', '2023-02-15 03:00:00'),
(111, 'Porta retrato madera', 160.00, 250.00, 0, '', '2023-02-15 03:00:00'),
(113, 'Portaminas', 55.00, 750.00, 0, '', '2023-02-15 03:00:00'),
(115, 'Portavela', 140.00, 200.00, 0, '', '2023-02-15 03:00:00'),
(117, 'Pretal más correa mediana', 350.00, 450.00, 0, '', '2023-02-15 03:00:00'),
(120, 'Regla con forma ', 0.00, 100.00, 0, '', '2023-02-27 03:00:00'),
(121, 'regla flexible corta', 450.00, 620.00, 0, 'Librería', '2023-02-27 03:00:00'),
(122, 'Repuesto 5', 530.00, 1500.00, 0, '', '2023-02-15 03:00:00'),
(125, 'Rueda Gigante inflable negra', 700.00, 950.00, 0, '', '2023-02-15 03:00:00'),
(126, 'sacapuntas metal', 20.00, 399.00, 0, 'Librería', '2023-02-15 03:00:00'),
(127, 'Salvavidas chico', 350.00, 490.00, 0, '', '2023-02-15 03:00:00'),
(128, 'Separadores por 6', 120.00, 190.00, 0, '', '2023-03-01 03:00:00'),
(129, 'Set de geometría por 4 piezas CHICO', 550.00, 750.00, 0, '', '2023-02-15 03:00:00'),
(131, 'Set de mordillos sonajero', 200.00, 280.00, 0, '', '2023-02-15 03:00:00'),
(132, 'Silicona 100 ml.', 450.00, 2205.00, 0, 'Librería', '2023-03-01 03:00:00'),
(133, 'Silicona 30 ml.', 650.00, 980.00, 0, 'Librería', '2023-03-01 03:00:00'),
(134, 'Silicona 60 ml.', 1620.00, 1850.00, 0, '', '2023-03-01 03:00:00'),
(135, 'Sonajero', 180.00, 280.00, 0, '', '2023-02-15 03:00:00'),
(136, 'Tabla periódica', 350.00, 380.00, 0, '', '2023-02-21 03:00:00'),
(138, 'Taza de cerámica', 1700.00, 5429.00, 0, 'Sublimación', '2023-02-15 03:00:00'),
(139, 'Taza polímero asa común', 890.00, 1500.00, 0, '', '2023-02-15 03:00:00'),
(141, 'Tela mandala por 3', 330.00, 330.00, 0, '', '2023-02-15 03:00:00'),
(144, 'Témperas por 6', 280.00, 300.00, 0, '', '2023-02-15 03:00:00'),
(146, 'Tijera maped', 590.00, 1100.00, 0, '', '2023-02-27 03:00:00'),
(147, 'Trincheta', 360.00, 500.00, 0, '', '2023-02-15 03:00:00'),
(149, 'Vaso con pico oso', 510.00, 1100.00, 0, '', '2023-02-15 03:00:00'),
(152, 'Yoyo', 600.00, 800.00, 0, 'Otros', '2023-02-15 03:00:00'),
(154, 'Silicona barra fina', 213.00, 380.00, 0, '', '2023-02-21 03:00:00'),
(155, 'Silicona barra gruesa', 525.00, 650.00, 0, 'Librería', '2023-02-21 03:00:00'),
(156, 'Compás metal', 0.00, 350.00, 0, '', '2023-03-04 14:39:59'),
(157, 'Goma Eva con brillo', 419.00, 1000.00, 0, '', '2023-03-04 14:41:33'),
(159, 'Birome color por 10', 200.00, 1575.00, 0, 'Librería', '2023-03-04 14:43:28'),
(165, 'Hoja N° 6 de Dibujo Miguel Ángel', 370.00, 1600.00, 0, '', '2023-04-14 14:35:28'),
(166, 'Acuarela Filgo por 12', 430.00, 980.00, 0, '', '2023-05-12 13:17:35'),
(167, 'Quitaesmalte ', 180.00, 390.00, 0, '', '2023-05-18 23:56:14'),
(168, 'Bengala ', 118.00, 250.00, 0, 'Otros', '2023-05-18 23:56:35'),
(169, 'Muñeca Mini princesa', 257.00, 400.00, 0, '', '2023-05-18 23:59:40'),
(170, 'Yenga', 350.00, 850.00, 0, '', '2023-05-19 00:00:08'),
(171, 'Cubo Rubik', 220.00, 300.00, 0, '', '2023-05-19 00:00:41'),
(172, 'Vela Boca River corona', 130.00, 250.00, 0, '', '2023-05-19 00:01:03'),
(173, 'Resaltador por 3', 230.00, 1260.00, 0, 'Librería', '2023-06-02 21:24:20'),
(174, 'Témperas por 10 ', 1600.00, 2000.00, 0, 'Librería', '2023-06-02 23:01:56'),
(176, 'Microfibra', 160.00, 240.00, 0, '', '2023-06-15 15:36:11'),
(177, 'Témpera unidad', 150.00, 250.00, 0, '', '2023-06-15 15:36:35'),
(178, 'Borrador azul y rojo ', 200.00, 262.00, 0, 'Librería', '2023-06-15 15:36:54'),
(179, 'Borrador blanco y gris', 50.00, 105.00, 0, 'Librería', '2023-06-15 15:37:11'),
(181, 'Resaltador fino ', 317.00, 504.00, 0, 'Librería', '2023-06-15 15:37:46'),
(182, 'Crayones', 0.00, 220.00, 0, '', '2023-07-01 21:30:30'),
(183, 'Alfiler', 380.00, 1200.00, 0, '', '2023-08-03 15:01:34'),
(184, 'Repuesto N 6 ', 1000.00, 1785.00, 0, 'Librería', '2023-08-17 00:40:16'),
(185, 'Lápices de colores por 6 cortos', 350.00, 504.00, 0, 'Librería', '2023-08-31 14:44:15'),
(186, 'Aro metálico mediano carpeta', 0.00, 750.00, 0, '', '2023-08-31 16:14:46'),
(187, 'Aro metálico grande carpeta', 0.00, 800.00, 0, '', '2023-08-31 16:15:05'),
(188, 'Cinta papel', 1920.00, 2500.00, 0, 'Librería', '2023-10-20 21:53:57'),
(189, 'Regla flexible 30 cm', 550.00, 724.00, 0, 'Librería', '2023-10-20 21:55:12'),
(190, 'Carta truco', 500.00, 600.00, 0, '', '2023-10-20 21:56:01'),
(192, 'Tijera grande', 605.00, 900.00, 0, '', '2023-11-18 21:45:27'),
(193, 'Encendedor corto', 840.00, 980.00, 0, '', '2023-11-18 21:45:46'),
(194, 'crepe', 0.00, 0.00, 0, '', '2023-12-06 15:07:14'),
(195, 'lapices', 0.00, 0.00, 0, '', '2023-12-15 23:35:20'),
(196, 'Fibras por 10 Filgo', 720.00, 950.00, 0, '', '2024-01-20 03:36:13'),
(197, 'Folio A4', 79.00, 126.00, 0, 'Librería', '2024-01-20 03:36:34'),
(199, 'Repuesto N 3', 460.00, 945.00, 0, 'Librería', '2024-01-20 03:40:31'),
(200, 'Voligoma chico', 720.00, 1260.00, 0, 'Librería', '2024-01-20 03:44:42'),
(201, 'Plastilina', 0.00, 147.00, 0, 'Librería', '2024-02-05 13:52:45'),
(202, 'plasticola 30 g', 0.00, 420.00, 0, 'Librería', '2024-02-05 13:53:55'),
(204, 'Regla 30 cm', 0.00, 200.00, 0, '', '2024-02-28 13:32:15'),
(207, 'Regla 30 cm rigida', 0.00, 300.00, 0, '', '2024-02-28 13:37:50'),
(208, 'Palitos de helado color', 0.00, 1200.00, 0, '', '2024-02-29 00:14:27'),
(209, 'Resaltador chato Filgo', 0.00, 580.00, 0, '', '2024-03-09 22:33:41'),
(210, 'Repuesto N° 3 COLOR ', 0.00, 850.00, 0, '', '2024-03-09 22:35:11'),
(211, 'Binocular', 0.00, 1000.00, 0, '', '2024-03-24 00:00:51'),
(212, 'Paleta rosa celeste', 0.00, 900.00, 0, '', '2024-03-24 00:01:17'),
(213, 'Compás', 0.00, 800.00, 0, '', '2024-03-24 00:12:05'),
(214, 'Birome borrable repuesto', 765.00, 1100.00, 0, '', '2024-06-24 15:29:03'),
(215, 'Pañuelos ', 0.00, 900.00, 0, '', '2024-06-24 16:22:35'),
(216, 'Hilo por unidad', 118.00, 250.00, 0, '', '2024-06-24 16:23:19'),
(217, 'Hilo blister', 970.00, 1500.00, 0, '', '2024-06-24 16:23:48'),
(218, 'Cargador Oryx 3.1 A', 1900.00, 2500.00, 0, '', '2024-06-24 16:25:58'),
(219, 'Marcador pizarra', 262.00, 450.00, 0, '', '2024-06-24 16:27:39'),
(220, 'Cubre calzados', 380.00, 800.00, 0, '', '2024-06-24 16:56:37'),
(221, 'Banda elástica por unidad', 8.00, 10.00, 0, '', '2024-06-24 17:12:20'),
(222, 'Tijera 15 cm', 725.00, 1100.00, 0, '', '2024-06-25 16:06:07'),
(223, 'Inflador de globos ', 1020.00, 1500.00, 0, '', '2024-06-25 16:08:05'),
(225, 'Sacapuntas plástico', 48.00, 150.00, 0, '', '2024-06-27 14:25:53'),
(226, 'Regla 15 cm', 125.00, 300.00, 0, '', '2024-06-27 14:26:22'),
(227, 'Repuesto mina', 0.00, 650.00, 0, '', '2024-07-05 22:52:19'),
(228, 'Banderín Feliz cumpleaños', 1260.00, 1800.00, 0, '', '2024-08-01 21:08:36'),
(229, 'Yoyo con luz', 1200.00, 1600.00, 0, '', '2024-08-01 21:37:14'),
(230, 'Balitas  paquete ', 390.00, 700.00, 0, '', '2024-08-01 21:38:20'),
(231, 'Caballito pata pata', 8600.00, 10500.00, 0, '', '2024-08-06 13:19:26'),
(232, 'Hornito', 1700.00, 2500.00, 0, '', '2024-08-06 13:19:43'),
(233, 'Acuarelas', 750.00, 900.00, 0, '', '2024-08-06 13:20:15'),
(234, 'Plasticola Acuarel', 215.00, 480.00, 0, '', '2024-08-06 13:20:39'),
(235, 'Linterna llavero', 340.00, 500.00, 0, '', '2024-08-06 13:21:10'),
(236, 'Fósforo', 1100.00, 1300.00, 0, '', '2024-08-06 13:21:25'),
(237, 'Pelota anti estress', 850.00, 1200.00, 0, '', '2024-08-06 13:21:49'),
(238, 'Repuesto x24 hojas', 950.00, 2000.00, 0, '', '2024-08-06 13:22:21'),
(239, 'Repuesto x48 hojas', 1900.00, 2700.00, 0, 'Librería', '2024-08-06 13:22:58'),
(240, 'Cuaderno x24 hojas', 705.00, 950.00, 0, '', '2024-08-06 13:23:26'),
(244, 'Cartas uno', 1700.00, 2500.00, 0, '', '2024-08-06 13:26:51'),
(245, 'Cartas poker', 770.00, 1700.00, 0, '', '2024-08-06 13:27:25'),
(246, 'Cartas poker 996', 1210.00, 2000.00, 0, '', '2024-08-06 13:27:51'),
(247, 'Alas', 2200.00, 3000.00, 0, '', '2024-08-06 13:28:06'),
(248, 'Banderin feliz cumpl', 1050.00, 1800.00, 0, '', '2024-08-06 13:28:46'),
(249, 'Velas hornito', 1100.00, 2000.00, 0, '', '2024-08-06 13:29:17'),
(250, 'Vaso BOCA', 620.00, 850.00, 0, '', '2024-08-06 13:50:28'),
(251, 'Comedero mediano', 840.00, 950.00, 0, '', '2024-08-06 13:50:57'),
(252, 'Comedero chico ', 740.00, 850.00, 0, '', '2024-08-06 13:51:28'),
(253, 'Pelota chifle', 820.00, 1200.00, 0, '', '2024-08-06 13:55:28'),
(254, 'Pata pata', 5350.00, 8000.00, 0, '', '2024-08-06 15:03:31'),
(255, 'Acrílico', 750.00, 900.00, 5, 'Librería', '2025-11-10 23:08:48'),
(256, 'Compás en blister ', 820.00, 1100.00, 0, '', '2024-08-20 16:52:35'),
(257, 'Adhesivo en barra', 660.00, 800.00, 0, '', '2024-09-27 15:10:55'),
(258, 'Minas 0.5', 770.00, 1000.00, 0, '', '2024-09-27 15:11:50'),
(259, 'Minas 0.7', 290.00, 600.00, 0, '', '2024-09-27 15:12:25'),
(260, 'Cinta pack 40 mm', 0.00, 1800.00, 0, '', '2024-09-27 15:14:06'),
(261, 'Cinta pack ancha ', 0.00, 2600.00, 0, '', '2024-09-27 15:14:26'),
(262, 'Cinta bifaz ', 0.00, 900.00, 0, '', '2024-09-27 15:14:49'),
(263, 'Repuesto x96 hojas ', 0.00, 3500.00, 0, '', '2024-11-11 15:30:58'),
(267, 'Prueba22', 100.00, 200.00, 45, 'Librería', '2025-11-10 23:20:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int NOT NULL,
  `detalle` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `cantidad` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `metodo` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `detalle`, `cantidad`, `precio`, `metodo`, `fecha`) VALUES
(1, 'Cuaderno', 2, 245.20, 'Transferencia', '2024-11-21 16:02:50'),
(2, 'Goma', 3, 2300.10, 'Efectivo', '2024-11-21 21:28:21'),
(3, 'aaa', 2, 1230.10, 'efectivo', '2024-11-21 21:38:24');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `items_factura`
--
ALTER TABLE `items_factura`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_factura` (`id_factura`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item` (`producto`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `items_factura`
--
ALTER TABLE `items_factura`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=268;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `items_factura`
--
ALTER TABLE `items_factura`
  ADD CONSTRAINT `items_factura_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
