-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 21-11-2024 a las 20:46:48
-- Versión del servidor: 8.0.40-0ubuntu0.20.04.1
-- Versión de PHP: 7.4.3-4ubuntu2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `nombre` varchar(255) NOT NULL,
  `detalle` text NOT NULL,
  `metodo` enum('efectivo','tarjeta','transferencia') NOT NULL,
  `total` decimal(10,2) DEFAULT '0.00',
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id`, `nombre`, `detalle`, `metodo`, `total`, `fecha`) VALUES
(1, 'Miguel', '', 'efectivo', '570.00', '2024-11-21 23:10:29'),
(2, 'Jorge', '5 (1 x 200.00), 22 (2 x 550.00)', 'transferencia', '1300.00', '2024-11-21 23:30:39'),
(3, 'Marcelo', 'Birome color común (2 x 70.00), Borrador (4 x 70.00)', 'tarjeta', '420.00', '2024-11-21 23:35:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `items_factura`
--

CREATE TABLE `items_factura` (
  `id` int NOT NULL,
  `id_factura` int NOT NULL,
  `detalle` varchar(255) NOT NULL,
  `cantidad` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int NOT NULL,
  `producto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `categoria` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `producto`, `stock`, `categoria`, `precio`, `costo`, `fecha`) VALUES
(3, 'Aro argolla chico', 0, '', '180.00', '120.00', '2023-02-18'),
(4, 'Aro metálico chico carpeta', 0, '', '350.00', '31.00', '2023-02-21'),
(5, 'Auricular', 0, '', '200.00', '120.00', '2023-02-13'),
(6, 'Cuaderno 48 hojas', 0, '', '250.00', '80.00', '2023-02-13'),
(7, 'Birome borrable', 0, '', '580.00', '520.00', '2023-02-13'),
(8, 'Birome color común', 0, '', '70.00', '23.00', '2023-03-01'),
(9, 'Birome común azul ABC', 0, '', '50.00', '30.00', '2023-02-15'),
(10, 'Birome Bic azul-negra-roja', 0, '', '140.00', '0.00', '2023-03-01'),
(11, 'Birome Bic color', 0, '', '170.00', '105.00', '2023-03-01'),
(12, 'Birome con gel por 5', 0, '', '650.00', '0.00', '2023-03-01'),
(13, 'Birome cuatro colores retractil', 0, '', '290.00', '146.00', '2023-03-01'),
(14, 'Bolsa de regalo Kraft', 0, '', '70.00', '46.00', '2023-02-15'),
(15, 'Borrador', 0, '', '70.00', '50.00', '2023-02-21'),
(16, 'Botella mancuerda', 0, '', '450.00', '950.00', '2023-02-15'),
(17, 'Brillantina', 0, '', '50.00', '34.00', '2023-02-15'),
(21, 'Candado para mochila', 0, '', '150.00', '50.00', '2023-02-15'),
(22, 'Cargador', 0, '', '550.00', '430.00', '2023-02-15'),
(23, 'Carpeta escolar para anillo ', 0, '', '480.00', '368.00', '2023-03-01'),
(24, 'Carpeta Número 5', 0, '', '250.00', '0.00', '2023-02-13'),
(25, 'Carpeta Número 5 con dibujos', 0, '', '480.00', '300.00', '2023-02-15'),
(26, 'Carpeta tapa transparente A4', 0, '', '190.00', '100.00', '2023-02-15'),
(27, 'Cartuchera', 0, '', '80.00', '50.00', '2023-02-15'),
(28, 'auricular River Boca', 0, '', '800.00', '615.00', '2023-02-27'),
(29, 'Cartulina ', 0, '', '260.00', '115.00', '2023-02-27'),
(32, 'Cinta pack ancha', 0, '', '750.00', '450.00', '2023-02-21'),
(33, 'Cinta pack mediana', 0, '', '600.00', '300.00', '2023-02-21'),
(34, 'Cinta scocht chica', 0, '', '100.00', '70.00', '2023-03-01'),
(35, 'Collar chico', 0, '', '250.00', '180.00', '2023-02-15'),
(36, 'Collar con pañuelo', 0, '', '350.00', '260.00', '2023-02-15'),
(37, 'Collar con tachas 47 cm.', 0, '', '400.00', '230.00', '2023-02-15'),
(38, 'Collar con tachas 57 cm', 0, '', '400.00', '280.00', '2023-02-15'),
(39, 'Collar cuero con puas', 0, '', '500.00', '380.00', '2023-02-15'),
(40, 'Collar y correa grueso', 0, '', '800.00', '450.00', '2023-02-15'),
(41, 'Comedero 22 cm mediano', 0, '', '370.00', '300.00', '2023-02-15'),
(42, 'Copos gomaespuma', 0, '', '550.00', '550.00', '2023-02-15'),
(43, 'Corta uñas grande', 0, '', '690.00', '600.00', '2023-03-01'),
(44, 'Correa cuerito fina', 0, '', '350.00', '270.00', '2023-02-15'),
(45, 'Correa más collar fino', 0, '', '300.00', '120.00', '2023-02-15'),
(46, 'Corrector chico', 0, '', '80.00', '40.00', '2023-02-21'),
(47, 'Corrector grande', 0, '', '400.00', '370.00', '2023-02-21'),
(48, 'Corta uñas infantil', 0, '', '200.00', '100.00', '2023-02-15'),
(49, 'Cuadernillo América', 0, '', '750.00', '495.00', '2023-02-15'),
(50, 'Cuaderno 24 hojas', 0, '', '300.00', '260.00', '2023-02-21'),
(51, 'cuaderno Gloria 84 hojas', 0, '', '250.00', '96.00', '2023-02-15'),
(52, 'Cuaderno Rivadavia 98 hojas', 0, '', '1900.00', '0.00', '2023-02-24'),
(53, 'Cuchara', 0, '', '120.00', '50.00', '2023-02-15'),
(54, 'Encendedor', 0, '', '100.00', '55.00', '2023-02-15'),
(55, 'Esmalte duo argentina', 0, '', '320.00', '243.00', '2023-02-15'),
(56, 'Etiquetas por 5', 0, '', '25.00', '9.00', '2023-02-15'),
(57, 'Fibras por 6 Filgo', 0, '', '400.00', '0.00', '2023-02-27'),
(58, 'fibras por 12', 0, '', '800.00', '100.00', '2023-02-15'),
(59, 'Folio tamaño carpeta N° 3', 0, '', '30.00', '10.00', '2023-02-15'),
(60, 'Forro araña azul', 0, '', '250.00', '220.00', '2023-02-21'),
(61, 'Goma Eva', 0, '', '280.00', '160.00', '2023-02-27'),
(62, 'Globo N° 12 por 50 surtidos', 0, '', '650.00', '495.00', '2023-03-01'),
(63, 'Hoja de block EPICA por 96', 0, '', '650.00', '0.00', '2023-03-01'),
(64, 'Hoja de block Éxito por 96', 0, '', '2000.00', '0.00', '2023-03-01'),
(65, 'Hoja de block Gloria por 96', 0, '', '650.00', '0.00', '2023-03-01'),
(66, 'Hoja canson color', 0, '', '200.00', '60.00', '2023-02-27'),
(67, 'Hoja de calcar', 0, '', '320.00', '260.00', '2023-02-15'),
(68, 'Hoja N° 3 de dibujo ', 0, '', '130.00', '0.00', '2023-03-01'),
(69, 'Identificador', 0, '', '200.00', '55.00', '2023-02-15'),
(70, 'Inflable martillo', 0, '', '400.00', '290.00', '2023-02-15'),
(71, 'Invisible', 0, '', '200.00', '140.00', '2023-02-15'),
(72, 'Jarra plástica 2 1-4 litro', 0, '', '350.00', '260.00', '2023-02-15'),
(73, 'Jarro con tapa', 0, '', '520.00', '480.00', '2023-02-15'),
(74, 'Juego Ludo en bolsa', 0, '', '320.00', '250.00', '2023-02-15'),
(75, 'Labiales Argentina', 0, '', '320.00', '243.00', '2023-02-15'),
(76, 'lápices cortos de colores por 12', 0, '', '480.00', '90.00', '2023-02-27'),
(77, 'lápices largos de colores', 0, '', '350.00', '120.00', '2023-02-15'),
(78, 'lápiz negro', 0, '', '70.00', '24.00', '2023-02-27'),
(79, 'Libro para pintar-cuentos ', 0, '', '150.00', '100.00', '2023-03-01'),
(80, 'Llavero destapador', 0, '', '150.00', '90.00', '2023-02-15'),
(81, 'Llavero día del padre', 0, '', '120.00', '70.00', '2023-02-15'),
(82, 'Llavero madera', 0, '', '120.00', '23.00', '2023-02-15'),
(84, 'Mapas', 0, '', '20.00', '0.00', '2023-02-15'),
(85, 'Marcador al agua Bic Negro', 0, '', '230.00', '200.00', '2023-02-15'),
(86, 'Marcador al agua Bic Rojo', 0, '', '200.00', '177.00', '2023-02-15'),
(87, 'Marcador Filgo por 6', 0, '', '140.00', '120.00', '2023-02-15'),
(88, 'Marcador lavable Ezco', 0, '', '240.00', '190.00', '2023-02-15'),
(89, 'Marcador permanente', 0, '', '80.00', '50.00', '2023-02-15'),
(90, 'Marcador Filgo al agua', 0, '', '200.00', '135.00', '2023-03-01'),
(91, 'Marcador permanente Filgo', 0, '', '280.00', '230.00', '2023-02-15'),
(92, 'Mate polímero', 0, '', '800.00', '630.00', '2023-02-15'),
(93, 'Muñeca bolsa', 0, '', '220.00', '160.00', '2023-02-15'),
(94, 'Naipe', 0, '', '150.00', '110.00', '2023-02-15'),
(95, 'Ojalillo', 0, '', '20.00', '7.00', '2023-03-01'),
(96, 'Papel Crepé', 0, '', '320.00', '150.00', '2023-03-01'),
(97, 'Papel glasé de lustre común', 0, '', '200.00', '0.00', '2023-02-28'),
(98, 'Papel glasé brillose', 0, '', '80.00', '0.00', '2023-02-28'),
(99, 'Papel de regalo brilloso', 0, '', '40.00', '25.00', '2023-02-15'),
(100, 'Papel de regalo grande', 0, '', '350.00', '210.00', '2023-02-15'),
(101, 'Pelota inflable futbol', 0, '', '430.00', '340.00', '2023-02-15'),
(102, 'Pelota inflable playera', 0, '', '380.00', '290.00', '2023-02-15'),
(103, 'Pelota mascota rugby', 0, '', '400.00', '280.00', '2023-02-15'),
(104, 'Pincel por 6 en bolsa', 0, '', '300.00', '170.00', '2023-02-15'),
(105, 'Pinta cara Argentina', 0, '', '320.00', '243.00', '2023-02-15'),
(106, 'Pinta cara Argentina blister', 0, '', '260.00', '138.00', '2023-02-15'),
(107, 'Pintura Argentina', 0, '', '260.00', '180.00', '2023-02-15'),
(108, 'Pistolita de agua', 0, '', '100.00', '30.00', '2023-02-15'),
(109, 'Plasticola Acuarel 50 g', 0, '', '120.00', '72.00', '2023-03-01'),
(110, 'Porta retrato feliz día', 0, '', '300.00', '170.00', '2023-02-15'),
(111, 'Porta retrato madera', 0, '', '250.00', '160.00', '2023-02-15'),
(112, 'Porta SUBE', 0, '', '100.00', '75.00', '2023-02-15'),
(113, 'Portaminas', 0, '', '130.00', '55.00', '2023-02-15'),
(114, 'Porta SUBE 2', 0, '', '250.00', '180.00', '2023-03-01'),
(115, 'Portavela', 0, '', '200.00', '140.00', '2023-02-15'),
(116, 'Pretal más correa chica', 0, '', '250.00', '350.00', '2023-02-15'),
(117, 'Pretal más correa mediana', 0, '', '450.00', '350.00', '2023-02-15'),
(120, 'Regla con forma ', 0, '', '100.00', '0.00', '2023-02-27'),
(121, 'regla flexible corta', 0, '', '250.00', '40.00', '2023-02-27'),
(122, 'Repuesto 5', 0, '', '300.00', '270.00', '2023-02-15'),
(123, 'Repuesto 6 El Nene', 0, '', '240.00', '180.00', '2023-02-15'),
(124, 'Repuesto 6 Miguel Angel', 0, '', '130.00', '70.00', '2023-02-15'),
(125, 'Rueda Gigante inflable negra', 0, '', '950.00', '700.00', '2023-02-15'),
(126, 'sacapuntas', 0, '', '40.00', '20.00', '2023-02-15'),
(127, 'Salvavidas chico', 0, '', '490.00', '350.00', '2023-02-15'),
(128, 'Separadores por 6', 0, '', '190.00', '120.00', '2023-03-01'),
(129, 'Set de geometría por 4 piezas', 0, '', '350.00', '70.00', '2023-02-15'),
(130, 'Set de mate día del padre', 0, '', '800.00', '550.00', '2023-02-15'),
(131, 'Set de mordillos sonajero', 0, '', '280.00', '200.00', '2023-02-15'),
(132, 'Silicona 100 ml.', 0, '', '1400.00', '450.00', '2023-03-01'),
(133, 'Silicona 30 ml.', 0, '', '400.00', '353.00', '2023-03-01'),
(134, 'Silicona 60 ml.', 0, '', '450.00', '345.00', '2023-03-01'),
(135, 'Sonajero', 0, '', '280.00', '180.00', '2023-02-15'),
(136, 'Tabla periódica', 0, '', '380.00', '350.00', '2023-02-21'),
(137, 'Taper jardín', 0, '', '400.00', '224.00', '2023-02-15'),
(138, 'Taza de cerámica', 0, '', '950.00', '570.00', '2023-02-15'),
(139, 'Taza polímero asa común', 0, '', '500.00', '250.00', '2023-02-15'),
(140, 'Taza polímero asa de corazón', 0, '', '500.00', '250.00', '2023-02-15'),
(141, 'Tela mandala por 3', 0, '', '330.00', '330.00', '2023-02-15'),
(142, 'Tela provenzal para cucha por metro', 0, '', '760.00', '460.00', '2023-02-15'),
(143, 'Tela tropical mecánico por metro', 0, '', '900.00', '700.00', '2023-02-15'),
(144, 'Témperas por 6', 0, '', '300.00', '280.00', '2023-02-15'),
(145, 'Tijera chica', 0, '', '60.00', '0.00', '2023-02-27'),
(146, 'Tijera maped', 0, '', '250.00', '0.00', '2023-02-27'),
(147, 'Trincheta', 0, '', '380.00', '315.00', '2023-02-15'),
(148, 'Vaso con pico', 0, '', '950.00', '490.00', '2023-02-15'),
(149, 'Vaso con pico oso', 0, '', '1100.00', '510.00', '2023-02-15'),
(151, 'Voligoma grande', 0, '', '480.00', '250.00', '2023-03-01'),
(152, 'Yoyo', 0, '', '200.00', '120.00', '2023-02-15'),
(154, 'Silicona barra fina', 0, '', '150.00', '45.00', '2023-02-21'),
(155, 'Silicona barra gruesa', 0, '', '190.00', '110.00', '2023-02-21'),
(156, 'Compás metal', 0, '', '350.00', '0.00', '2023-03-04'),
(157, 'Goma Eva con brillo', 0, '', '450.00', '419.00', '2023-03-04'),
(158, 'Birome color por 6', 0, '', '300.00', '0.00', '2023-03-04'),
(159, 'Birome color por 10', 0, '', '600.00', '200.00', '2023-03-04'),
(165, 'Hoja N° 6 de Dibujo Miguel Ángel', 0, '', '600.00', '370.00', '2023-04-14'),
(167, 'Quitaesmalte ', 0, '', '390.00', '180.00', '2023-05-18'),
(168, 'Bengala ', 0, '', '150.00', '118.00', '2023-05-18'),
(169, 'Muñeca Mini princesa', 0, '', '400.00', '257.00', '2023-05-18'),
(170, 'Yenga', 0, '', '850.00', '350.00', '2023-05-18'),
(171, 'Cubo Rubik', 0, '', '300.00', '220.00', '2023-05-18'),
(172, 'Vela Boca River corona', 0, '', '250.00', '130.00', '2023-05-18'),
(173, 'Resaltador por 3', 0, '', '800.00', '230.00', '2023-06-02'),
(174, 'Témperas por 10 ', 0, '', '600.00', '510.00', '2023-06-02'),
(176, 'Microfibra', 0, '', '240.00', '160.00', '2023-06-15'),
(177, 'Témpera unidad', 0, '', '70.00', '50.00', '2023-06-15'),
(178, 'Borrador azul y rojo Until', 0, '', '50.00', '30.00', '2023-06-15'),
(179, 'Borrador blanco y gris', 0, '', '70.00', '50.00', '2023-06-15'),
(180, 'Birome Bic cristal roja verde', 0, '', '160.00', '140.00', '2023-06-15'),
(181, 'Resaltador fino ', 0, '', '380.00', '190.00', '2023-06-15'),
(182, 'Crayones', 0, '', '220.00', '0.00', '2023-07-01'),
(183, 'Alfiler', 0, '', '450.00', '380.00', '2023-08-03'),
(184, 'Repuesto N 6 ', 0, '', '550.00', '375.00', '2023-08-16'),
(185, 'Lápices de colores por 6 cortos', 0, '', '280.00', '180.00', '2023-08-31'),
(186, 'Aro metálico mediano carpeta', 0, '', '490.00', '0.00', '2023-08-31'),
(187, 'Aro metálico grande carpeta', 0, '', '690.00', '0.00', '2023-08-31'),
(188, 'Cinta papel', 0, '', '950.00', '500.00', '2023-10-20'),
(189, 'Regla flexible 30 cm', 0, '', '380.00', '300.00', '2023-10-20'),
(190, 'Carta truco', 0, '', '600.00', '500.00', '2023-10-20'),
(191, 'Aro para celular', 0, '', '350.00', '140.00', '2023-11-18'),
(192, 'Tijera grande', 0, '', '900.00', '605.00', '2023-11-18'),
(193, 'Encendedor corto', 25, '', '980.00', '840.00', '2023-11-18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int NOT NULL,
  `detalle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `cantidad` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `metodo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `detalle`, `cantidad`, `precio`, `metodo`, `fecha`) VALUES
(1, 'Cuaderno', 2, '245.20', 'Transferencia', '2024-11-21 16:02:50'),
(2, 'Goma', 3, '2300.10', 'Efectivo', '2024-11-21 21:28:21'),
(3, 'aaa', 2, '1230.10', 'efectivo', '2024-11-21 21:38:24');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

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
