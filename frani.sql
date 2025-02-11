-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-02-2025 a las 21:06:14
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

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
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
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
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `detalle` text NOT NULL,
  `metodo` enum('efectivo','tarjeta','transferencia') NOT NULL,
  `total` decimal(10,2) DEFAULT 0.00,
  `fecha` timestamp NULL DEFAULT current_timestamp()
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
  `id` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `detalle` varchar(255) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(133) NOT NULL,
  `producto` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL,
  `categoria` varchar(255) NOT NULL,
  `precio` double NOT NULL,
  `costo` double NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `producto`, `stock`, `categoria`, `precio`, `costo`, `fecha`) VALUES
(1, 'Afiche', 20, 'Librería', 850, 715, '2025-02-02 21:24:41'),
(4, 'Aro metálico chico carpeta', 0, 'Librería', 735, 31, '2023-02-21 03:00:00'),
(5, 'Auricular', 0, 'Otros', 1200, 120, '2023-02-13 03:00:00'),
(6, 'Cuaderno 48 hojas', 0, '', 980, 80, '2023-02-13 03:00:00'),
(7, 'Birome borrable', 0, '', 1700, 1470, '2023-02-13 03:00:00'),
(10, 'Birome Bic azul-negra-roja', 0, 'Librería', 750, 0, '2023-03-01 03:00:00'),
(13, 'Birome cuatro colores retractil', 0, '', 290, 146, '2023-03-01 03:00:00'),
(17, 'Brillantina', 0, 'Librería', 350, 185, '2023-02-15 03:00:00'),
(23, 'Carpeta escolar para anillo ', 0, '', 480, 368, '2023-03-01 03:00:00'),
(25, 'Carpeta Número 5 con dibujos', 0, '', 2100, 300, '2023-02-15 03:00:00'),
(26, 'Carpeta tapa transparente A4', 0, '', 190, 100, '2023-02-15 03:00:00'),
(28, 'auricular River Boca', 0, 'Otros', 1400, 615, '2023-02-27 03:00:00'),
(33, 'Cinta pack mediana', 0, '', 1700, 300, '2023-02-21 03:00:00'),
(34, 'Cinta scocht chica', 0, 'Librería', 158, 70, '2023-03-01 03:00:00'),
(38, 'Collar con tachas 57 cm', 0, '', 400, 280, '2023-02-15 03:00:00'),
(39, 'Collar cuero con puas', 0, '', 500, 380, '2023-02-15 03:00:00'),
(41, 'Comedero 22 cm mediano', 0, '', 370, 300, '2023-02-15 03:00:00'),
(42, 'Copos gomaespuma', 0, '', 550, 550, '2023-02-15 03:00:00'),
(43, 'Corta uñas grande', 0, '', 690, 600, '2023-03-01 03:00:00'),
(45, 'Correa más collar fino', 0, '', 300, 120, '2023-02-15 03:00:00'),
(46, 'Corrector chico', 0, '', 80, 40, '2023-02-21 03:00:00'),
(47, 'Corrector grande', 0, 'Librería', 630, 370, '2023-02-21 03:00:00'),
(48, 'Corta uñas infantil', 0, '', 200, 100, '2023-02-15 03:00:00'),
(49, 'Cuadernillo América', 0, '', 750, 495, '2023-02-15 03:00:00'),
(50, 'Cuaderno 24 hojas', 0, 'Librería', 900, 830, '2023-02-21 03:00:00'),
(51, 'cuaderno 84 hojas', 0, 'Librería', 1050, 96, '2023-02-15 03:00:00'),
(52, 'Cuaderno Rivadavia 98 hojas', 0, '', 1900, 0, '2023-02-24 03:00:00'),
(53, 'Cuchara', 0, '', 120, 50, '2023-02-15 03:00:00'),
(54, 'Encendedor', 0, 'Otros', 380, 55, '2023-02-15 03:00:00'),
(55, 'Esmalte duo argentina', 0, '', 320, 243, '2023-02-15 03:00:00'),
(56, 'Etiquetas por 5', 0, '', 25, 9, '2023-02-15 03:00:00'),
(57, 'Fibras por 6 Filgo', 0, 'Librería', 714, 0, '2023-02-27 03:00:00'),
(58, 'fibras por 12', 0, '', 800, 100, '2023-02-15 03:00:00'),
(59, 'Folio tamaño carpeta N° 3', 0, 'Librería', 105, 10, '2023-02-15 03:00:00'),
(60, 'Forro araña azul', 0, '', 250, 220, '2023-02-21 03:00:00'),
(61, 'Goma Eva', 0, '', 680, 590, '2023-02-27 03:00:00'),
(62, 'Globo N° 12 por 50 surtidos', 0, '', 650, 495, '2023-03-01 03:00:00'),
(63, 'Hoja de block EPICA por 96', 0, '', 3000, 0, '2023-03-01 03:00:00'),
(64, 'Hoja de block Éxito por 96', 0, 'Librería', 3675, 0, '2023-03-01 03:00:00'),
(65, 'Hoja de block Gloria por 96', 0, 'Librería', 2100, 0, '2023-03-01 03:00:00'),
(67, 'Hoja de calcar', 0, '', 1100, 630, '2023-02-15 03:00:00'),
(69, 'Identificador', 0, '', 200, 55, '2023-02-15 03:00:00'),
(70, 'Inflable martillo', 0, '', 400, 290, '2023-02-15 03:00:00'),
(71, 'Invisible', 0, '', 200, 140, '2023-02-15 03:00:00'),
(72, 'Jarra plástica 2 1-4 litro', 0, '', 350, 260, '2023-02-15 03:00:00'),
(73, 'Jarro con tapa', 0, '', 520, 480, '2023-02-15 03:00:00'),
(74, 'Juego Ludo en bolsa', 0, '', 320, 250, '2023-02-15 03:00:00'),
(75, 'Labiales Argentina', 0, '', 320, 243, '2023-02-15 03:00:00'),
(76, 'lápices cortos de colores por 12', 0, 'Librería', 861, 670, '2023-02-27 03:00:00'),
(77, 'lápices largos de colores', 0, 'Librería', 1470, 120, '2023-02-15 03:00:00'),
(78, 'lápiz negro', 0, '', 150, 80, '2023-02-27 03:00:00'),
(79, 'Libro para pintar-cuentos ', 0, '', 150, 100, '2023-03-01 03:00:00'),
(80, 'Llavero destapador', 0, '', 150, 90, '2023-02-15 03:00:00'),
(81, 'Llavero día del padre', 0, '', 120, 70, '2023-02-15 03:00:00'),
(82, 'Llavero madera', 0, '', 120, 23, '2023-02-15 03:00:00'),
(84, 'Mapas', 0, 'Librería', 105, 62, '2023-02-15 03:00:00'),
(85, 'Marcador al agua Bic Negro', 0, 'Librería', 1680, 200, '2023-02-15 03:00:00'),
(87, 'Marcador Filgo por 6', 0, '', 140, 120, '2023-02-15 03:00:00'),
(88, 'Marcador lavable Ezco', 0, '', 240, 190, '2023-02-15 03:00:00'),
(89, 'Marcador permanente', 0, 'Librería', 368, 50, '2023-02-15 03:00:00'),
(90, 'Cuaderno Gloria 42 Hojas ', 0, '', 2800, 135, '2023-03-01 03:00:00'),
(91, 'Marcador permanente Filgo', 0, 'Librería', 750, 450, '2023-02-15 03:00:00'),
(92, 'Mate polímero', 0, '', 800, 630, '2023-02-15 03:00:00'),
(93, 'Muñeca bolsa', 0, '', 220, 160, '2023-02-15 03:00:00'),
(94, 'Naipe', 0, '', 150, 110, '2023-02-15 03:00:00'),
(95, 'Ojalillo', 0, '', 120, 7, '2023-03-01 03:00:00'),
(96, 'Papel Crepé', 0, 'Librería', 650, 355, '2023-03-01 03:00:00'),
(97, 'Papel glasé de lustre común', 0, '', 30, 990, '2023-02-28 03:00:00'),
(98, 'Papel glasé brillose', 0, 'Librería', 350, 217, '2023-02-28 03:00:00'),
(99, 'Papel de regalo brilloso', 0, '', 40, 25, '2023-02-15 03:00:00'),
(100, 'Papel de regalo grande', 0, '', 350, 210, '2023-02-15 03:00:00'),
(101, 'Pelota inflable futbol', 0, '', 430, 340, '2023-02-15 03:00:00'),
(102, 'Pelota inflable playera', 0, '', 380, 290, '2023-02-15 03:00:00'),
(103, 'Pelota mascota rugby', 0, '', 400, 280, '2023-02-15 03:00:00'),
(104, 'Pincel por 6 en bolsa', 0, 'Librería', 788, 170, '2023-02-15 03:00:00'),
(105, 'Pinta cara Argentina', 0, '', 320, 243, '2023-02-15 03:00:00'),
(107, 'Pintura Argentina', 0, '', 260, 180, '2023-02-15 03:00:00'),
(108, 'Pistolita de agua', 0, '', 100, 30, '2023-02-15 03:00:00'),
(109, 'Plasticola Acuarel 50 g', 0, '', 700, 72, '2023-03-01 03:00:00'),
(110, 'Porta retrato feliz día', 0, '', 300, 170, '2023-02-15 03:00:00'),
(111, 'Porta retrato madera', 0, '', 250, 160, '2023-02-15 03:00:00'),
(113, 'Portaminas', 0, '', 750, 55, '2023-02-15 03:00:00'),
(115, 'Portavela', 0, '', 200, 140, '2023-02-15 03:00:00'),
(117, 'Pretal más correa mediana', 0, '', 450, 350, '2023-02-15 03:00:00'),
(120, 'Regla con forma ', 0, '', 100, 0, '2023-02-27 03:00:00'),
(121, 'regla flexible corta', 0, 'Librería', 620, 450, '2023-02-27 03:00:00'),
(122, 'Repuesto 5', 0, '', 1500, 530, '2023-02-15 03:00:00'),
(125, 'Rueda Gigante inflable negra', 0, '', 950, 700, '2023-02-15 03:00:00'),
(126, 'sacapuntas metal', 0, 'Librería', 399, 20, '2023-02-15 03:00:00'),
(127, 'Salvavidas chico', 0, '', 490, 350, '2023-02-15 03:00:00'),
(128, 'Separadores por 6', 0, '', 190, 120, '2023-03-01 03:00:00'),
(129, 'Set de geometría por 4 piezas CHICO', 0, '', 750, 550, '2023-02-15 03:00:00'),
(131, 'Set de mordillos sonajero', 0, '', 280, 200, '2023-02-15 03:00:00'),
(132, 'Silicona 100 ml.', 0, 'Librería', 2205, 450, '2023-03-01 03:00:00'),
(133, 'Silicona 30 ml.', 0, 'Librería', 980, 650, '2023-03-01 03:00:00'),
(134, 'Silicona 60 ml.', 0, '', 1850, 1620, '2023-03-01 03:00:00'),
(135, 'Sonajero', 0, '', 280, 180, '2023-02-15 03:00:00'),
(136, 'Tabla periódica', 0, '', 380, 350, '2023-02-21 03:00:00'),
(138, 'Taza de cerámica', 0, 'Sublimación', 5429, 1700, '2023-02-15 03:00:00'),
(139, 'Taza polímero asa común', 0, '', 1500, 890, '2023-02-15 03:00:00'),
(141, 'Tela mandala por 3', 0, '', 330, 330, '2023-02-15 03:00:00'),
(144, 'Témperas por 6', 0, '', 300, 280, '2023-02-15 03:00:00'),
(146, 'Tijera maped', 0, '', 1100, 590, '2023-02-27 03:00:00'),
(147, 'Trincheta', 0, '', 500, 360, '2023-02-15 03:00:00'),
(149, 'Vaso con pico oso', 0, '', 1100, 510, '2023-02-15 03:00:00'),
(152, 'Yoyo', 0, 'Otros', 800, 600, '2023-02-15 03:00:00'),
(154, 'Silicona barra fina', 0, '', 380, 213, '2023-02-21 03:00:00'),
(155, 'Silicona barra gruesa', 0, 'Librería', 650, 525, '2023-02-21 03:00:00'),
(156, 'Compás metal', 0, '', 350, 0, '2023-03-04 14:39:59'),
(157, 'Goma Eva con brillo', 0, '', 1000, 419, '2023-03-04 14:41:33'),
(159, 'Birome color por 10', 0, 'Librería', 1575, 200, '2023-03-04 14:43:28'),
(165, 'Hoja N° 6 de Dibujo Miguel Ángel', 0, '', 1600, 370, '2023-04-14 14:35:28'),
(166, 'Acuarela Filgo por 12', 0, '', 980, 430, '2023-05-12 13:17:35'),
(167, 'Quitaesmalte ', 0, '', 390, 180, '2023-05-18 23:56:14'),
(168, 'Bengala ', 0, 'Otros', 250, 118, '2023-05-18 23:56:35'),
(169, 'Muñeca Mini princesa', 0, '', 400, 257, '2023-05-18 23:59:40'),
(170, 'Yenga', 0, '', 850, 350, '2023-05-19 00:00:08'),
(171, 'Cubo Rubik', 0, '', 300, 220, '2023-05-19 00:00:41'),
(172, 'Vela Boca River corona', 0, '', 250, 130, '2023-05-19 00:01:03'),
(173, 'Resaltador por 3', 0, 'Librería', 1260, 230, '2023-06-02 21:24:20'),
(174, 'Témperas por 10 ', 0, 'Librería', 2000, 1600, '2023-06-02 23:01:56'),
(176, 'Microfibra', 0, '', 240, 160, '2023-06-15 15:36:11'),
(177, 'Témpera unidad', 0, '', 250, 150, '2023-06-15 15:36:35'),
(178, 'Borrador azul y rojo ', 0, 'Librería', 262, 200, '2023-06-15 15:36:54'),
(179, 'Borrador blanco y gris', 0, 'Librería', 105, 50, '2023-06-15 15:37:11'),
(181, 'Resaltador fino ', 0, 'Librería', 504, 317, '2023-06-15 15:37:46'),
(182, 'Crayones', 0, '', 220, 0, '2023-07-01 21:30:30'),
(183, 'Alfiler', 0, '', 1200, 380, '2023-08-03 15:01:34'),
(184, 'Repuesto N 6 ', 0, 'Librería', 1785, 1000, '2023-08-17 00:40:16'),
(185, 'Lápices de colores por 6 cortos', 0, 'Librería', 504, 350, '2023-08-31 14:44:15'),
(186, 'Aro metálico mediano carpeta', 0, '', 750, 0, '2023-08-31 16:14:46'),
(187, 'Aro metálico grande carpeta', 0, '', 800, 0, '2023-08-31 16:15:05'),
(188, 'Cinta papel', 0, 'Librería', 2500, 1920, '2023-10-20 21:53:57'),
(189, 'Regla flexible 30 cm', 0, 'Librería', 724, 550, '2023-10-20 21:55:12'),
(190, 'Carta truco', 0, '', 600, 500, '2023-10-20 21:56:01'),
(192, 'Tijera grande', 0, '', 900, 605, '2023-11-18 21:45:27'),
(193, 'Encendedor corto', 0, '', 980, 840, '2023-11-18 21:45:46'),
(194, 'crepe', 0, '', 0, 0, '2023-12-06 15:07:14'),
(195, 'lapices', 0, '', 0, 0, '2023-12-15 23:35:20'),
(196, 'Fibras por 10 Filgo', 0, '', 950, 720, '2024-01-20 03:36:13'),
(197, 'Folio A4', 0, 'Librería', 126, 79, '2024-01-20 03:36:34'),
(199, 'Repuesto N 3', 0, 'Librería', 945, 460, '2024-01-20 03:40:31'),
(200, 'Voligoma chico', 0, 'Librería', 1260, 720, '2024-01-20 03:44:42'),
(201, 'Plastilina', 0, 'Librería', 147, 0, '2024-02-05 13:52:45'),
(202, 'plasticola 30 g', 0, 'Librería', 420, 0, '2024-02-05 13:53:55'),
(204, 'Regla 30 cm', 0, '', 200, 0, '2024-02-28 13:32:15'),
(207, 'Regla 30 cm rigida', 0, '', 300, 0, '2024-02-28 13:37:50'),
(208, 'Palitos de helado color', 0, '', 1200, 0, '2024-02-29 00:14:27'),
(209, 'Resaltador chato Filgo', 0, '', 580, 0, '2024-03-09 22:33:41'),
(210, 'Repuesto N° 3 COLOR ', 0, '', 850, 0, '2024-03-09 22:35:11'),
(211, 'Binocular', 0, '', 1000, 0, '2024-03-24 00:00:51'),
(212, 'Paleta rosa celeste', 0, '', 900, 0, '2024-03-24 00:01:17'),
(213, 'Compás', 0, '', 800, 0, '2024-03-24 00:12:05'),
(214, 'Birome borrable repuesto', 0, '', 1100, 765, '2024-06-24 15:29:03'),
(215, 'Pañuelos ', 0, '', 900, 0, '2024-06-24 16:22:35'),
(216, 'Hilo por unidad', 0, '', 250, 118, '2024-06-24 16:23:19'),
(217, 'Hilo blister', 0, '', 1500, 970, '2024-06-24 16:23:48'),
(218, 'Cargador Oryx 3.1 A', 0, '', 2500, 1900, '2024-06-24 16:25:58'),
(219, 'Marcador pizarra', 0, '', 450, 262, '2024-06-24 16:27:39'),
(220, 'Cubre calzados', 0, '', 800, 380, '2024-06-24 16:56:37'),
(221, 'Banda elástica por unidad', 0, '', 10, 8, '2024-06-24 17:12:20'),
(222, 'Tijera 15 cm', 0, '', 1100, 725, '2024-06-25 16:06:07'),
(223, 'Inflador de globos ', 0, '', 1500, 1020, '2024-06-25 16:08:05'),
(225, 'Sacapuntas plástico', 0, '', 150, 48, '2024-06-27 14:25:53'),
(226, 'Regla 15 cm', 0, '', 300, 125, '2024-06-27 14:26:22'),
(227, 'Repuesto mina', 0, '', 650, 0, '2024-07-05 22:52:19'),
(228, 'Banderín Feliz cumpleaños', 0, '', 1800, 1260, '2024-08-01 21:08:36'),
(229, 'Yoyo con luz', 0, '', 1600, 1200, '2024-08-01 21:37:14'),
(230, 'Balitas  paquete ', 0, '', 700, 390, '2024-08-01 21:38:20'),
(231, 'Caballito pata pata', 0, '', 10500, 8600, '2024-08-06 13:19:26'),
(232, 'Hornito', 0, '', 2500, 1700, '2024-08-06 13:19:43'),
(233, 'Acuarelas', 0, '', 900, 750, '2024-08-06 13:20:15'),
(234, 'Plasticola Acuarel', 0, '', 480, 215, '2024-08-06 13:20:39'),
(235, 'Linterna llavero', 0, '', 500, 340, '2024-08-06 13:21:10'),
(236, 'Fósforo', 0, '', 1300, 1100, '2024-08-06 13:21:25'),
(237, 'Pelota anti estress', 0, '', 1200, 850, '2024-08-06 13:21:49'),
(238, 'Repuesto x24 hojas', 0, '', 2000, 950, '2024-08-06 13:22:21'),
(239, 'Repuesto x48 hojas', 0, 'Librería', 2700, 1900, '2024-08-06 13:22:58'),
(240, 'Cuaderno x24 hojas', 0, '', 950, 705, '2024-08-06 13:23:26'),
(244, 'Cartas uno', 0, '', 2500, 1700, '2024-08-06 13:26:51'),
(245, 'Cartas poker', 0, '', 1700, 770, '2024-08-06 13:27:25'),
(246, 'Cartas poker 996', 0, '', 2000, 1210, '2024-08-06 13:27:51'),
(247, 'Alas', 0, '', 3000, 2200, '2024-08-06 13:28:06'),
(248, 'Banderin feliz cumpl', 0, '', 1800, 1050, '2024-08-06 13:28:46'),
(249, 'Velas hornito', 0, '', 2000, 1100, '2024-08-06 13:29:17'),
(250, 'Vaso BOCA', 0, '', 850, 620, '2024-08-06 13:50:28'),
(251, 'Comedero mediano', 0, '', 950, 840, '2024-08-06 13:50:57'),
(252, 'Comedero chico ', 0, '', 850, 740, '2024-08-06 13:51:28'),
(253, 'Pelota chifle', 0, '', 1200, 820, '2024-08-06 13:55:28'),
(254, 'Pata pata', 0, '', 8000, 5350, '2024-08-06 15:03:31'),
(255, 'Acrílico', 0, '', 900, 750, '2024-08-20 16:48:05'),
(256, 'Compás en blister ', 0, '', 1100, 820, '2024-08-20 16:52:35'),
(257, 'Adhesivo en barra', 0, '', 800, 660, '2024-09-27 15:10:55'),
(258, 'Minas 0.5', 0, '', 1000, 770, '2024-09-27 15:11:50'),
(259, 'Minas 0.7', 0, '', 600, 290, '2024-09-27 15:12:25'),
(260, 'Cinta pack 40 mm', 0, '', 1800, 0, '2024-09-27 15:14:06'),
(261, 'Cinta pack ancha ', 0, '', 2600, 0, '2024-09-27 15:14:26'),
(262, 'Cinta bifaz ', 0, '', 900, 0, '2024-09-27 15:14:49'),
(263, 'Repuesto x96 hojas ', 0, '', 3500, 0, '2024-11-11 15:30:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos2`
--

CREATE TABLE `productos2` (
  `id` int(11) NOT NULL,
  `producto` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `categoria` varchar(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `productos2`
--

INSERT INTO `productos2` (`id`, `producto`, `stock`, `categoria`, `precio`, `costo`, `fecha`) VALUES
(3, 'Aro argolla chico', 0, '', 180.00, 120.00, '2023-02-18'),
(4, 'Aro metálico chico carpeta', 0, '', 350.00, 31.00, '2023-02-21'),
(5, 'Auricular', 0, '', 200.00, 120.00, '2023-02-13'),
(6, 'Cuaderno 48 hojas', 0, '', 250.00, 80.00, '2023-02-13'),
(7, 'Birome borrable', 0, '', 580.00, 520.00, '2023-02-13'),
(8, 'Birome color común', 0, '', 70.00, 23.00, '2023-03-01'),
(9, 'Birome común azul ABC', 0, '', 50.00, 30.00, '2023-02-15'),
(10, 'Birome Bic azul-negra-roja', 0, '', 140.00, 0.00, '2023-03-01'),
(11, 'Birome Bic color', 0, '', 170.00, 105.00, '2023-03-01'),
(12, 'Birome con gel por 5', 0, '', 650.00, 0.00, '2023-03-01'),
(13, 'Birome cuatro colores retractil', 0, '', 290.00, 146.00, '2023-03-01'),
(14, 'Bolsa de regalo Kraft', 0, '', 70.00, 46.00, '2023-02-15'),
(15, 'Borrador', 0, '', 70.00, 50.00, '2023-02-21'),
(16, 'Botella mancuerda', 0, '', 450.00, 950.00, '2023-02-15'),
(17, 'Brillantina', 0, '', 50.00, 34.00, '2023-02-15'),
(21, 'Candado para mochila', 0, '', 150.00, 50.00, '2023-02-15'),
(22, 'Cargador', 0, '', 550.00, 430.00, '2023-02-15'),
(23, 'Carpeta escolar para anillo ', 0, '', 480.00, 368.00, '2023-03-01'),
(24, 'Carpeta Número 5', 0, '', 250.00, 0.00, '2023-02-13'),
(25, 'Carpeta Número 5 con dibujos', 0, '', 480.00, 300.00, '2023-02-15'),
(26, 'Carpeta tapa transparente A4', 0, '', 190.00, 100.00, '2023-02-15'),
(27, 'Cartuchera', 0, '', 80.00, 50.00, '2023-02-15'),
(28, 'auricular River Boca', 0, '', 800.00, 615.00, '2023-02-27'),
(29, 'Cartulina ', 0, '', 260.00, 115.00, '2023-02-27'),
(32, 'Cinta pack ancha', 0, '', 750.00, 450.00, '2023-02-21'),
(33, 'Cinta pack mediana', 0, '', 600.00, 300.00, '2023-02-21'),
(34, 'Cinta scocht chica', 0, '', 100.00, 70.00, '2023-03-01'),
(35, 'Collar chico', 0, '', 250.00, 180.00, '2023-02-15'),
(36, 'Collar con pañuelo', 0, '', 350.00, 260.00, '2023-02-15'),
(37, 'Collar con tachas 47 cm.', 0, '', 400.00, 230.00, '2023-02-15'),
(38, 'Collar con tachas 57 cm', 0, '', 400.00, 280.00, '2023-02-15'),
(39, 'Collar cuero con puas', 0, '', 500.00, 380.00, '2023-02-15'),
(40, 'Collar y correa grueso', 0, '', 800.00, 450.00, '2023-02-15'),
(41, 'Comedero 22 cm mediano', 0, '', 370.00, 300.00, '2023-02-15'),
(42, 'Copos gomaespuma', 0, '', 550.00, 550.00, '2023-02-15'),
(43, 'Corta uñas grande', 0, '', 690.00, 600.00, '2023-03-01'),
(44, 'Correa cuerito fina', 0, '', 350.00, 270.00, '2023-02-15'),
(45, 'Correa más collar fino', 0, '', 300.00, 120.00, '2023-02-15'),
(46, 'Corrector chico', 0, '', 80.00, 40.00, '2023-02-21'),
(47, 'Corrector grande', 0, '', 400.00, 370.00, '2023-02-21'),
(48, 'Corta uñas infantil', 0, '', 200.00, 100.00, '2023-02-15'),
(49, 'Cuadernillo América', 0, '', 750.00, 495.00, '2023-02-15'),
(50, 'Cuaderno 24 hojas', 0, '', 300.00, 260.00, '2023-02-21'),
(51, 'cuaderno Gloria 84 hojas', 0, '', 250.00, 96.00, '2023-02-15'),
(52, 'Cuaderno Rivadavia 98 hojas', 0, '', 1900.00, 0.00, '2023-02-24'),
(53, 'Cuchara', 0, '', 120.00, 50.00, '2023-02-15'),
(54, 'Encendedor', 0, '', 100.00, 55.00, '2023-02-15'),
(55, 'Esmalte duo argentina', 0, '', 320.00, 243.00, '2023-02-15'),
(56, 'Etiquetas por 5', 0, '', 25.00, 9.00, '2023-02-15'),
(57, 'Fibras por 6 Filgo', 0, '', 400.00, 0.00, '2023-02-27'),
(58, 'fibras por 12', 0, '', 800.00, 100.00, '2023-02-15'),
(59, 'Folio tamaño carpeta N° 3', 0, '', 30.00, 10.00, '2023-02-15'),
(60, 'Forro araña azul', 0, '', 250.00, 220.00, '2023-02-21'),
(61, 'Goma Eva', 0, '', 280.00, 160.00, '2023-02-27'),
(62, 'Globo N° 12 por 50 surtidos', 0, '', 650.00, 495.00, '2023-03-01'),
(63, 'Hoja de block EPICA por 96', 0, '', 650.00, 0.00, '2023-03-01'),
(64, 'Hoja de block Éxito por 96', 0, '', 2000.00, 0.00, '2023-03-01'),
(65, 'Hoja de block Gloria por 96', 0, '', 650.00, 0.00, '2023-03-01'),
(66, 'Hoja canson color', 0, '', 200.00, 60.00, '2023-02-27'),
(67, 'Hoja de calcar', 0, '', 320.00, 260.00, '2023-02-15'),
(68, 'Hoja N° 3 de dibujo ', 0, '', 130.00, 0.00, '2023-03-01'),
(69, 'Identificador', 0, '', 200.00, 55.00, '2023-02-15'),
(70, 'Inflable martillo', 0, '', 400.00, 290.00, '2023-02-15'),
(71, 'Invisible', 0, '', 200.00, 140.00, '2023-02-15'),
(72, 'Jarra plástica 2 1-4 litro', 0, '', 350.00, 260.00, '2023-02-15'),
(73, 'Jarro con tapa', 0, '', 520.00, 480.00, '2023-02-15'),
(74, 'Juego Ludo en bolsa', 0, '', 320.00, 250.00, '2023-02-15'),
(75, 'Labiales Argentina', 0, '', 320.00, 243.00, '2023-02-15'),
(76, 'lápices cortos de colores por 12', 0, '', 480.00, 90.00, '2023-02-27'),
(77, 'lápices largos de colores', 0, '', 350.00, 120.00, '2023-02-15'),
(78, 'lápiz negro', 0, '', 70.00, 24.00, '2023-02-27'),
(79, 'Libro para pintar-cuentos ', 0, '', 150.00, 100.00, '2023-03-01'),
(80, 'Llavero destapador', 0, '', 150.00, 90.00, '2023-02-15'),
(81, 'Llavero día del padre', 0, '', 120.00, 70.00, '2023-02-15'),
(82, 'Llavero madera', 0, '', 120.00, 23.00, '2023-02-15'),
(84, 'Mapas', 0, '', 20.00, 0.00, '2023-02-15'),
(85, 'Marcador al agua Bic Negro', 0, '', 230.00, 200.00, '2023-02-15'),
(86, 'Marcador al agua Bic Rojo', 0, '', 200.00, 177.00, '2023-02-15'),
(87, 'Marcador Filgo por 6', 0, '', 140.00, 120.00, '2023-02-15'),
(88, 'Marcador lavable Ezco', 0, '', 240.00, 190.00, '2023-02-15'),
(89, 'Marcador permanente', 0, '', 80.00, 50.00, '2023-02-15'),
(90, 'Marcador Filgo al agua', 0, '', 200.00, 135.00, '2023-03-01'),
(91, 'Marcador permanente Filgo', 0, '', 280.00, 230.00, '2023-02-15'),
(92, 'Mate polímero', 0, '', 800.00, 630.00, '2023-02-15'),
(93, 'Muñeca bolsa', 0, '', 220.00, 160.00, '2023-02-15'),
(94, 'Naipe', 0, '', 150.00, 110.00, '2023-02-15'),
(95, 'Ojalillo', 0, '', 20.00, 7.00, '2023-03-01'),
(96, 'Papel Crepé', 0, '', 320.00, 150.00, '2023-03-01'),
(97, 'Papel glasé de lustre común', 0, '', 200.00, 0.00, '2023-02-28'),
(98, 'Papel glasé brillose', 0, '', 80.00, 0.00, '2023-02-28'),
(99, 'Papel de regalo brilloso', 0, '', 40.00, 25.00, '2023-02-15'),
(100, 'Papel de regalo grande', 0, '', 350.00, 210.00, '2023-02-15'),
(101, 'Pelota inflable futbol', 0, '', 430.00, 340.00, '2023-02-15'),
(102, 'Pelota inflable playera', 0, '', 380.00, 290.00, '2023-02-15'),
(103, 'Pelota mascota rugby', 0, '', 400.00, 280.00, '2023-02-15'),
(104, 'Pincel por 6 en bolsa', 0, '', 300.00, 170.00, '2023-02-15'),
(105, 'Pinta cara Argentina', 0, '', 320.00, 243.00, '2023-02-15'),
(106, 'Pinta cara Argentina blister', 0, '', 260.00, 138.00, '2023-02-15'),
(107, 'Pintura Argentina', 0, '', 260.00, 180.00, '2023-02-15'),
(108, 'Pistolita de agua', 0, '', 100.00, 30.00, '2023-02-15'),
(109, 'Plasticola Acuarel 50 g', 0, '', 120.00, 72.00, '2023-03-01'),
(110, 'Porta retrato feliz día', 0, '', 300.00, 170.00, '2023-02-15'),
(111, 'Porta retrato madera', 0, '', 250.00, 160.00, '2023-02-15'),
(112, 'Porta SUBE', 0, '', 100.00, 75.00, '2023-02-15'),
(113, 'Portaminas', 0, '', 130.00, 55.00, '2023-02-15'),
(114, 'Porta SUBE 2', 0, '', 250.00, 180.00, '2023-03-01'),
(115, 'Portavela', 0, '', 200.00, 140.00, '2023-02-15'),
(116, 'Pretal más correa chica', 0, '', 250.00, 350.00, '2023-02-15'),
(117, 'Pretal más correa mediana', 0, '', 450.00, 350.00, '2023-02-15'),
(120, 'Regla con forma ', 0, '', 100.00, 0.00, '2023-02-27'),
(121, 'regla flexible corta', 0, '', 250.00, 40.00, '2023-02-27'),
(122, 'Repuesto 5', 0, '', 300.00, 270.00, '2023-02-15'),
(123, 'Repuesto 6 El Nene', 0, '', 240.00, 180.00, '2023-02-15'),
(124, 'Repuesto 6 Miguel Angel', 0, '', 130.00, 70.00, '2023-02-15'),
(125, 'Rueda Gigante inflable negra', 0, '', 950.00, 700.00, '2023-02-15'),
(126, 'sacapuntas', 0, '', 40.00, 20.00, '2023-02-15'),
(127, 'Salvavidas chico', 0, '', 490.00, 350.00, '2023-02-15'),
(128, 'Separadores por 6', 0, '', 190.00, 120.00, '2023-03-01'),
(129, 'Set de geometría por 4 piezas', 0, '', 350.00, 70.00, '2023-02-15'),
(130, 'Set de mate día del padre', 0, '', 800.00, 550.00, '2023-02-15'),
(131, 'Set de mordillos sonajero', 0, '', 280.00, 200.00, '2023-02-15'),
(132, 'Silicona 100 ml.', 0, '', 1400.00, 450.00, '2023-03-01'),
(133, 'Silicona 30 ml.', 0, '', 400.00, 353.00, '2023-03-01'),
(134, 'Silicona 60 ml.', 0, '', 450.00, 345.00, '2023-03-01'),
(135, 'Sonajero', 0, '', 280.00, 180.00, '2023-02-15'),
(136, 'Tabla periódica', 0, '', 380.00, 350.00, '2023-02-21'),
(137, 'Taper jardín', 0, '', 400.00, 224.00, '2023-02-15'),
(138, 'Taza de cerámica', 0, '', 950.00, 570.00, '2023-02-15'),
(139, 'Taza polímero asa común', 0, '', 500.00, 250.00, '2023-02-15'),
(140, 'Taza polímero asa de corazón', 0, '', 500.00, 250.00, '2023-02-15'),
(141, 'Tela mandala por 3', 0, '', 330.00, 330.00, '2023-02-15'),
(142, 'Tela provenzal para cucha por metro', 0, '', 760.00, 460.00, '2023-02-15'),
(143, 'Tela tropical mecánico por metro', 0, '', 900.00, 700.00, '2023-02-15'),
(144, 'Témperas por 6', 0, '', 300.00, 280.00, '2023-02-15'),
(145, 'Tijera chica', 0, '', 60.00, 0.00, '2023-02-27'),
(146, 'Tijera maped', 0, '', 250.00, 0.00, '2023-02-27'),
(147, 'Trincheta', 0, '', 380.00, 315.00, '2023-02-15'),
(148, 'Vaso con pico', 0, '', 950.00, 490.00, '2023-02-15'),
(149, 'Vaso con pico oso', 0, '', 1100.00, 510.00, '2023-02-15'),
(151, 'Voligoma grande', 0, '', 480.00, 250.00, '2023-03-01'),
(152, 'Yoyo', 0, '', 200.00, 120.00, '2023-02-15'),
(154, 'Silicona barra fina', 0, '', 150.00, 45.00, '2023-02-21'),
(155, 'Silicona barra gruesa', 0, '', 190.00, 110.00, '2023-02-21'),
(156, 'Compás metal', 0, '', 350.00, 0.00, '2023-03-04'),
(157, 'Goma Eva con brillo', 0, '', 450.00, 419.00, '2023-03-04'),
(158, 'Birome color por 6', 0, '', 300.00, 0.00, '2023-03-04'),
(159, 'Birome color por 10', 0, '', 600.00, 200.00, '2023-03-04'),
(165, 'Hoja N° 6 de Dibujo Miguel Ángel', 0, '', 600.00, 370.00, '2023-04-14'),
(167, 'Quitaesmalte ', 0, '', 390.00, 180.00, '2023-05-18'),
(168, 'Bengala ', 0, '', 150.00, 118.00, '2023-05-18'),
(169, 'Muñeca Mini princesa', 0, '', 400.00, 257.00, '2023-05-18'),
(170, 'Yenga', 0, '', 850.00, 350.00, '2023-05-18'),
(171, 'Cubo Rubik', 0, '', 300.00, 220.00, '2023-05-18'),
(172, 'Vela Boca River corona', 0, '', 250.00, 130.00, '2023-05-18'),
(173, 'Resaltador por 3', 0, '', 800.00, 230.00, '2023-06-02'),
(174, 'Témperas por 10 ', 0, '', 600.00, 510.00, '2023-06-02'),
(176, 'Microfibra', 0, '', 240.00, 160.00, '2023-06-15'),
(177, 'Témpera unidad', 0, '', 70.00, 50.00, '2023-06-15'),
(178, 'Borrador azul y rojo Until', 0, '', 50.00, 30.00, '2023-06-15'),
(179, 'Borrador blanco y gris', 0, '', 70.00, 50.00, '2023-06-15'),
(180, 'Birome Bic cristal roja verde', 0, '', 160.00, 140.00, '2023-06-15'),
(181, 'Resaltador fino ', 0, '', 380.00, 190.00, '2023-06-15'),
(182, 'Crayones', 0, '', 220.00, 0.00, '2023-07-01'),
(183, 'Alfiler', 100, '', 450.00, 380.00, '2025-02-02'),
(184, 'Repuesto N 6 ', 0, '', 550.00, 375.00, '2023-08-16'),
(185, 'Lápices de colores por 6 cortos', 0, '', 280.00, 180.00, '2023-08-31'),
(186, 'Aro metálico mediano carpeta', 0, '', 490.00, 0.00, '2023-08-31'),
(187, 'Aro metálico grande carpeta', 0, '', 690.00, 0.00, '2023-08-31'),
(188, 'Cinta papel', 0, '', 950.00, 500.00, '2023-10-20'),
(189, 'Regla flexible 30 cm', 0, '', 380.00, 300.00, '2023-10-20'),
(190, 'Carta truco', 0, '', 600.00, 500.00, '2023-10-20'),
(191, 'Aro para celular', 0, '', 350.00, 140.00, '2023-11-18'),
(192, 'Tijera grande', 0, '', 900.00, 605.00, '2023-11-18'),
(193, 'Encendedor corto', 25, '', 980.00, 840.00, '2023-11-18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `detalle` varchar(255) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `metodo` varchar(255) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
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
-- Indices de la tabla `productos2`
--
ALTER TABLE `productos2`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `items_factura`
--
ALTER TABLE `items_factura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(133) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=267;

--
-- AUTO_INCREMENT de la tabla `productos2`
--
ALTER TABLE `productos2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
