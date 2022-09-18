-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 18-09-2022 a las 18:18:49
-- Versión del servidor: 8.0.30-0ubuntu0.20.04.2
-- Versión de PHP: 7.4.3

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
  `id` int NOT NULL,
  `producto` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `precio` double NOT NULL,
  `costo` double NOT NULL,
  `status` double NOT NULL,
  `registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `producto`, `precio`, `costo`, `status`, `registro`) VALUES
(1, 'Afiche', 80, 60, 1, '2022-06-08 21:06:46'),
(2, 'Aro metálico', 20, 9, 1, '2022-05-31 14:44:27'),
(3, 'Auricular', 200, 120, 1, '2022-05-31 14:50:22'),
(4, 'Birome Bic', 70, 38, 1, '2022-05-30 22:46:35'),
(5, 'Birome borrable', 160, 140, 1, '2022-05-31 14:42:24'),
(6, 'Bolsa de regalo Kraft ', 70, 46, 1, '2022-06-08 21:37:32'),
(7, 'borrador', 20, 13, 1, '2022-05-30 22:44:44'),
(8, 'Candado para mochila', 100, 50, 1, '2022-05-31 14:49:45'),
(9, 'Carpeta Número 5', 180, 110, 1, '2022-05-31 14:48:51'),
(10, 'Carpeta Número 5 con dibujos', 230, 180, 1, '2022-05-31 14:49:17'),
(11, 'Carpeta tapa transparente A4', 120, 60, 1, '2022-05-31 14:51:29'),
(12, 'Cartuchera', 80, 50, 1, '2022-05-30 22:43:59'),
(13, 'Cartulina blanca', 40, 18, 1, '2022-05-31 14:43:53'),
(14, 'Cartulina color', 60, 40, 1, '2022-05-31 14:43:40'),
(15, 'Chopp polímero', 1000, 690, 1, '2022-06-02 00:14:04'),
(16, 'Cinta pack ancha', 200, 170, 1, '2022-05-31 14:48:07'),
(17, 'Cinta pack fina', 180, 150, 1, '2022-06-09 16:03:19'),
(18, 'Compás general', 23, 12, 1, '2022-05-31 03:13:48'),
(19, 'Copos gomaespuma', 550, 550, 1, '2022-05-31 22:23:02'),
(20, 'corrector chico', 50, 35, 1, '2022-05-30 22:47:43'),
(21, 'Cuadernillo', 250, 190, 1, '2022-05-31 14:49:31'),
(22, 'cuaderno 24 hojas', 50, 30, 1, '2022-05-30 22:45:24'),
(23, 'cuaderno Gloria 84 hojas', 140, 96, 1, '2022-05-30 22:45:51'),
(24, 'Cuaderno Rivadavia 98 hojas', 500, 0, 1, '2022-06-09 22:12:32'),
(25, 'Etiquetas por 5', 25, 9, 1, '2022-05-30 22:46:15'),
(26, 'fibras por 12', 150, 100, 1, '2022-05-30 22:47:10'),
(27, 'Folio tamaño carpeta N° 3', 25, 10, 1, '2022-05-31 14:42:49'),
(28, 'Hoja canson color ', 90, 60, 1, '2022-05-31 14:50:52'),
(29, 'Hoja de calcar ', 100, 70, 1, '2022-05-31 14:50:39'),
(30, 'Jarro con tapa', 520, 480, 1, '2022-06-02 00:14:35'),
(31, 'lápices cortos de colores', 120, 77, 1, '2022-05-30 22:43:05'),
(32, 'lápices largos de colores', 220, 120, 1, '2022-05-30 22:43:44'),
(33, 'lápiz negro', 20, 8, 1, '2022-05-30 22:44:30'),
(34, 'Llavero destapador', 150, 90, 1, '2022-06-08 00:24:10'),
(35, 'Llavero día del padre', 120, 70, 1, '2022-06-08 00:24:23'),
(36, 'Llavero polímero ', 90, 50, 1, '2022-06-08 00:23:54'),
(37, 'Mapas', 10, 4, 1, '2022-05-31 14:43:05'),
(38, 'Marcador al agua', 70, 47, 1, '2022-05-31 14:46:11'),
(39, 'Marcador Filgo por 6', 140, 120, 1, '2022-05-31 14:51:47'),
(40, 'Marcador permanente', 80, 50, 1, '2022-05-31 14:45:56'),
(41, 'Mate polímero', 650, 470, 1, '2022-06-02 00:15:05'),
(42, 'Ojalillo', 15, 7, 1, '2022-05-31 14:43:19'),
(43, 'Papel de regalo brilloso', 40, 25, 1, '2022-06-08 21:07:17'),
(44, 'Papel de regalo grande', 70, 40, 1, '2022-06-08 21:07:03'),
(45, 'Porta retrato feliz día', 300, 170, 1, '2022-06-02 00:15:42'),
(46, 'Porta retrato madera ', 250, 160, 1, '2022-06-02 00:16:02'),
(47, 'Porta SUBE', 100, 75, 1, '2022-05-31 14:50:02'),
(48, 'Portaminas', 100, 55, 1, '2022-05-31 14:44:08'),
(49, 'regla flexible corta', 60, 40, 1, '2022-05-30 22:44:15'),
(50, 'Repuesto 3 carpeta ', 50, 25, 1, '2022-05-31 14:47:23'),
(51, 'Repuesto 5', 80, 50, 1, '2022-05-31 14:47:43'),
(52, 'Repuesto 6 El Nene', 240, 180, 1, '2022-05-31 14:45:41'),
(53, 'Repuesto 6 Miguel Angel', 130, 70, 1, '2022-05-31 14:48:30'),
(54, 'sacapuntas', 40, 20, 1, '2022-05-30 22:44:58'),
(55, 'separadores', 50, 35, 1, '2022-05-30 22:47:26'),
(56, 'Set de geometría por 4 piezas', 150, 70, 1, '2022-05-30 22:46:53'),
(57, 'Set de mate día del padre', 800, 550, 1, '2022-06-08 00:24:39'),
(58, 'Silicona 100 ml.', 280, 260, 1, '2022-05-31 14:51:09'),
(59, 'Tabla periódica', 80, 45, 1, '2022-05-31 14:46:55'),
(60, 'Taza de cerámica', 800, 450, 1, '2022-06-01 13:31:56'),
(61, 'Taza polímero asa común ', 350, 180, 1, '2022-06-01 13:31:19'),
(62, 'Taza polímero asa de corazón ', 400, 200, 1, '2022-06-01 13:31:38'),
(63, 'Tela mandala por 3 ', 330, 330, 1, '2022-05-31 22:23:53'),
(64, 'Tela provenzal para cucha por metro', 760, 460, 1, '2022-05-31 22:24:36'),
(65, 'Tela tropical mecánico por metro', 550, 190, 1, '2022-05-31 22:25:04'),
(66, 'Yerbero', 480, 365, 1, '2022-06-02 00:13:08'),
(67, 'Alcancía', 480, 350, 1, '2022-08-20 21:42:23'),
(68, 'Vaso con tapa (térmico)', 700, 560, 1, '2022-08-20 21:54:47'),
(71, 'Vaso con pico', 550, 400, 1, '2022-08-23 13:33:02'),
(72, 'Cuchara', 120, 50, 1, '2022-08-23 22:22:21'),
(73, 'Cazuelita', 280, 160, 1, '2022-08-23 22:28:05'),
(86, 'aaa', 34, 35, 0, '2022-09-18 20:58:05'),
(87, 'aaaa', 23, 24, 0, '2022-09-18 21:01:59'),
(88, 'aaaaa', 23, 25, 0, '2022-09-18 21:02:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `correo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `clave` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `rango` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `correo`, `clave`, `rango`, `registro`) VALUES
(1, 'soledadm77@gmail.com', 'corazon', 'admin', '2022-05-29 20:18:50'),
(2, 'pabloruiz1980@gmail.com', 'soledad', 'admin', '2022-05-30 13:04:30');

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
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
