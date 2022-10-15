-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 15-10-2022 a las 13:35:39
-- Versión del servidor: 5.6.37
-- Versión de PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
  `producto` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `precio` double NOT NULL,
  `costo` double NOT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `producto`, `foto`, `precio`, `costo`, `estado`, `registro`) VALUES
(1, 'Afiche', 'muestra.jpg', 100, 80, 'publico', '2022-06-08 21:06:46'),
(2, 'Aro metálico', 'muestra.jpg', 20, 9, 'publico', '2022-05-31 14:44:27'),
(3, 'Auricular', 'muestra.jpg', 200, 120, 'publico', '2022-05-31 14:50:22'),
(4, 'Birome Bic', 'muestra.jpg', 90, 40, 'publico', '2022-05-30 22:46:35'),
(5, 'Birome borrable', 'muestra.jpg', 160, 140, 'publico', '2022-05-31 14:42:24'),
(6, 'Bolsa de regalo Kraft ', 'muestra.jpg', 70, 46, 'publico', '2022-06-08 21:37:32'),
(8, 'Candado para mochila', 'muestra.jpg', 100, 50, 'publico', '2022-05-31 14:49:45'),
(9, 'Carpeta Número 5', 'muestra.jpg', 180, 110, 'publico', '2022-05-31 14:48:51'),
(10, 'Carpeta Número 5 con dibujos', 'muestra.jpg', 230, 180, 'publico', '2022-05-31 14:49:17'),
(11, 'Carpeta tapa transparente A4', 'muestra.jpg', 120, 60, 'publico', '2022-05-31 14:51:29'),
(12, 'Cartuchera', 'muestra.jpg', 80, 50, 'publico', '2022-05-30 22:43:59'),
(13, 'Cartulina blanca', 'muestra.jpg', 60, 33, 'publico', '2022-05-31 14:43:53'),
(14, 'Cartulina color', 'muestra.jpg', 60, 40, 'publico', '2022-05-31 14:43:40'),
(15, 'Chopp polímero', 'muestra.jpg', 1100, 690, 'publico', '2022-06-02 00:14:04'),
(16, 'Cinta pack ancha', 'muestra.jpg', 200, 170, 'publico', '2022-05-31 14:48:07'),
(17, 'Cinta pack fina', 'muestra.jpg', 180, 150, 'publico', '2022-06-09 16:03:19'),
(18, 'Compás general', 'muestra.jpg', 23, 12, 'publico', '2022-05-31 03:13:48'),
(19, 'Copos gomaespuma', 'muestra.jpg', 550, 550, 'publico', '2022-05-31 22:23:02'),
(20, 'corrector chico', 'muestra.jpg', 50, 35, 'publico', '2022-05-30 22:47:43'),
(21, 'Cuadernillo', 'muestra.jpg', 250, 190, 'publico', '2022-05-31 14:49:31'),
(22, 'cuaderno 24 hojas', 'muestra.jpg', 50, 30, 'publico', '2022-05-30 22:45:24'),
(23, 'cuaderno Gloria 84 hojas', 'muestra.jpg', 140, 96, 'publico', '2022-05-30 22:45:51'),
(24, 'Cuaderno Rivadavia 98 hojas', 'muestra.jpg', 500, 0, 'publico', '2022-06-09 22:12:32'),
(25, 'Etiquetas por 5', 'muestra.jpg', 25, 9, 'publico', '2022-05-30 22:46:15'),
(26, 'fibras por 12', 'muestra.jpg', 150, 100, 'publico', '2022-05-30 22:47:10'),
(27, 'Folio tamaño carpeta N° 3', 'muestra.jpg', 25, 10, 'publico', '2022-05-31 14:42:49'),
(28, 'Hoja canson color ', 'muestra.jpg', 90, 60, 'publico', '2022-05-31 14:50:52'),
(29, 'Hoja de calcar ', 'muestra.jpg', 100, 70, 'publico', '2022-05-31 14:50:39'),
(30, 'Jarro con tapa', 'muestra.jpg', 520, 480, 'publico', '2022-06-02 00:14:35'),
(31, 'lápices cortos de colores', 'muestra.jpg', 120, 77, 'publico', '2022-05-30 22:43:05'),
(32, 'lápices largos de colores', 'muestra.jpg', 220, 120, 'publico', '2022-05-30 22:43:44'),
(33, 'lápiz negro', 'muestra.jpg', 20, 8, 'publico', '2022-05-30 22:44:30'),
(34, 'Llavero destapador', 'muestra.jpg', 150, 90, 'publico', '2022-06-08 00:24:10'),
(35, 'Llavero día del padre', 'muestra.jpg', 120, 70, 'publico', '2022-06-08 00:24:23'),
(36, 'Llavero polímero ', 'muestra.jpg', 150, 55, 'publico', '2022-06-08 00:23:54'),
(37, 'Mapas', 'muestra.jpg', 10, 4, 'publico', '2022-05-31 14:43:05'),
(38, 'Marcador al agua', 'muestra.jpg', 70, 47, 'publico', '2022-05-31 14:46:11'),
(39, 'Marcador Filgo por 6', 'muestra.jpg', 140, 120, 'publico', '2022-05-31 14:51:47'),
(40, 'Marcador permanente', 'muestra.jpg', 80, 50, 'publico', '2022-05-31 14:45:56'),
(41, 'Mate polímero', 'muestra.jpg', 750, 630, 'publico', '2022-06-02 00:15:05'),
(42, 'Ojalillo', 'muestra.jpg', 15, 7, 'publico', '2022-05-31 14:43:19'),
(43, 'Papel de regalo brilloso', 'muestra.jpg', 40, 25, 'publico', '2022-06-08 21:07:17'),
(44, 'Papel de regalo grande', 'muestra.jpg', 70, 40, 'publico', '2022-06-08 21:07:03'),
(45, 'Porta retrato feliz día', 'muestra.jpg', 300, 170, 'publico', '2022-06-02 00:15:42'),
(46, 'Porta retrato madera ', 'muestra.jpg', 250, 160, 'publico', '2022-06-02 00:16:02'),
(47, 'Porta SUBE', 'muestra.jpg', 100, 75, 'publico', '2022-05-31 14:50:02'),
(48, 'Portaminas', 'muestra.jpg', 100, 55, 'publico', '2022-05-31 14:44:08'),
(49, 'regla flexible corta', 'muestra.jpg', 60, 40, 'publico', '2022-05-30 22:44:15'),
(50, 'Repuesto 3 carpeta ', 'muestra.jpg', 50, 25, 'publico', '2022-05-31 14:47:23'),
(51, 'Repuesto 5', 'muestra.jpg', 120, 98, 'publico', '2022-05-31 14:47:43'),
(52, 'Repuesto 6 El Nene', 'muestra.jpg', 240, 180, 'publico', '2022-05-31 14:45:41'),
(53, 'Repuesto 6 Miguel Angel', 'muestra.jpg', 130, 70, 'publico', '2022-05-31 14:48:30'),
(54, 'sacapuntas', 'muestra.jpg', 40, 20, 'publico', '2022-05-30 22:44:58'),
(55, 'separadores', 'muestra.jpg', 50, 35, 'publico', '2022-05-30 22:47:26'),
(56, 'Set de geometría por 4 piezas', 'muestra.jpg', 150, 70, 'publico', '2022-05-30 22:46:53'),
(57, 'Set de mate día del padre', 'muestra.jpg', 800, 550, 'publico', '2022-06-08 00:24:39'),
(58, 'Silicona 100 ml.', 'muestra.jpg', 280, 260, 'publico', '2022-05-31 14:51:09'),
(59, 'Tabla periódica', 'muestra.jpg', 80, 45, 'publico', '2022-05-31 14:46:55'),
(60, 'Taza de cerámica', 'muestra.jpg', 950, 570, 'publico', '2022-06-01 13:31:56'),
(61, 'Taza polímero asa común ', 'muestra.jpg', 450, 240, 'publico', '2022-06-01 13:31:19'),
(62, 'Taza polímero asa de corazón ', 'muestra.jpg', 450, 250, 'publico', '2022-06-01 13:31:38'),
(63, 'Tela mandala por 3 ', 'muestra.jpg', 330, 330, 'privado', '2022-05-31 22:23:53'),
(64, 'Tela provenzal para cucha por metro', 'muestra.jpg', 760, 460, 'publico', '2022-05-31 22:24:36'),
(65, 'Tela tropical mecánico por metro', 'muestra.jpg', 550, 190, 'publico', '2022-05-31 22:25:04'),
(66, 'Yerbero', 'muestra.jpg', 550, 430, 'publico', '2022-06-02 00:13:08'),
(67, 'Alcancía', 'muestra.jpg', 480, 350, 'publico', '2022-08-20 21:42:23'),
(68, 'Vaso con tapa (térmico)', 'muestra.jpg', 700, 560, 'publico', '2022-08-20 21:54:47'),
(71, 'Vaso con pico', 'muestra.jpg', 550, 400, 'publico', '2022-08-23 13:33:02'),
(72, 'Cuchara', 'muestra.jpg', 120, 50, 'publico', '2022-08-23 22:22:21'),
(73, 'Cazuelita', 'muestra.jpg', 280, 160, 'publico', '2022-08-23 22:28:05'),
(79, 'Encendedor', 'muestra.jpg', 100, 55, 'publico', '2022-09-19 14:05:55'),
(80, 'Borrador', 'muestra.jpg', 40, 23, 'publico', '2022-09-19 14:08:54'),
(83, 'Collar chico', 'muestra.jpg', 250, 180, 'publico', '2022-09-20 15:10:30'),
(84, 'Identificador', 'muestra.jpg', 200, 55, 'publico', '2022-09-20 22:09:09'),
(85, 'Collar y correa grueso', 'muestra.jpg', 800, 450, 'publico', '2022-09-20 22:10:45'),
(86, 'Invisible', 'muestra.jpg', 200, 140, 'publico', '2022-09-21 23:39:27'),
(87, 'Labiales Argentina', 'muestra.jpg', 320, 243, 'publico', '2022-09-22 22:08:55'),
(88, 'Pintura Argentina', 'muestra.jpg', 260, 180, 'publico', '2022-09-22 22:09:14'),
(89, 'Cuadro Feliz día', 'muestra.jpg', 280, 130, 'publico', '2022-09-23 13:35:11'),
(90, 'Esmalte duo argentina', 'muestra.jpg', 320, 243, 'publico', '2022-09-23 21:14:00'),
(91, 'Pinta cara Argentina', 'muestra.jpg', 320, 243, 'publico', '2022-09-23 21:15:02'),
(92, 'Pinta cara Argentina blister', 'muestra.jpg', 260, 138, 'publico', '2022-09-23 21:16:27'),
(93, 'Brillantina', 'muestra.jpg', 50, 34, 'publico', '2022-09-23 21:20:59'),
(94, 'Marcador lavable Ezco ', 'muestra.jpg', 240, 190, 'publico', '2022-09-23 21:21:25'),
(95, 'Pretal más correa chica', 'muestra.jpg', 250, 350, 'publico', '2022-09-24 21:10:47'),
(96, 'Llavero madera', 'muestra.jpg', 120, 23, 'publico', '2022-09-24 21:13:10'),
(97, 'Collar cuero con puas', 'muestra.jpg', 450, 300, 'publico', '2022-09-24 22:00:45'),
(98, 'Camiseta Argentina Talle 3', 'muestra.jpg', 800, 450, 'publico', '2022-09-25 00:29:50'),
(99, 'Camiseta Argentina Talle 4', 'muestra.jpg', 900, 550, 'publico', '2022-09-25 00:30:30'),
(100, 'Yoyo', 'muestra.jpg', 200, 120, 'publico', '2022-09-26 21:58:06'),
(101, 'Camiseta Argentina Talle 5', 'muestra.jpg', 1000, 850, 'publico', '2022-09-30 13:36:01'),
(102, 'Pretal talle 3', 'muestra.jpg', 450, 300, 'privado', '2022-10-01 22:18:47'),
(103, 'Pretal más correa mediana', 'muestra.jpg', 450, 350, 'privado', '2022-10-03 23:36:07'),
(104, 'Marcador permanente Filgo', 'muestra.jpg', 100, 80, 'publico', '2022-10-04 14:32:23'),
(105, 'Birome color ', 'muestra.jpg', 40, 23, 'publico', '2022-10-04 14:33:13'),
(106, 'Corrector', 'muestra.jpg', 120, 80, 'publico', '2022-10-04 14:38:54'),
(107, 'Pistolita de agua', 'muestra.jpg', 100, 30, 'privado', '2022-10-04 14:40:11'),
(110, 'Pretal más correa talle 3', 'muestra.jpg', 800, 450, 'privado', '2022-10-07 17:05:00'),
(111, 'Papel Crepé', 'muestra.jpg', 70, 40, 'publico', '2022-10-14 22:32:14'),
(112, 'Goma Eva', 'muestra.jpg', 150, 120, 'publico', '2022-10-14 22:32:32'),
(113, 'Birome común azul ABC', 'muestra.jpg', 40, 30, 'privado', '2022-10-14 23:51:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(133) NOT NULL,
  `correo` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `clave` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `rango` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
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
  `producto` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `cantidad` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `unitario` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `total` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
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
(77, 'Birome común azul ABC', '2', '40', '80', '2022-10-14 20:52:01');

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
  MODIFY `id` int(133) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(133) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(133) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
