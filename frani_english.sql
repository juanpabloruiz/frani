-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: mariadb
-- Tiempo de generación: 17-11-2025 a las 03:35:25
-- Versión del servidor: 11.8.4-MariaDB-ubu2404
-- Versión de PHP: 8.3.26

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
-- Estructura de tabla para la tabla `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `code` char(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `cat` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL,
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `posts`
--

INSERT INTO `posts` (`id`, `code`, `title`, `cost`, `price`, `stock`, `cat`, `created`, `modified`) VALUES
(1, '234235345345', 'Birome', 100.00, 200.10, 10, 3, '2025-11-15 17:59:32', NULL),
(2, '35345345343', 'Cuaderno', 260.45, 530.45, 20, 3, '2025-11-15 18:35:57', NULL),
(3, '4445555555', 'Carpeta A4', NULL, 1000.00, NULL, NULL, '2025-11-15 22:00:53', NULL),
(4, '1111111112222', 'Borradores', NULL, 200.10, NULL, NULL, '2025-11-15 22:31:00', NULL),
(5, '34343434343', 'Perfumes', NULL, 2100.10, NULL, NULL, '2025-11-15 23:27:51', NULL),
(6, '555555555', 'Cartulina roja', NULL, 234.20, NULL, NULL, '2025-11-15 23:30:29', NULL),
(7, '999999999', 'Medias', NULL, 123.00, NULL, NULL, '2025-11-15 23:54:34', NULL),
(8, '387238426873', 'Láminas', NULL, 300.00, NULL, NULL, '2025-11-17 00:49:36', NULL),
(9, '348934539845', 'Holass', NULL, 200.10, NULL, NULL, '2025-11-17 01:38:19', NULL),
(10, '666667777777', 'Hojas A5', NULL, 3100.00, NULL, NULL, '2025-11-17 02:00:53', NULL),
(11, '555555', 'sdfasdfasdf', NULL, 4000.00, NULL, NULL, '2025-11-17 02:02:13', NULL),
(12, '3333', 'cfgcfgh', NULL, 34534.00, NULL, NULL, '2025-11-17 02:35:19', NULL),
(13, '121212', 'dfdfgdf', NULL, 23423.00, NULL, NULL, '2025-11-17 02:41:13', NULL),
(14, '2121212', 'dfdf', NULL, 234234.00, NULL, NULL, '2025-11-17 02:54:35', NULL),
(15, '33333', 'pablo', NULL, 4234.00, NULL, NULL, '2025-11-17 02:55:46', NULL),
(16, '888886666', 'erererere', NULL, 234243.00, NULL, NULL, '2025-11-17 02:56:26', NULL),
(17, '6666', 'hola', NULL, 345345.00, NULL, NULL, '2025-11-17 02:57:19', NULL),
(18, '2434', 'dgsdfg', NULL, 23434.00, NULL, NULL, '2025-11-17 03:00:26', NULL),
(19, '11111', 'dfgf', NULL, 452345.00, NULL, NULL, '2025-11-17 03:33:01', NULL),
(20, '22222', 'dfgf', NULL, 1115.00, NULL, NULL, '2025-11-17 03:33:49', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
