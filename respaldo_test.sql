/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.6-MariaDB, for debian-linux-gnu (aarch64)
--
-- Host: db    Database: frani
-- ------------------------------------------------------
-- Server version	11.4.13-MariaDB-ubu2404

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `agregado` timestamp NOT NULL DEFAULT current_timestamp(),
  `modificado` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_categorias_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES
(1,'General','2026-08-29 01:20:46',NULL),
(2,'Librería','2026-08-31 15:20:22',NULL),
(3,'Regalos','2026-08-31 15:38:35',NULL),
(4,'Sublimación','2026-08-31 15:40:21',NULL);
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `facturas`
--

DROP TABLE IF EXISTS `facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `facturas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `total` int(11) NOT NULL DEFAULT 0,
  `efectivo` int(11) DEFAULT NULL,
  `transferencia` int(11) DEFAULT NULL,
  `deuda` int(11) DEFAULT NULL,
  `detalle` text NOT NULL,
  `agregado` timestamp NOT NULL DEFAULT current_timestamp(),
  `modificado` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facturas`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `facturas` WRITE;
/*!40000 ALTER TABLE `facturas` DISABLE KEYS */;
INSERT INTO `facturas` VALUES
(1,'Pablo',24500,4500,20000,NULL,'Abrecorcho (1 x 2500.00), Birome (11 x 2000.00)','2026-08-31 15:21:39',NULL),
(2,'Jorge',46000,NULL,40000,6000,'Aro de luz (1 x 27000.00), Balanza (1 x 7000.00), Cartulina (3 x 4000.00)','2026-08-31 20:58:15',NULL);
/*!40000 ALTER TABLE `facturas` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `producto` varchar(200) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `costo` int(11) NOT NULL DEFAULT 0,
  `precio` int(11) NOT NULL DEFAULT 0,
  `stock` int(11) DEFAULT NULL,
  `id_categoria` int(10) unsigned NOT NULL,
  `agregado` timestamp NOT NULL DEFAULT current_timestamp(),
  `modificado` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_productos_id_categoria` (`id_categoria`),
  CONSTRAINT `fk_productos_categorias` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=228 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES
(1,'Autox4 mounstro',NULL,NULL,6000,8000,NULL,1,'2026-08-29 01:23:50',NULL),
(2,'Abre corcho imán',NULL,NULL,2500,3000,NULL,1,'2026-08-29 01:23:50',NULL),
(3,'Abrecorcho',NULL,NULL,1700,2500,NULL,1,'2026-08-29 01:23:50',NULL),
(4,'Aceites de horno',NULL,NULL,1500,2500,NULL,1,'2026-08-29 01:23:50',NULL),
(5,'Acrílicos',NULL,NULL,1100,1500,NULL,1,'2026-08-29 01:23:50',NULL),
(6,'Afeitadora',NULL,NULL,7000,10000,NULL,1,'2026-08-29 01:23:50',NULL),
(7,'Alfiler de gancho blister 4 medidas',NULL,NULL,1800,2200,NULL,1,'2026-08-29 01:23:50',NULL),
(8,'Aro de luz',NULL,NULL,23000,27000,NULL,1,'2026-08-29 01:23:50',NULL),
(9,'Aro luz celular',NULL,NULL,4000,6500,NULL,1,'2026-08-29 01:23:50',NULL),
(10,'Aspiradora portatil',NULL,NULL,7000,9700,NULL,1,'2026-08-29 01:23:50',NULL),
(11,'Atomizador para perfume',NULL,NULL,1500,2000,NULL,1,'2026-08-29 01:23:50',NULL),
(12,'Auricular vincha',NULL,NULL,8500,10500,NULL,1,'2026-08-29 01:23:50',NULL),
(13,'Autocebante',NULL,NULL,11000,16000,NULL,1,'2026-08-29 01:23:50',NULL),
(14,'Balanza',NULL,NULL,4800,7000,NULL,1,'2026-08-29 01:23:50',NULL),
(15,'Bandera Argentina tela 40x60',NULL,NULL,1900,2500,NULL,1,'2026-08-29 01:23:50',NULL),
(16,'Bandera Argentina tela 60x90',NULL,NULL,3500,4500,NULL,1,'2026-08-29 01:23:50',NULL),
(17,'Barbie articulada',NULL,NULL,2500,5000,NULL,1,'2026-08-29 01:23:50',NULL),
(18,'Barbie con zapatos',NULL,NULL,3800,5800,NULL,1,'2026-08-29 01:23:50',NULL),
(19,'Bastón caballero',NULL,NULL,1500,2000,NULL,1,'2026-08-29 01:23:50',NULL),
(20,'Bebé que rie',NULL,NULL,6800,9800,NULL,1,'2026-08-29 01:23:50',NULL),
(21,'Botella capibara con sensor',NULL,NULL,7500,15000,NULL,1,'2026-08-29 01:23:50',NULL),
(22,'Botella cuernito',NULL,NULL,8500,12000,NULL,1,'2026-08-29 01:23:50',NULL),
(23,'Botella de AFA con manija',NULL,NULL,10500,15000,NULL,1,'2026-08-29 01:23:50',NULL),
(24,'Botella didáctica',NULL,NULL,14000,17000,NULL,1,'2026-08-29 01:23:50',NULL),
(25,'Botella infantil cuernito',NULL,NULL,8500,12000,NULL,1,'2026-08-29 01:23:50',NULL),
(26,'Botella Kuromi',NULL,NULL,10000,15000,NULL,1,'2026-08-29 01:23:50',NULL),
(27,'Botella Quencher Boca River',NULL,NULL,10000,15000,NULL,1,'2026-08-29 01:23:50',NULL),
(28,'Botella Stitch con llavero',NULL,NULL,11000,15000,NULL,1,'2026-08-29 01:23:50',NULL),
(29,'Broches hawaianos',NULL,NULL,1100,1700,NULL,1,'2026-08-29 01:23:50',NULL),
(30,'Bufanda',NULL,NULL,1950,2500,NULL,1,'2026-08-29 01:23:50',NULL),
(31,'Cactus parlante',NULL,NULL,5800,13000,NULL,1,'2026-08-29 01:23:50',NULL),
(32,'Cafetero digital',NULL,NULL,9000,13000,NULL,1,'2026-08-29 01:23:50',NULL),
(33,'Caja de chinches dorada 50 unidades',NULL,NULL,390,500,NULL,1,'2026-08-29 01:23:50',NULL),
(34,'Canastita de agujas CBX',NULL,NULL,380,500,NULL,1,'2026-08-29 01:23:50',NULL),
(35,'Cartuchera 2 pisos',NULL,NULL,6500,11000,NULL,1,'2026-08-29 01:23:50',NULL),
(36,'Cartuchera metal colectivo',NULL,NULL,4000,6500,NULL,1,'2026-08-29 01:23:50',NULL),
(37,'Cartulina bandera',NULL,NULL,800,950,NULL,1,'2026-08-29 01:23:50',NULL),
(38,'Celular juguete con sonido',NULL,NULL,1500,2500,NULL,1,'2026-08-29 01:23:50',NULL),
(39,'Chispero USB',NULL,NULL,4000,7000,NULL,1,'2026-08-29 01:23:50',NULL),
(40,'Cinta pack',NULL,NULL,1200,2500,NULL,1,'2026-08-29 01:23:50',NULL),
(41,'Cinta pack ancha',NULL,NULL,1100,2500,NULL,1,'2026-08-29 01:23:50',NULL),
(42,'Coche bebé',NULL,NULL,6000,9000,NULL,1,'2026-08-29 01:23:50',NULL),
(43,'Coche de supermercado',NULL,NULL,5500,9000,NULL,1,'2026-08-29 01:23:50',NULL),
(44,'Cocina con dispenser',NULL,NULL,5500,7800,NULL,1,'2026-08-29 01:23:50',NULL),
(45,'Compás con portaminas',NULL,NULL,1500,1800,NULL,1,'2026-08-29 01:23:50',NULL),
(46,'Compás Koby plástico',NULL,NULL,1200,1500,NULL,1,'2026-08-29 01:23:50',NULL),
(47,'Corneta Argentina chica 16 cm',NULL,NULL,600,1500,NULL,1,'2026-08-29 01:23:50',NULL),
(48,'Corona de plumas con luz',NULL,NULL,950,2500,NULL,1,'2026-08-29 01:23:50',NULL),
(49,'Corrector Simba',NULL,NULL,400,550,NULL,1,'2026-08-29 01:23:50',NULL),
(50,'Cortina cotillón lluvia metalizada',NULL,NULL,950,1500,NULL,1,'2026-08-29 01:23:50',NULL),
(51,'Cubo Rubik',NULL,NULL,1500,2400,NULL,1,'2026-08-29 01:23:50',NULL),
(52,'Encendedor eléctrico',NULL,NULL,4000,7000,NULL,1,'2026-08-29 01:23:50',NULL),
(53,'Especiero acrílico',NULL,NULL,9500,10000,NULL,1,'2026-08-29 01:23:50',NULL),
(54,'Folio N° 3x10',NULL,NULL,380,100,NULL,1,'2026-08-29 01:23:50',NULL),
(55,'Folio oficio x10',NULL,NULL,1100,140,NULL,1,'2026-08-29 01:23:50',NULL),
(56,'Galera caballero',NULL,NULL,700,1200,NULL,1,'2026-08-29 01:23:50',NULL),
(57,'Gallinas',NULL,NULL,8500,15000,NULL,1,'2026-08-29 01:23:50',NULL),
(58,'Globo bandera argentina',NULL,NULL,1200,1600,NULL,1,'2026-08-29 01:23:50',NULL),
(59,'Globo Estrella dorada/plateada',NULL,NULL,1650,2000,NULL,1,'2026-08-29 01:23:50',NULL),
(60,'Globo Felicidades plateado/dorado',NULL,NULL,1200,1900,NULL,1,'2026-08-29 01:23:50',NULL),
(61,'Globo número 16 plata',NULL,NULL,600,950,NULL,1,'2026-08-29 01:23:50',NULL),
(62,'Globos argentina celeste y blanco x 50',NULL,NULL,4300,150,NULL,1,'2026-08-29 01:23:50',NULL),
(63,'Gogos moño Argentina',NULL,NULL,250,400,NULL,1,'2026-08-29 01:23:50',NULL),
(64,'Gorro unicornio levanta orejas',NULL,NULL,5500,9500,NULL,1,'2026-08-29 01:23:50',NULL),
(65,'Guitarra',NULL,NULL,6000,9500,NULL,1,'2026-08-29 01:23:50',NULL),
(66,'Hombre araña chico en caja',NULL,NULL,4000,6500,NULL,1,'2026-08-29 01:23:50',NULL),
(67,'Lámpara de Stitch',NULL,NULL,10000,12500,NULL,1,'2026-08-29 01:23:50',NULL),
(68,'Lapicera capibara',NULL,NULL,2000,3000,NULL,1,'2026-08-29 01:23:50',NULL),
(69,'Lápices Faber Castell',NULL,NULL,3500,4500,NULL,1,'2026-08-29 01:23:50',NULL),
(70,'Linterna eléctrica',NULL,NULL,3500,5000,NULL,1,'2026-08-29 01:23:50',NULL),
(71,'Llaveros Snoopy',NULL,NULL,5500,6500,NULL,1,'2026-08-29 01:23:50',NULL),
(72,'Manta bebé',NULL,NULL,4200,9000,NULL,1,'2026-08-29 01:23:50',NULL),
(73,'Manta cartera Stitch',NULL,NULL,9500,15000,NULL,1,'2026-08-29 01:23:50',NULL),
(74,'Manta lisa plush',NULL,NULL,7000,13500,NULL,1,'2026-08-29 01:23:50',NULL),
(75,'Manta luminosa araña',NULL,NULL,7000,15000,NULL,1,'2026-08-29 01:23:50',NULL),
(76,'Marcador fibrón pizarra',NULL,NULL,600,850,NULL,1,'2026-08-29 01:23:50',NULL),
(77,'Minas 0.5 Stitch',NULL,NULL,300,700,NULL,1,'2026-08-29 01:23:50',NULL),
(78,'Mini quencher con moño',NULL,NULL,9000,13500,NULL,1,'2026-08-29 01:23:50',NULL),
(79,'Mini quencher negro',NULL,NULL,8500,13500,NULL,1,'2026-08-29 01:23:50',NULL),
(80,'Miniquencher',NULL,NULL,9500,13500,NULL,1,'2026-08-29 01:23:50',NULL),
(81,'Mochila Argentina',NULL,NULL,11500,15000,NULL,1,'2026-08-29 01:23:50',NULL),
(82,'Mochila Boca River',NULL,NULL,10500,15500,NULL,1,'2026-08-29 01:23:50',NULL),
(83,'Mochila Wilson',NULL,NULL,8000,14000,NULL,1,'2026-08-29 01:23:50',NULL),
(84,'Molde corazón por 3',NULL,NULL,6000,9500,NULL,1,'2026-08-29 01:23:50',NULL),
(85,'Molde de torta',NULL,NULL,8500,16000,NULL,1,'2026-08-29 01:23:50',NULL),
(86,'Monopatín',NULL,NULL,12000,22000,NULL,1,'2026-08-29 01:23:50',NULL),
(87,'Moñitos de Argentina',NULL,NULL,800,1000,NULL,1,'2026-08-29 01:23:50',NULL),
(88,'Morral de hombre',NULL,NULL,13500,18000,NULL,1,'2026-08-29 01:23:50',NULL),
(89,'Moto didáctica',NULL,NULL,3900,6900,NULL,1,'2026-08-29 01:23:50',NULL),
(90,'Muñeco por 2',NULL,NULL,3500,7000,NULL,1,'2026-08-29 01:23:50',NULL),
(91,'Ojalillos',NULL,NULL,170,350,NULL,1,'2026-08-29 01:23:50',NULL),
(92,'Palito helado',NULL,NULL,1250,1500,NULL,1,'2026-08-29 01:23:50',NULL),
(93,'Papel crepé',NULL,NULL,320,650,NULL,1,'2026-08-29 01:23:50',NULL),
(94,'Paraguas infantil',NULL,NULL,4800,7500,NULL,1,'2026-08-29 01:23:50',NULL),
(95,'Pareja oso panda',NULL,NULL,14000,17000,NULL,1,'2026-08-29 01:23:50',NULL),
(96,'Pava eléctrica',NULL,NULL,8500,12000,NULL,1,'2026-08-29 01:23:50',NULL),
(97,'Pechera dama mochila',NULL,NULL,15000,18000,NULL,1,'2026-08-29 01:23:50',NULL),
(98,'Peinetón dama antigua',NULL,NULL,300,600,NULL,1,'2026-08-29 01:23:50',NULL),
(99,'Peinetón y mantilla',NULL,NULL,2200,2700,NULL,1,'2026-08-29 01:23:50',NULL),
(100,'Pelota River Boca',NULL,NULL,6000,9000,NULL,1,'2026-08-29 01:23:50',NULL),
(101,'Peluche eco con manta',NULL,NULL,7500,15000,NULL,1,'2026-08-29 01:23:50',NULL),
(102,'Peluche que respira',NULL,NULL,13500,23000,NULL,1,'2026-08-29 01:23:50',NULL),
(103,'Picadora chica',NULL,NULL,2500,4500,NULL,1,'2026-08-29 01:23:50',NULL),
(104,'Picadora Grande',NULL,NULL,4000,8500,NULL,1,'2026-08-29 01:23:50',NULL),
(105,'Picadora mediana',NULL,NULL,3500,7000,NULL,1,'2026-08-29 01:23:50',NULL),
(106,'Pingüino sacapelusa',NULL,NULL,3000,5000,NULL,1,'2026-08-29 01:23:50',NULL),
(107,'Pintacara mundial',NULL,NULL,950,2500,NULL,1,'2026-08-29 01:23:50',NULL),
(108,'Pintura bandera',NULL,NULL,3000,3800,NULL,1,'2026-08-29 01:23:50',NULL),
(109,'Pintura de labios por 2',NULL,NULL,1200,1800,NULL,1,'2026-08-29 01:23:50',NULL),
(110,'Piso didáctico',NULL,NULL,1700,3800,NULL,1,'2026-08-29 01:23:50',NULL),
(111,'Pistola naranja policia',NULL,NULL,3900,6900,NULL,1,'2026-08-29 01:23:50',NULL),
(112,'Plasticola STA 30 gr',NULL,NULL,390,500,NULL,1,'2026-08-29 01:23:50',NULL),
(113,'Plastificado en frio',NULL,NULL,1900,2500,NULL,1,'2026-08-29 01:23:50',NULL),
(114,'Reloj Stitch',NULL,NULL,8000,14000,NULL,1,'2026-08-29 01:23:50',NULL),
(115,'Repuesto arqueador',NULL,NULL,1200,1900,NULL,1,'2026-08-29 01:23:50',NULL),
(116,'Repuesto de dibujo N° 6',NULL,NULL,1200,1700,NULL,1,'2026-08-29 01:23:50',NULL),
(117,'Repuesto dibujo N° 5 8 hojas',NULL,NULL,650,1200,NULL,1,'2026-08-29 01:23:50',NULL),
(118,'Repuesto dibujo N°3',NULL,NULL,520,850,NULL,1,'2026-08-29 01:23:50',NULL),
(119,'Resaltador Sky chato',NULL,NULL,450,750,NULL,1,'2026-08-29 01:23:50',NULL),
(120,'Sábana 2 plazas',NULL,NULL,16500,22000,NULL,1,'2026-08-29 01:23:50',NULL),
(121,'Sacapelusas por 2',NULL,NULL,5200,7000,NULL,1,'2026-08-29 01:23:50',NULL),
(122,'Saphirus textil 250 ml',NULL,NULL,3800,4300,NULL,1,'2026-08-29 01:23:50',NULL),
(123,'Secador de zapatillas',NULL,NULL,11000,16000,NULL,1,'2026-08-29 01:23:50',NULL),
(124,'Set aguja hilos',NULL,NULL,1500,1900,NULL,1,'2026-08-29 01:23:50',NULL),
(125,'Set de arte',NULL,NULL,14000,20000,NULL,1,'2026-08-29 01:23:50',NULL),
(126,'Set de geometría 30 cm transparente x4',NULL,NULL,950,1500,NULL,1,'2026-08-29 01:23:50',NULL),
(127,'Set de geometría blister con calculadora',NULL,NULL,6000,7500,NULL,1,'2026-08-29 01:23:50',NULL),
(128,'Set por 6 autos',NULL,NULL,6000,7800,NULL,1,'2026-08-29 01:23:50',NULL),
(129,'Silicona fina transparente en barra',NULL,NULL,280,350,NULL,1,'2026-08-29 01:23:50',NULL),
(130,'Silicona líquida Koby 50 gr',NULL,NULL,1400,1600,NULL,1,'2026-08-29 01:23:50',NULL),
(131,'Silicona líquida Sky 30 ml.',NULL,NULL,850,1200,NULL,1,'2026-08-29 01:23:50',NULL),
(132,'Sonajero',NULL,NULL,3800,6800,NULL,1,'2026-08-29 01:23:50',NULL),
(133,'Sorbete de metal con silicona',NULL,NULL,950,2500,NULL,1,'2026-08-29 01:23:50',NULL),
(134,'Strass',NULL,NULL,260,450,NULL,1,'2026-08-29 01:23:50',NULL),
(135,'Tabla periódica',NULL,NULL,850,1200,NULL,1,'2026-08-29 01:23:50',NULL),
(136,'Taza batidora',NULL,NULL,10500,14000,NULL,1,'2026-08-29 01:23:50',NULL),
(137,'Taza batidora Boca River',NULL,NULL,5500,10000,NULL,1,'2026-08-29 01:23:50',NULL),
(138,'Taza Boca River con plato y cuchara',NULL,NULL,5500,9000,NULL,1,'2026-08-29 01:23:50',NULL),
(139,'Taza con calentador',NULL,NULL,7500,12000,NULL,1,'2026-08-29 01:23:50',NULL),
(140,'Taza con plato y cuchara para papá',NULL,NULL,5000,8000,NULL,1,'2026-08-29 01:23:50',NULL),
(141,'Taza para papá',NULL,NULL,3500,5000,NULL,1,'2026-08-29 01:23:50',NULL),
(142,'Taza Stitch media manija',NULL,NULL,10000,12500,NULL,1,'2026-08-29 01:23:50',NULL),
(143,'Termito con 3 tazas',NULL,NULL,7500,12000,NULL,1,'2026-08-29 01:23:50',NULL),
(144,'Termo Boca River con mate y bombilla',NULL,NULL,21000,26000,NULL,1,'2026-08-29 01:23:50',NULL),
(145,'Termo bomba 2 litros',NULL,NULL,19500,25000,NULL,1,'2026-08-29 01:23:50',NULL),
(146,'Termo bomba chico',NULL,NULL,12000,20000,NULL,1,'2026-08-29 01:23:50',NULL),
(147,'Termo gris media manija 1 litro',NULL,NULL,8500,12000,NULL,1,'2026-08-29 01:23:50',NULL),
(148,'Termo gris medio litro',NULL,NULL,9000,12000,NULL,1,'2026-08-29 01:23:50',NULL),
(149,'Toallón con diseño',NULL,NULL,3800,6000,NULL,1,'2026-08-29 01:23:50',NULL),
(150,'Tren juguete',NULL,NULL,7500,9500,NULL,1,'2026-08-29 01:23:50',NULL),
(151,'Trenzadora',NULL,NULL,5500,7000,NULL,1,'2026-08-29 01:23:50',NULL),
(152,'Trípode',NULL,NULL,5000,7500,NULL,1,'2026-08-29 01:23:50',NULL),
(153,'Unipox',NULL,NULL,2000,2800,NULL,1,'2026-08-29 01:23:50',NULL),
(154,'Vaso cafetero con sensor',NULL,NULL,8000,12000,NULL,1,'2026-08-29 01:23:50',NULL),
(155,'Vaso con parlante Messi',NULL,NULL,14500,17000,NULL,1,'2026-08-29 01:23:50',NULL),
(156,'Vaso con sorbete boca river',NULL,NULL,3000,3500,NULL,1,'2026-08-29 01:23:50',NULL),
(157,'Vaso conejo con cadena',NULL,NULL,9500,14500,NULL,1,'2026-08-29 01:23:50',NULL),
(158,'Vaso Quencher FIFA con pelota',NULL,NULL,14000,17000,NULL,1,'2026-08-29 01:23:50',NULL),
(159,'Vaso Quencher negro con corazones',NULL,NULL,14000,17000,NULL,1,'2026-08-29 01:23:50',NULL),
(160,'Vaso Quencher Picachu',NULL,NULL,14000,17000,NULL,1,'2026-08-29 01:23:50',NULL),
(161,'Vaso Quencher salchicha',NULL,NULL,9000,15000,NULL,1,'2026-08-29 01:23:50',NULL),
(162,'Vasos con sorbete de vidrio River/Boca',NULL,NULL,3000,4000,NULL,1,'2026-08-29 01:23:50',NULL),
(163,'Vianda eléctrica',NULL,NULL,12000,14000,NULL,1,'2026-08-29 01:23:50',NULL),
(164,'Vincha de Argentina dos banderas',NULL,NULL,2700,3000,NULL,1,'2026-08-29 01:23:50',NULL),
(165,'Vincha de Argentina moños',NULL,NULL,1500,2000,NULL,1,'2026-08-29 01:23:50',NULL),
(166,'Vincha de Argentina rosas',NULL,NULL,2500,3100,NULL,1,'2026-08-29 01:23:50',NULL),
(167,'Vincha de beba',NULL,NULL,900,1200,NULL,1,'2026-08-29 01:23:50',NULL),
(168,'Vincha de beba corona',NULL,NULL,1200,1800,NULL,1,'2026-08-29 01:23:50',NULL),
(169,'Vincha elastizada Argentina',NULL,NULL,650,1000,NULL,1,'2026-08-29 01:23:50',NULL),
(170,'Barra de silicona gruesa',NULL,NULL,350,750,NULL,1,'2026-08-29 01:23:50',NULL),
(171,'Muñeca flaca económica',NULL,NULL,570,1200,NULL,1,'2026-08-29 01:23:50',NULL),
(172,'Pizarra mágica 35x25 cm',NULL,NULL,2300,3400,NULL,1,'2026-08-29 01:23:50',NULL),
(173,'Pizarra nágica 23x18 cm',NULL,NULL,1300,2400,NULL,1,'2026-08-29 01:23:50',NULL),
(174,'Set de peine x6 rosa',NULL,NULL,900,1800,NULL,1,'2026-08-29 01:23:50',NULL),
(175,'Voligoma Neoart 50ml.',NULL,NULL,500,1000,NULL,1,'2026-08-29 01:23:50',NULL),
(176,'Mordillo de mascota 15 cm',NULL,NULL,1700,2500,NULL,1,'2026-08-29 01:23:50',NULL),
(177,'Mordillo de mascota 10cm',NULL,NULL,850,1800,NULL,1,'2026-08-29 01:23:50',NULL),
(178,'Gancho metálico clip x 12 unidades',NULL,NULL,1500,2000,NULL,1,'2026-08-29 01:23:50',NULL),
(179,'Rollo etiqueta de precios',NULL,NULL,300,900,NULL,1,'2026-08-29 01:23:50',NULL),
(180,'Mariposa x12 blister',NULL,NULL,2100,2800,NULL,1,'2026-08-29 01:23:50',NULL),
(181,'Cortina metalizada cuadrada guirnalda p/cumple',NULL,NULL,1300,2100,NULL,1,'2026-08-29 01:23:50',NULL),
(182,'Broches para abrochadora x3 N° 10',NULL,NULL,1150,2100,NULL,1,'2026-08-29 01:23:50',NULL),
(183,'Tizas blancas x12',NULL,NULL,450,1000,NULL,1,'2026-08-29 01:23:50',NULL),
(184,'Aros carpeta N°50 x2',NULL,NULL,450,1100,NULL,1,'2026-08-29 01:23:50',NULL),
(185,'Pulsera ojitos',NULL,NULL,1800,2000,NULL,1,'2026-08-29 01:23:50',NULL),
(186,'Almohadón capi con manta',NULL,NULL,7500,13500,NULL,1,'2026-08-29 01:23:50',NULL),
(187,'Carrito supermercado',NULL,NULL,5500,9500,NULL,1,'2026-08-29 01:23:50',NULL),
(188,'Set valija doctora',NULL,NULL,6500,9500,NULL,1,'2026-08-29 01:23:50',NULL),
(189,'Marcador para pizarra recargable Olami x4',NULL,NULL,3500,4000,NULL,1,'2026-08-29 01:23:50',NULL),
(190,'Roller gel Pop x10',NULL,NULL,4800,5800,NULL,1,'2026-08-29 01:23:50',NULL),
(191,'Roller gel Pop x4',NULL,NULL,2500,2800,NULL,1,'2026-08-29 01:23:50',NULL),
(192,'Abecedario letras goma eva',NULL,NULL,1700,3000,NULL,1,'2026-08-29 01:23:50',NULL),
(193,'Sonajeros en bolsa',NULL,NULL,3800,7500,NULL,1,'2026-08-29 01:23:50',NULL),
(194,'Tren de juguete en caja',NULL,NULL,5500,8500,NULL,1,'2026-08-29 01:23:50',NULL),
(195,'Sacapelusas pingüino',NULL,NULL,3000,5000,NULL,1,'2026-08-29 01:23:50',NULL),
(196,'Muñeca por 2',NULL,NULL,3500,6800,NULL,1,'2026-08-29 01:23:50',NULL),
(197,'Dino lanza humo',NULL,NULL,10000,14000,NULL,1,'2026-08-29 01:23:50',NULL),
(198,'Avión lanza humo',NULL,NULL,7000,12000,NULL,1,'2026-08-29 01:23:50',NULL),
(199,'Dinos en bolsa plásticos',NULL,NULL,4800,7800,NULL,1,'2026-08-29 01:23:50',NULL),
(200,'Auto transformer',NULL,NULL,10500,14000,NULL,1,'2026-08-29 01:23:50',NULL),
(201,'Tortuga chifles en bolsa',NULL,NULL,5000,8000,NULL,1,'2026-08-29 01:23:50',NULL),
(202,'Tren lanza humo',NULL,NULL,10000,14000,NULL,1,'2026-08-29 01:23:50',NULL),
(203,'Hombre araña con máscara y garra',NULL,NULL,11000,14500,NULL,1,'2026-08-29 01:23:50',NULL),
(204,'Set dentista',NULL,NULL,4800,7900,NULL,1,'2026-08-29 01:23:50',NULL),
(205,'Aros capibara encastre',NULL,NULL,7000,9800,NULL,1,'2026-08-29 01:23:50',NULL),
(206,'Camión con balde',NULL,NULL,5500,7500,NULL,1,'2026-08-29 01:23:50',NULL),
(207,'Camión chico x2',NULL,NULL,3500,4800,NULL,1,'2026-08-29 01:23:50',NULL),
(208,'Hombre araña lanzador',NULL,NULL,6000,8500,NULL,1,'2026-08-29 01:23:50',NULL),
(209,'Bolsa boxeo',NULL,NULL,7500,9500,NULL,1,'2026-08-29 01:23:50',NULL),
(210,'Pelotas chicas N°2',NULL,NULL,4000,7000,NULL,1,'2026-08-29 01:23:50',NULL),
(211,'Squishi',NULL,NULL,1000,2000,NULL,1,'2026-08-29 01:23:50',NULL),
(212,'Bolsa tambor sonajeros varios',NULL,NULL,6800,9000,NULL,1,'2026-08-29 01:23:50',NULL),
(213,'Cartera de pinturas Stitch',NULL,NULL,13000,15500,NULL,1,'2026-08-29 01:23:50',NULL),
(214,'Auto con volante',NULL,NULL,11500,15500,NULL,1,'2026-08-29 01:23:50',NULL),
(215,'Encendedor',NULL,NULL,480,600,NULL,1,'2026-08-29 01:23:50',NULL),
(216,'Cinta scoch fina grande',NULL,NULL,710,1200,NULL,1,'2026-08-29 01:23:50',NULL),
(217,'Cinta pack fina',NULL,NULL,820,1400,NULL,1,'2026-08-29 01:23:50',NULL),
(218,'Guantes de cocina Make T M',NULL,NULL,1400,2000,NULL,1,'2026-08-29 01:23:50',NULL),
(219,'Pizarra Magnética',NULL,NULL,5000,8000,NULL,1,'2026-08-29 01:23:50',NULL),
(220,'Popit',NULL,NULL,5800,8500,NULL,1,'2026-08-29 01:23:50',NULL),
(221,'Mesa con ruedas',NULL,NULL,10500,15000,NULL,1,'2026-08-29 01:23:50',NULL),
(222,'Capibara que camina',NULL,NULL,4500,4500,NULL,1,'2026-08-29 01:23:50',NULL),
(223,'Mochila bloque',NULL,NULL,6500,9500,NULL,1,'2026-08-29 01:23:50',NULL),
(224,'Masa mágica por 39',NULL,NULL,4800,8500,NULL,1,'2026-08-29 01:23:50',NULL),
(225,'Toallón por 3',NULL,NULL,7000,10000,NULL,1,'2026-08-29 01:23:50',NULL),
(226,'Birome',NULL,'De todos los colores',1000,2000,-1,2,'2026-08-31 15:20:34','2026-08-31 15:21:39'),
(227,'Cartulina',NULL,'De todos los colores',2000,4000,17,2,'2026-08-31 20:57:27','2026-08-31 20:58:15');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `correo` varchar(200) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `ingreso` timestamp NULL DEFAULT NULL,
  `editado` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuarios_correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES
(1,'admin',NULL,'pabloruiz1980@gmail.com','$2y$12$emBPsJNMpsTuqm7ww9pUFuMFC5G/XwE3r/JmDndscLZ5WC8wsJGau',NULL,'2026-08-31 20:56:57','2026-08-31 20:56:57');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Dumping routines for database 'frani'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-08-31 18:00:42
