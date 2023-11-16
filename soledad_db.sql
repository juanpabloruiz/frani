-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-11-2023 a las 17:20:15
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `soledad_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(133) NOT NULL,
  `producto` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `precio` double NOT NULL,
  `costo` double NOT NULL,
  `estado` varchar(255) NOT NULL,
  `registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `producto`, `foto`, `precio`, `costo`, `estado`, `registro`) VALUES
(1, 'Afiche', 'muestra.jpg', 280, 245, 'privado', '2023-02-15 03:00:00'),
(3, 'Aro argolla chico', 'muestra.jpg', 180, 120, '1', '2023-02-18 03:00:00'),
(4, 'Aro metálico chico carpeta', 'muestra.jpg', 350, 31, 'privado', '2023-02-21 03:00:00'),
(5, 'Auricular', 'muestra.jpg', 200, 120, '1', '2023-02-13 03:00:00'),
(6, 'Cuaderno 48 hojas', 'muestra.jpg', 250, 80, 'privado', '2023-02-13 03:00:00'),
(7, 'Birome borrable', 'muestra.jpg', 580, 520, 'publico', '2023-02-13 03:00:00'),
(8, 'Birome color común', 'muestra.jpg', 70, 23, '1', '2023-03-01 03:00:00'),
(9, 'Birome común azul ABC', 'muestra.jpg', 50, 30, '1', '2023-02-15 03:00:00'),
(10, 'Birome Bic azul-negra-roja', 'muestra.jpg', 140, 0, '1', '2023-03-01 03:00:00'),
(11, 'Birome Bic color', 'muestra.jpg', 170, 105, '1', '2023-03-01 03:00:00'),
(12, 'Birome con gel por 5', 'muestra.jpg', 650, 0, '1', '2023-03-01 03:00:00'),
(13, 'Birome cuatro colores retractil', 'muestra.jpg', 290, 146, 'privado', '2023-03-01 03:00:00'),
(14, 'Bolsa de regalo Kraft', 'muestra.jpg', 70, 46, '1', '2023-02-15 03:00:00'),
(15, 'Borrador', 'muestra.jpg', 70, 50, 'publico', '2023-02-21 03:00:00'),
(16, 'Botella mancuerda', 'muestra.jpg', 450, 950, '1', '2023-02-15 03:00:00'),
(17, 'Brillantina', 'muestra.jpg', 50, 34, '1', '2023-02-15 03:00:00'),
(21, 'Candado para mochila', 'muestra.jpg', 150, 50, '1', '2023-02-15 03:00:00'),
(22, 'Cargador', 'muestra.jpg', 550, 430, '1', '2023-02-15 03:00:00'),
(23, 'Carpeta escolar para anillo ', 'muestra.jpg', 480, 368, '1', '2023-03-01 03:00:00'),
(24, 'Carpeta Número 5', 'muestra.jpg', 250, 0, '1', '2023-02-13 03:00:00'),
(25, 'Carpeta Número 5 con dibujos', 'muestra.jpg', 480, 300, '1', '2023-02-15 03:00:00'),
(26, 'Carpeta tapa transparente A4', 'muestra.jpg', 190, 100, 'privado', '2023-02-15 03:00:00'),
(27, 'Cartuchera', 'muestra.jpg', 80, 50, '1', '2023-02-15 03:00:00'),
(28, 'auricular River Boca', 'muestra.jpg', 800, 615, 'privado', '2023-02-27 03:00:00'),
(29, 'Cartulina ', 'muestra.jpg', 250, 115, 'privado', '2023-02-27 03:00:00'),
(32, 'Cinta pack ancha', 'muestra.jpg', 750, 450, 'privado', '2023-02-21 03:00:00'),
(33, 'Cinta pack mediana', 'muestra.jpg', 600, 300, 'privado', '2023-02-21 03:00:00'),
(34, 'Cinta scocht chica', 'muestra.jpg', 100, 70, '1', '2023-03-01 03:00:00'),
(35, 'Collar chico', 'muestra.jpg', 250, 180, '1', '2023-02-15 03:00:00'),
(36, 'Collar con pañuelo', 'muestra.jpg', 350, 260, '1', '2023-02-15 03:00:00'),
(37, 'Collar con tachas 47 cm.', 'muestra.jpg', 400, 230, '1', '2023-02-15 03:00:00'),
(38, 'Collar con tachas 57 cm', 'muestra.jpg', 400, 280, '1', '2023-02-15 03:00:00'),
(39, 'Collar cuero con puas', 'muestra.jpg', 500, 380, '1', '2023-02-15 03:00:00'),
(40, 'Collar y correa grueso', 'muestra.jpg', 800, 450, '1', '2023-02-15 03:00:00'),
(41, 'Comedero 22 cm mediano', 'muestra.jpg', 370, 300, '1', '2023-02-15 03:00:00'),
(42, 'Copos gomaespuma', 'muestra.jpg', 550, 550, '1', '2023-02-15 03:00:00'),
(43, 'Corta uñas grande', 'muestra.jpg', 300, 157, '1', '2023-03-01 03:00:00'),
(44, 'Correa cuerito fina', 'muestra.jpg', 350, 270, '1', '2023-02-15 03:00:00'),
(45, 'Correa más collar fino', 'muestra.jpg', 300, 120, '1', '2023-02-15 03:00:00'),
(46, 'Corrector chico', 'muestra.jpg', 80, 40, '1', '2023-02-21 03:00:00'),
(47, 'Corrector grande', 'muestra.jpg', 140, 80, '1', '2023-02-21 03:00:00'),
(48, 'Corta uñas infantil', 'muestra.jpg', 200, 100, '1', '2023-02-15 03:00:00'),
(49, 'Cuadernillo América', 'muestra.jpg', 750, 495, '1', '2023-02-15 03:00:00'),
(50, 'Cuaderno 24 hojas', 'muestra.jpg', 300, 260, 'privado', '2023-02-21 03:00:00'),
(51, 'cuaderno Gloria 84 hojas', 'muestra.jpg', 250, 96, 'publico', '2023-02-15 03:00:00'),
(52, 'Cuaderno Rivadavia 98 hojas', 'muestra.jpg', 1900, 0, '1', '2023-02-24 03:00:00'),
(53, 'Cuchara', 'muestra.jpg', 120, 50, '1', '2023-02-15 03:00:00'),
(54, 'Encendedor', 'muestra.jpg', 100, 55, '1', '2023-02-15 03:00:00'),
(55, 'Esmalte duo argentina', 'muestra.jpg', 320, 243, '1', '2023-02-15 03:00:00'),
(56, 'Etiquetas por 5', 'muestra.jpg', 25, 9, '1', '2023-02-15 03:00:00'),
(57, 'Fibras por 6 Filgo', 'muestra.jpg', 400, 0, 'privado', '2023-02-27 03:00:00'),
(58, 'fibras por 12', 'muestra.jpg', 800, 100, 'privado', '2023-02-15 03:00:00'),
(59, 'Folio tamaño carpeta N° 3', 'muestra.jpg', 30, 10, '1', '2023-02-15 03:00:00'),
(60, 'Forro araña azul', 'muestra.jpg', 250, 220, 'privado', '2023-02-21 03:00:00'),
(61, 'Goma Eva', 'muestra.jpg', 280, 160, 'publico', '2023-02-27 03:00:00'),
(62, 'Globo N° 12 por 50 surtidos', 'muestra.jpg', 650, 495, '1', '2023-03-01 03:00:00'),
(63, 'Hoja de block EPICA por 96', 'muestra.jpg', 650, 0, '1', '2023-03-01 03:00:00'),
(64, 'Hoja de block Éxito por 96', 'muestra.jpg', 730, 0, '1', '2023-03-01 03:00:00'),
(65, 'Hoja de block Gloria por 96', 'muestra.jpg', 650, 0, '1', '2023-03-01 03:00:00'),
(66, 'Hoja canson color', 'muestra.jpg', 200, 60, '1', '2023-02-27 03:00:00'),
(67, 'Hoja de calcar', 'muestra.jpg', 320, 260, 'publico', '2023-02-15 03:00:00'),
(68, 'Hoja N° 3 de dibujo ', 'muestra.jpg', 130, 0, '1', '2023-03-01 03:00:00'),
(69, 'Identificador', 'muestra.jpg', 200, 55, '1', '2023-02-15 03:00:00'),
(70, 'Inflable martillo', 'muestra.jpg', 400, 290, '1', '2023-02-15 03:00:00'),
(71, 'Invisible', 'muestra.jpg', 200, 140, '1', '2023-02-15 03:00:00'),
(72, 'Jarra plástica 2 1-4 litro', 'muestra.jpg', 350, 260, '1', '2023-02-15 03:00:00'),
(73, 'Jarro con tapa', 'muestra.jpg', 520, 480, '1', '2023-02-15 03:00:00'),
(74, 'Juego Ludo en bolsa', 'muestra.jpg', 320, 250, '1', '2023-02-15 03:00:00'),
(75, 'Labiales Argentina', 'muestra.jpg', 320, 243, '1', '2023-02-15 03:00:00'),
(76, 'lápices cortos de colores por 12', 'muestra.jpg', 480, 90, 'privado', '2023-02-27 03:00:00'),
(77, 'lápices largos de colores', 'muestra.jpg', 350, 120, 'privado', '2023-02-15 03:00:00'),
(78, 'lápiz negro', 'muestra.jpg', 70, 24, 'privado', '2023-02-27 03:00:00'),
(79, 'Libro para pintar-cuentos ', 'muestra.jpg', 150, 100, '1', '2023-03-01 03:00:00'),
(80, 'Llavero destapador', 'muestra.jpg', 150, 90, '1', '2023-02-15 03:00:00'),
(81, 'Llavero día del padre', 'muestra.jpg', 120, 70, '1', '2023-02-15 03:00:00'),
(82, 'Llavero madera', 'muestra.jpg', 120, 23, '1', '2023-02-15 03:00:00'),
(84, 'Mapas', 'muestra.jpg', 20, 0, '1', '2023-02-15 03:00:00'),
(85, 'Marcador al agua Bic Negro', 'muestra.jpg', 230, 200, '1', '2023-02-15 03:00:00'),
(86, 'Marcador al agua Bic Rojo', 'muestra.jpg', 200, 177, '1', '2023-02-15 03:00:00'),
(87, 'Marcador Filgo por 6', 'muestra.jpg', 140, 120, '1', '2023-02-15 03:00:00'),
(88, 'Marcador lavable Ezco', 'muestra.jpg', 240, 190, '1', '2023-02-15 03:00:00'),
(89, 'Marcador permanente', 'muestra.jpg', 80, 50, '1', '2023-02-15 03:00:00'),
(90, 'Marcador Filgo al agua', 'muestra.jpg', 200, 135, '1', '2023-03-01 03:00:00'),
(91, 'Marcador permanente Filgo', 'muestra.jpg', 280, 230, 'privado', '2023-02-15 03:00:00'),
(92, 'Mate polímero', 'muestra.jpg', 800, 630, '1', '2023-02-15 03:00:00'),
(93, 'Muñeca bolsa', 'muestra.jpg', 220, 160, '1', '2023-02-15 03:00:00'),
(94, 'Naipe', 'muestra.jpg', 150, 110, '1', '2023-02-15 03:00:00'),
(95, 'Ojalillo', 'muestra.jpg', 20, 7, '1', '2023-03-01 03:00:00'),
(96, 'Papel Crepé', 'muestra.jpg', 320, 150, 'privado', '2023-03-01 03:00:00'),
(97, 'Papel glasé de lustre común', 'muestra.jpg', 200, 0, 'privado', '2023-02-28 03:00:00'),
(98, 'Papel glasé brillose', 'muestra.jpg', 80, 0, '1', '2023-02-28 03:00:00'),
(99, 'Papel de regalo brilloso', 'muestra.jpg', 40, 25, '1', '2023-02-15 03:00:00'),
(100, 'Papel de regalo grande', 'muestra.jpg', 350, 210, 'privado', '2023-02-15 03:00:00'),
(101, 'Pelota inflable futbol', 'muestra.jpg', 430, 340, '1', '2023-02-15 03:00:00'),
(102, 'Pelota inflable playera', 'muestra.jpg', 380, 290, '1', '2023-02-15 03:00:00'),
(103, 'Pelota mascota rugby', 'muestra.jpg', 400, 280, '1', '2023-02-15 03:00:00'),
(104, 'Pincel por 6 en bolsa', 'muestra.jpg', 300, 170, '1', '2023-02-15 03:00:00'),
(105, 'Pinta cara Argentina', 'muestra.jpg', 320, 243, '1', '2023-02-15 03:00:00'),
(106, 'Pinta cara Argentina blister', 'muestra.jpg', 260, 138, '1', '2023-02-15 03:00:00'),
(107, 'Pintura Argentina', 'muestra.jpg', 260, 180, '1', '2023-02-15 03:00:00'),
(108, 'Pistolita de agua', 'muestra.jpg', 100, 30, '1', '2023-02-15 03:00:00'),
(109, 'Plasticola Acuarel 50 g', 'muestra.jpg', 120, 72, '1', '2023-03-01 03:00:00'),
(110, 'Porta retrato feliz día', 'muestra.jpg', 300, 170, '1', '2023-02-15 03:00:00'),
(111, 'Porta retrato madera', 'muestra.jpg', 250, 160, '1', '2023-02-15 03:00:00'),
(112, 'Porta SUBE', 'muestra.jpg', 100, 75, '1', '2023-02-15 03:00:00'),
(113, 'Portaminas', 'muestra.jpg', 130, 55, '1', '2023-02-15 03:00:00'),
(114, 'Porta SUBE 2', 'muestra.jpg', 250, 180, '1', '2023-03-01 03:00:00'),
(115, 'Portavela', 'muestra.jpg', 200, 140, '1', '2023-02-15 03:00:00'),
(116, 'Pretal más correa chica', 'muestra.jpg', 250, 350, '1', '2023-02-15 03:00:00'),
(117, 'Pretal más correa mediana', 'muestra.jpg', 450, 350, '1', '2023-02-15 03:00:00'),
(120, 'Regla con forma ', 'muestra.jpg', 100, 0, '1', '2023-02-27 03:00:00'),
(121, 'regla flexible corta', 'muestra.jpg', 250, 40, 'privado', '2023-02-27 03:00:00'),
(122, 'Repuesto 5', 'muestra.jpg', 300, 270, 'privado', '2023-02-15 03:00:00'),
(123, 'Repuesto 6 El Nene', 'muestra.jpg', 240, 180, '1', '2023-02-15 03:00:00'),
(124, 'Repuesto 6 Miguel Angel', 'muestra.jpg', 130, 70, '1', '2023-02-15 03:00:00'),
(125, 'Rueda Gigante inflable negra', 'muestra.jpg', 950, 700, '1', '2023-02-15 03:00:00'),
(126, 'sacapuntas', 'muestra.jpg', 40, 20, '1', '2023-02-15 03:00:00'),
(127, 'Salvavidas chico', 'muestra.jpg', 490, 350, '1', '2023-02-15 03:00:00'),
(128, 'Separadores por 6', 'muestra.jpg', 190, 120, '1', '2023-03-01 03:00:00'),
(129, 'Set de geometría por 4 piezas', 'muestra.jpg', 350, 70, 'publico', '2023-02-15 03:00:00'),
(130, 'Set de mate día del padre', 'muestra.jpg', 800, 550, '1', '2023-02-15 03:00:00'),
(131, 'Set de mordillos sonajero', 'muestra.jpg', 280, 200, '1', '2023-02-15 03:00:00'),
(132, 'Silicona 100 ml.', 'muestra.jpg', 1400, 450, 'privado', '2023-03-01 03:00:00'),
(133, 'Silicona 30 ml.', 'muestra.jpg', 400, 353, 'privado', '2023-03-01 03:00:00'),
(134, 'Silicona 60 ml.', 'muestra.jpg', 450, 345, '1', '2023-03-01 03:00:00'),
(135, 'Sonajero', 'muestra.jpg', 280, 180, '1', '2023-02-15 03:00:00'),
(136, 'Tabla periódica', 'muestra.jpg', 380, 350, 'privado', '2023-02-21 03:00:00'),
(137, 'Taper jardín', 'muestra.jpg', 400, 224, '1', '2023-02-15 03:00:00'),
(138, 'Taza de cerámica', 'muestra.jpg', 950, 570, '1', '2023-02-15 03:00:00'),
(139, 'Taza polímero asa común', 'muestra.jpg', 500, 250, '1', '2023-02-15 03:00:00'),
(140, 'Taza polímero asa de corazón', 'muestra.jpg', 500, 250, '1', '2023-02-15 03:00:00'),
(141, 'Tela mandala por 3', 'muestra.jpg', 330, 330, '1', '2023-02-15 03:00:00'),
(142, 'Tela provenzal para cucha por metro', 'muestra.jpg', 760, 460, '1', '2023-02-15 03:00:00'),
(143, 'Tela tropical mecánico por metro', 'muestra.jpg', 900, 700, '1', '2023-02-15 03:00:00'),
(144, 'Témperas por 6', 'muestra.jpg', 300, 280, '1', '2023-02-15 03:00:00'),
(145, 'Tijera chica', 'muestra.jpg', 60, 0, '1', '2023-02-27 03:00:00'),
(146, 'Tijera maped', 'muestra.jpg', 250, 0, '1', '2023-02-27 03:00:00'),
(147, 'Trincheta', 'muestra.jpg', 300, 80, 'privado', '2023-02-15 03:00:00'),
(148, 'Vaso con pico', 'muestra.jpg', 950, 490, '1', '2023-02-15 03:00:00'),
(149, 'Vaso con pico oso', 'muestra.jpg', 1100, 510, '1', '2023-02-15 03:00:00'),
(151, 'Voligoma grande', 'muestra.jpg', 480, 250, 'privado', '2023-03-01 03:00:00'),
(152, 'Yoyo', 'muestra.jpg', 200, 120, '1', '2023-02-15 03:00:00'),
(154, 'Silicona barra fina', 'muestra.jpg', 150, 45, '1', '2023-02-21 03:00:00'),
(155, 'Silicona barra gruesa', 'muestra.jpg', 190, 110, '1', '2023-02-21 03:00:00'),
(156, 'Compás metal', 'muestra.jpg', 350, 0, 'publico', '2023-03-04 14:39:59'),
(157, 'Goma Eva con brillo', 'muestra.jpg', 450, 419, 'publico', '2023-03-04 14:41:33'),
(158, 'Birome color por 6', 'muestra.jpg', 300, 0, 'publico', '2023-03-04 14:43:06'),
(159, 'Birome color por 10', 'muestra.jpg', 600, 200, 'publico', '2023-03-04 14:43:28'),
(165, 'Hoja N° 6 de Dibujo Miguel Ángel', 'muestra.jpg', 600, 370, 'privado', '2023-04-14 14:35:28'),
(166, 'Acuarela Filgo por 12', 'muestra.jpg', 550, 430, 'privado', '2023-05-12 13:17:35'),
(167, 'Quitaesmalte ', 'muestra.jpg', 390, 180, 'publico', '2023-05-18 23:56:14'),
(168, 'Bengala ', 'muestra.jpg', 150, 118, 'privado', '2023-05-18 23:56:35'),
(169, 'Muñeca Mini princesa', 'muestra.jpg', 400, 257, 'privado', '2023-05-18 23:59:40'),
(170, 'Yenga', 'muestra.jpg', 850, 350, 'privado', '2023-05-19 00:00:08'),
(171, 'Cubo Rubik', 'muestra.jpg', 300, 220, 'privado', '2023-05-19 00:00:41'),
(172, 'Vela Boca River corona', 'muestra.jpg', 250, 130, 'privado', '2023-05-19 00:01:03'),
(173, 'Resaltador por 3', 'muestra.jpg', 800, 230, 'privado', '2023-06-02 21:24:20'),
(174, 'Témperas por 10 ', 'muestra.jpg', 600, 510, 'privado', '2023-06-02 23:01:56'),
(175, 'Afiche estampado', 'muestra.jpg', 240, 190, 'privado', '2023-06-03 13:17:17'),
(176, 'Microfibra', 'muestra.jpg', 240, 160, 'privado', '2023-06-15 15:36:11'),
(177, 'Témpera unidad', 'muestra.jpg', 70, 50, 'privado', '2023-06-15 15:36:35'),
(178, 'Borrador azul y rojo Until', 'muestra.jpg', 50, 30, 'privado', '2023-06-15 15:36:54'),
(179, 'Borrador blanco y gris', 'muestra.jpg', 70, 50, 'privado', '2023-06-15 15:37:11'),
(180, 'Birome Bic cristal roja verde', 'muestra.jpg', 160, 140, 'privado', '2023-06-15 15:37:30'),
(181, 'Resaltador fino ', 'muestra.jpg', 380, 190, 'privado', '2023-06-15 15:37:46'),
(182, 'Crayones', 'muestra.jpg', 220, 0, 'privado', '2023-07-01 21:30:30'),
(183, 'Alfiler', 'muestra.jpg', 450, 380, 'privado', '2023-08-03 15:01:34'),
(184, 'Repuesto N 6 ', 'muestra.jpg', 550, 375, 'privado', '2023-08-17 00:40:16'),
(185, 'Lápices de colores por 6 cortos', 'muestra.jpg', 280, 180, 'privado', '2023-08-31 14:44:15'),
(186, 'Aro metálico mediano carpeta', 'muestra.jpg', 490, 0, 'privado', '2023-08-31 16:14:46'),
(187, 'Aro metálico grande carpeta', 'muestra.jpg', 690, 0, 'privado', '2023-08-31 16:15:05'),
(188, 'Cinta papel', 'muestra.jpg', 950, 500, 'privado', '2023-10-20 21:53:57'),
(189, 'Regla flexible 30 cm', 'muestra.jpg', 380, 300, 'privado', '2023-10-20 21:55:12'),
(190, 'Carta truco', 'muestra.jpg', 600, 500, 'privado', '2023-10-20 21:56:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(133) NOT NULL,
  `correo` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `clave` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `rango` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `correo`, `clave`, `rango`, `registro`) VALUES
(1, 'soledadm77@gmail.com', 'corazon', 'admin', '2022-05-29 20:18:50'),
(2, 'pabloruiz1980@gmail.com', 'soledad', 'admin', '2022-05-30 13:04:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(133) NOT NULL,
  `producto` varchar(255) NOT NULL,
  `cantidad` varchar(255) NOT NULL,
  `unitario` varchar(255) NOT NULL,
  `total` varchar(255) NOT NULL,
  `registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `producto`, `cantidad`, `unitario`, `total`, `registro`) VALUES
(6, 'Identificador', '2', '200', '400', '2022-09-20 19:14:25'),
(7, 'Taza de cerámica', '2', '950', '1900', '2022-09-20 19:11:43'),
(8, 'Alcancía', '1', '480', '480', '2022-09-20 19:11:51'),
(9, 'Llavero polímero ', '22', '120', '2640', '2022-09-20 19:14:51'),
(10, 'Collar chico goma gato', '2', '250', '500', '2022-09-20 19:14:05'),
(11, 'Mate polímero', '2', '650', '1300', '2022-09-20 19:15:07'),
(12, 'Invisible', '1', '200', '200', '2022-09-21 20:39:45'),
(13, 'Taza polímero asa común ', '2', '450', '900', '2022-09-21 20:39:56'),
(14, 'Taza de cerámica', '1', '950', '950', '2022-09-22 19:09:31'),
(15, 'Labiales Argentina', '1', '300', '300', '2022-09-22 19:09:40'),
(16, 'Pintura Argentina', '1', '260', '260', '2022-09-22 19:09:55'),
(17, 'Taza de cerámica', '2', '950', '1900', '2022-09-24 18:11:01'),
(18, 'Yerbero', '1', '480', '480', '2022-09-24 18:11:09'),
(19, 'Pretal más correa chica', '1', '250', '250', '2022-09-24 18:11:19'),
(20, 'Identificador', '1', '200', '200', '2022-09-24 18:11:29'),
(21, 'Taza polímero asa de corazón ', '1', '450', '450', '2022-09-24 18:25:19'),
(22, 'Labiales Argentina', '1', '320', '320', '2022-09-24 19:01:28'),
(23, 'Collar cuero con puas', '1', '450', '450', '2022-09-24 19:02:09'),
(24, 'Camiseta Argentina Talle 3', '1', '800', '800', '2022-09-24 21:30:39'),
(25, 'Camiseta Argentina Talle 4', '1', '900', '900', '2022-09-24 21:30:45'),
(26, 'Yoyo', '1', '150', '150', '2022-09-26 18:58:16'),
(27, 'Afiche', '1', '80', '80', '2022-09-26 20:41:53'),
(28, 'Taza polímero asa de corazón ', '1', '450', '450', '2022-09-27 19:18:18'),
(29, 'Taza polímero asa común ', '1', '450', '450', '2022-09-27 19:39:44'),
(30, 'Taza polímero asa común ', '1', '450', '450', '2022-09-28 12:05:52'),
(31, 'Camiseta Argentina Talle 5', '1', '1000', '1000', '2022-09-30 10:36:31'),
(32, 'Taza polímero asa común ', '1', '450', '450', '2022-10-01 19:19:02'),
(33, 'Identificador', '1', '200', '200', '2022-10-01 19:19:09'),
(34, 'Pretal talle 3', '1', '450', '450', '2022-10-01 19:19:16'),
(35, 'Identificador', '1', '200', '200', '2022-10-01 23:15:23'),
(36, 'Taza de cerámica', '1', '950', '950', '2022-10-01 23:15:33'),
(38, 'Pretal más correa mediana', '1', '450', '450', '2022-10-03 20:36:24'),
(39, 'Collar chico', '1', '250', '250', '2022-10-03 20:36:39'),
(40, 'Cartulina color', '1', '60', '60', '2022-10-04 10:43:19'),
(41, 'Birome Bic', '1', '70', '70', '2022-10-04 10:43:30'),
(42, 'regla flexible corta', '1', '60', '60', '2022-10-04 11:31:37'),
(44, 'Birome color ', '2', '40', '80', '2022-10-04 11:33:25'),
(45, 'Marcador permanente Filgo', '1', '100', '100', '2022-10-04 11:34:15'),
(46, 'Corrector', '1', '120', '120', '2022-10-04 11:39:17'),
(47, 'Pistolita de agua', '1', '100', '100', '2022-10-04 11:40:24'),
(48, 'Mate polímero', '6', '650', '3900', '2022-10-04 12:18:20'),
(49, 'Taza polímero asa de corazón ', '2', '450', '900', '2022-10-04 12:18:29'),
(50, 'Llavero polímero ', '4', '120', '480', '2022-10-05 20:31:01'),
(53, 'Marcador permanente Filgo', '1', '100', '100', '2022-10-06 13:10:30'),
(57, 'Yoyo', '1', '200', '200', '2022-10-06 13:11:55'),
(58, 'Chopp polímero', '1', '1100', '1100', '2022-10-06 20:10:09'),
(60, 'Llavero polímero ', '1', '150', '150', '2022-10-06 20:11:22'),
(61, 'Identificador', '1', '200', '200', '2022-10-06 20:11:34'),
(62, 'Pretal más correa mediana', '1', '450', '450', '2022-10-07 14:04:20'),
(63, 'Identificador', '1', '200', '200', '2022-10-07 14:05:15'),
(64, 'Pretal más correa talle 3', '1', '800', '800', '2022-10-07 14:05:24'),
(65, 'Silicona 100 ml.', '1', '280', '280', '2022-10-07 21:01:07'),
(66, 'Identificador', '3', '200', '600', '2022-10-11 19:22:46'),
(67, 'Identificador', '1', '200', '200', '2022-10-12 19:11:44'),
(68, 'Chopp polímero', '1', '1100', '1100', '2022-10-12 19:55:31'),
(69, 'Mate polímero', '1', '750', '750', '2022-10-13 10:48:52'),
(70, 'Taza de cerámica', '1', '950', '950', '2022-10-13 19:40:59'),
(71, 'Taza polímero asa común ', '1', '450', '450', '2022-10-14 11:53:09'),
(72, 'Mate polímero', '1', '750', '750', '2022-10-14 11:53:18'),
(73, 'Yerbero', '1', '550', '550', '2022-10-14 11:53:24'),
(75, 'Taza polímero asa de corazón ', '2', '450', '900', '2022-10-14 13:05:48'),
(76, 'Birome Bic', '1', '80', '80', '2022-10-14 20:51:09'),
(77, 'Birome común azul ABC', '2', '40', '80', '2022-10-14 20:52:01'),
(78, 'Collar', '1', '320', '320', '2022-10-18 12:46:28'),
(80, 'Hebilla', '1', '100', '100', '2022-10-18 12:55:33'),
(81, 'lápiz negro', '1', '30', '30', '2022-10-18 12:55:40'),
(82, 'Minions', '5', '350', '1750', '2022-10-19 11:39:40'),
(83, 'Barbijo', '1', '100', '100', '2022-10-19 12:10:59'),
(84, 'Llavero polímero ', '20', '150', '3000', '2022-10-19 12:11:09'),
(85, 'Collar y correa gruesa', '1', '950', '950', '2022-10-19 20:27:54'),
(86, 'Marcador permanente Filgo', '1', '100', '100', '2022-10-20 19:23:46'),
(87, 'Identificador', '2', '200', '400', '2022-10-22 11:02:22'),
(88, 'Témperas por 6', '2', '300', '600', '2022-10-22 12:26:29'),
(89, 'Yoyo', '1', '200', '200', '2022-10-22 13:04:15'),
(90, 'Llavero polímero ', '12', '150', '1800', '2022-10-26 12:05:48'),
(91, 'Identificador', '1', '200', '200', '2022-10-27 18:26:24'),
(92, 'Taza polímero asa común ', '2', '450', '900', '2022-10-27 18:26:35'),
(93, 'Taza polímero asa de corazón ', '2', '450', '900', '2022-10-27 18:26:43'),
(94, 'Birome Bic', '1', '90', '90', '2022-10-28 10:36:49'),
(95, 'Birome común azul ABC', '1', '40', '40', '2022-10-28 10:37:01'),
(96, 'Pintura bandera', '1', '350', '350', '2022-10-28 10:49:30'),
(97, 'Llavero polímero ', '16', '150', '2400', '2022-11-01 18:51:39'),
(98, 'Birome Bic', '1', '90', '90', '2022-11-01 19:08:25'),
(99, 'Afiche', '1', '100', '100', '2022-11-02 20:29:47'),
(100, 'Collar chico', '1', '250', '250', '2022-11-02 20:30:26'),
(101, 'Identificador', '1', '200', '200', '2022-11-02 20:30:39'),
(102, 'Collar y correa chico', '1', '450', '450', '2022-11-02 20:31:22'),
(103, 'Taza polímero asa de corazón ', '1', '450', '450', '2022-11-03 12:03:38'),
(104, 'Pinta cara Argentina', '1', '320', '320', '2022-11-03 12:28:51'),
(105, 'Gomitas', '1', '130', '130', '2022-11-03 12:29:14'),
(106, 'Collares por 4', '1', '1060', '1060', '2022-11-04 21:52:24'),
(108, 'Pinta cara Argentina', '1', '320', '320', '2022-11-04 21:52:55'),
(109, 'Identificador', '1', '200', '200', '2022-11-05 12:32:56'),
(110, 'Taza polímero asa común ', '6', '450', '2700', '2022-11-05 12:33:09'),
(111, 'Collar', '1', '280', '280', '2022-11-05 12:33:22'),
(112, 'Muñeco Argentina', '1', '200', '200', '2022-11-05 12:33:35'),
(113, 'Taza de cerámica', '1', '950', '950', '2022-11-08 11:45:09'),
(114, 'Chopp polímero', '1', '1100', '1100', '2022-11-08 11:45:19'),
(115, 'Identificador', '1', '200', '200', '2022-11-08 11:45:27'),
(116, 'Collar chico', '1', '250', '250', '2022-11-08 11:45:43'),
(117, 'Collar', '1', '300', '300', '2022-11-09 19:14:51'),
(118, 'Identificador', '1', '200', '200', '2022-11-09 19:15:23'),
(119, 'Hoja de block', '1', '300', '300', '2022-11-09 19:15:45'),
(120, 'Cortauñas', '1', '200', '200', '2022-11-09 19:15:55'),
(121, 'Impresión tela', '1', '150', '150', '2022-11-09 19:44:20'),
(122, 'Muñeco de apego', '1', '350', '350', '2022-11-09 19:44:45'),
(124, 'Collar correa grueso', '1', '950', '950', '2022-11-09 20:33:33'),
(125, 'Collares cuerito', '2', '950', '1900', '2022-11-09 20:33:47'),
(126, 'Pincel N 5', '1', '140', '140', '2022-11-10 10:46:54'),
(127, 'Témperas sueltas', '2', '100', '200', '2022-11-10 10:47:06'),
(128, 'Birome Bic', '1', '90', '90', '2022-11-10 10:47:13'),
(129, 'Borrador', '1', '40', '40', '2022-11-10 11:39:03'),
(130, 'Gomitas ', '2', '40', '80', '2022-11-10 11:39:29'),
(131, 'Ropa ARG talle 5', '1', '1000', '1000', '2022-11-10 18:59:10'),
(132, 'Botellita', '1', '180', '180', '2022-11-11 11:54:45'),
(133, 'Birome Bic', '3', '90', '270', '2022-11-11 20:31:03'),
(134, 'Alpargata', '1', '600', '600', '2022-11-11 20:31:19'),
(135, 'Juguete para gato ', '1', '40', '40', '2022-11-11 20:31:30'),
(136, 'Bolsita de regalo', '1', '20', '20', '2022-11-11 20:31:39'),
(137, 'Juego de autos', '1', '300', '300', '2022-11-11 20:31:52'),
(139, 'Pintura bandera', '1', '350', '350', '2022-11-14 11:01:04'),
(140, 'Pretal correa', '1', '450', '450', '2022-11-14 11:02:48'),
(141, 'Identificador', '1', '200', '200', '2022-11-14 11:03:04'),
(142, 'Collar chico', '1', '250', '250', '2022-11-14 11:03:17'),
(143, 'Cargador', '1', '1150', '1150', '2022-11-16 12:20:19');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item` (`producto`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(133) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(133) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(133) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
