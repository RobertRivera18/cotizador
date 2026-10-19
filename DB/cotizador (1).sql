-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-10-2026 a las 00:02:51
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
-- Base de datos: `cotizador`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-356a192b7913b04c54574d18c28d46e6395428ab', 'i:3;', 1792433037),
('laravel-cache-356a192b7913b04c54574d18c28d46e6395428ab:timer', 'i:1792433037;', 1792433037),
('laravel-cache-74946802ad0f7817324408d513c75172', 'i:1;', 1792421651),
('laravel-cache-74946802ad0f7817324408d513c75172:timer', 'i:1792421651;', 1792421651),
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:10:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:19:\"Acceso al Dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:22:\"Gestion de generadores\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:16:\"Ver cotizaciones\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:18:\"Crear cotizaciones\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:19:\"Editar cotizaciones\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:21:\"Eliminar cotizaciones\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:26:\"Descargar PDF cotizaciones\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:16:\"Gestion de roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:19:\"Gestion de permisos\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:19:\"Gestion de Usuarios\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:2:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:13:\"Administrador\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:6:\"Asesor\";s:1:\"c\";s:3:\"web\";}}}', 1792507992);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizaciones`
--

CREATE TABLE `cotizaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entorno_id` bigint(20) UNSIGNED DEFAULT NULL,
  `generador_id` bigint(20) UNSIGNED DEFAULT NULL,
  `equipos_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`equipos_json`)),
  `potencia_total_w` int(10) UNSIGNED DEFAULT NULL,
  `capacidad_recomendada_kva` decimal(8,2) DEFAULT NULL,
  `capacidad_recomendada_kw` decimal(8,2) DEFAULT NULL,
  `nombre_cliente` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `cedula_ruc` varchar(13) DEFAULT NULL,
  `estado` varchar(255) NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tipo_origen` varchar(255) NOT NULL DEFAULT 'web',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cotizaciones`
--

INSERT INTO `cotizaciones` (`id`, `entorno_id`, `generador_id`, `equipos_json`, `potencia_total_w`, `capacidad_recomendada_kva`, `capacidad_recomendada_kw`, `nombre_cliente`, `telefono`, `email`, `cedula_ruc`, `estado`, `created_at`, `updated_at`, `tipo_origen`, `total`) VALUES
(9, NULL, NULL, NULL, NULL, NULL, NULL, 'Mario Jose Lopez Guevara', '0978908900', 'mariolope@gmail.com', '2400335119', 'pendiente', '2026-10-20 02:46:21', '2026-10-20 02:46:21', 'manual', 4700.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion_detalles`
--

CREATE TABLE `cotizacion_detalles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cotizacion_id` bigint(20) UNSIGNED NOT NULL,
  `equipo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cotizacion_detalles`
--

INSERT INTO `cotizacion_detalles` (`id`, `cotizacion_id`, `equipo_id`, `descripcion`, `cantidad`, `precio_unitario`, `subtotal`, `created_at`, `updated_at`) VALUES
(16, 9, NULL, 'Bateria GSL 10 kw ', 1, 3000.00, 3000.00, '2026-10-20 02:46:21', '2026-10-20 02:46:21'),
(17, 9, NULL, 'Mano de obra e instalacion del equipo', 1, 1700.00, 1700.00, '2026-10-20 02:46:21', '2026-10-20 02:46:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entornos`
--

CREATE TABLE `entornos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `icono` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `orden` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `entornos`
--

INSERT INTO `entornos` (`id`, `nombre`, `slug`, `icono`, `descripcion`, `orden`, `created_at`, `updated_at`) VALUES
(1, 'Residencial', 'residencial', 'home', NULL, 1, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(2, 'Comercial', 'comercial', 'building', NULL, 2, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(3, 'Industrial', 'industrial', 'factory', NULL, 3, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(4, 'Eventos', 'eventos', 'sparkles', NULL, 4, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(5, 'Campamento', 'campamento', 'tent', NULL, 5, '2026-10-19 19:50:46', '2026-10-19 19:50:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entorno_equipo`
--

CREATE TABLE `entorno_equipo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entorno_id` bigint(20) UNSIGNED NOT NULL,
  `equipo_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad_defecto` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `seleccionado_defecto` tinyint(1) NOT NULL DEFAULT 0,
  `orden` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `entorno_equipo`
--

INSERT INTO `entorno_equipo` (`id`, `entorno_id`, `equipo_id`, `cantidad_defecto`, `seleccionado_defecto`, `orden`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(2, 1, 2, 0, 0, 2, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(3, 1, 3, 10, 1, 3, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(4, 1, 4, 1, 0, 4, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(5, 1, 5, 0, 0, 5, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(6, 1, 7, 0, 0, 6, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(7, 1, 8, 0, 0, 7, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(8, 1, 13, 1, 1, 8, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(9, 2, 3, 20, 1, 1, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(10, 2, 2, 2, 1, 2, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(11, 2, 8, 4, 1, 3, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(12, 2, 1, 1, 1, 4, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(13, 2, 9, 1, 0, 5, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(14, 2, 13, 2, 1, 6, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(15, 3, 11, 3, 1, 1, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(16, 3, 6, 1, 1, 2, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(17, 3, 3, 30, 1, 3, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(18, 3, 14, 4, 0, 4, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(19, 4, 3, 40, 1, 1, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(20, 4, 14, 4, 0, 2, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(21, 4, 12, 1, 0, 3, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(22, 4, 15, 10, 0, 4, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(23, 5, 3, 6, 1, 1, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(24, 5, 10, 1, 0, 2, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(25, 5, 6, 1, 1, 3, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(26, 5, 15, 4, 1, 4, '2026-10-19 19:50:46', '2026-10-19 19:50:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipo` varchar(255) NOT NULL DEFAULT 'generador',
  `nombre` varchar(255) NOT NULL,
  `capacidad_kva` decimal(8,2) DEFAULT NULL,
  `capacidad_kw` decimal(8,2) NOT NULL,
  `combustible` enum('gasolina','diesel','gas') DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `fases` varchar(255) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id`, `tipo`, `nombre`, `capacidad_kva`, `capacidad_kw`, `combustible`, `marca`, `fases`, `precio`, `imagen`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
(9, 'generador', 'Generador de 15 kW', 12.00, 15.00, 'gasolina', 'GENSET /JUNKE', 'monofasico', 5400.00, NULL, NULL, 1, '2026-10-19 20:44:46', '2026-10-19 21:39:30'),
(10, 'generador', 'Generador de 18kW', 22.50, 18.00, 'gasolina', 'GENSET / JUNKE', 'trifasico', 5100.00, NULL, NULL, 1, '2026-10-19 20:58:19', '2026-10-19 21:28:10'),
(11, 'generador', 'Generador de 22kW', 27.50, 22.00, 'gasolina', 'GENSET / JUNKE', 'trifasico', 5400.00, NULL, NULL, 1, '2026-10-19 21:03:44', '2026-10-19 21:28:18'),
(12, 'generador', 'Generador de 24kW', 30.00, 24.00, 'gasolina', 'GENSET / JUNKE', 'monofasico', 6500.00, NULL, NULL, 1, '2026-10-19 21:06:29', '2026-10-19 21:06:29'),
(13, 'generador', 'Generador de 26kW', 32.50, 26.00, 'gasolina', ' GENSET / JUNKE ', 'trifasico', 6500.00, NULL, NULL, 1, '2026-10-19 21:12:00', '2026-10-19 21:28:27'),
(14, 'generador', 'Generador de 32kW', 40.00, 32.00, 'gasolina', ' GENSET / JUNKE ', 'monofasico', 8200.00, NULL, NULL, 1, '2026-10-19 21:24:31', '2026-10-19 21:28:38'),
(15, 'generador', 'Generador de 35kW', 43.75, 35.00, 'gasolina', ' GENSET / JUNKE ', 'trifasico', 6500.00, NULL, NULL, 1, '2026-10-19 21:30:24', '2026-10-19 21:30:24'),
(16, 'generador', 'Generador de 48kW', 60.00, 48.00, 'gasolina', ' GENSET / JUNKE ', 'trifasico', 7900.00, NULL, NULL, 1, '2026-10-19 21:32:34', '2026-10-19 21:32:34'),
(17, 'generador', 'Generador de 60kW', 75.00, 60.00, 'gasolina', ' GENSET / JUNKE ', 'trifasico', 9500.00, NULL, NULL, 1, '2026-10-19 21:35:02', '2026-10-19 21:39:11'),
(18, 'generador', '  Generador Diesel 15KW  ', 18.75, 15.00, 'diesel', ' JUSTPOWER', 'monofasico', 5300.00, 'generadores/VX3ruM1aiqqrN0DL9f4Tl8W0k8avjd0gZlMg2Rji.png', NULL, 1, '2026-10-19 21:38:57', '2026-10-19 23:03:34'),
(19, 'generador', 'Generador Diesel  24KW  ', 30.00, 24.00, 'diesel', ' JUSTPOWER', 'monofasico', 6500.00, 'generadores/33NdS0pwmEexocZRWS57Np31PEOqprDf2Y9URPk8.png', NULL, 1, '2026-10-19 21:41:23', '2026-10-19 23:03:23'),
(20, 'generador', 'Generador Diesel  26KW ', 32.50, 26.00, 'diesel', ' JUSTPOWER', 'trifasico', NULL, 'generadores/w9ZvfrX5X18LN7P9gAV50KyCqaHYaj5OfSnVN27x.png', NULL, 1, '2026-10-19 21:44:45', '2026-10-19 23:02:59'),
(21, 'generador', '  Generador Diesel 35KW ', 43.75, 35.00, 'diesel', ' JUSTPOWER', 'trifasico', 6700.00, 'generadores/jRw3l4RSeHAxiC3nd70KuKyl4lWJbYs6PrvsZiUB.png', NULL, 1, '2026-10-19 21:46:40', '2026-10-19 23:02:50'),
(22, 'generador', 'Generador Diesel  32KW  ', 40.00, 32.00, 'diesel', ' JUSTPOWER', 'monofasico', 8200.00, 'generadores/OjDNTBDBIK1waRu9ScItnnuHdxtokAcpftvl50Az.png', NULL, 1, '2026-10-19 21:48:06', '2026-10-19 23:02:38'),
(23, 'generador', 'Generador Diesel  48KW', 60.00, 48.00, 'diesel', ' JUSTPOWER', 'trifasico', 8300.00, 'generadores/x4vKu0HDe913hT4VbnHEyIcuo137zTZVmdjpUdMh.png', NULL, 1, '2026-10-19 21:49:48', '2026-10-19 23:02:26'),
(24, 'generador', 'Generador Diesel  60KW  ', 75.00, 60.00, 'diesel', ' JUSTPOWER', 'trifasico', 9500.00, 'generadores/GLculymGtPhmcCIXd8Wd8yO5yrakunSYkvPWhNP7.png', NULL, 1, '2026-10-19 21:54:09', '2026-10-19 23:02:01'),
(25, 'generador', 'Generador Portatil Pequeño 3kW', 3.75, 3.00, 'gasolina', ' JUSTPOWER', 'monofasico', 450.00, NULL, NULL, 1, '2026-10-19 22:22:25', '2026-10-19 22:22:25'),
(26, 'generador', 'Generador Portatil Pequeño 7kW', 8.75, 7.00, 'gasolina', ' JUSTPOWER', 'monofasico', 700.00, NULL, NULL, 1, '2026-10-19 22:24:59', '2026-10-19 22:24:59'),
(27, 'generador', 'Generador Generac-Guardian 24KW a Gas', 30.00, 24.00, 'gas', 'Generac', 'monofasico', 9200.00, NULL, NULL, 1, '2026-10-19 22:27:08', '2026-10-19 22:27:08'),
(28, 'generador', ' Generador Generac-Guardian 14KW a Gas', 17.50, 14.00, 'gas', 'Generac', 'monofasico', 6800.00, NULL, NULL, 1, '2026-10-19 22:40:10', '2026-10-19 22:40:10'),
(29, 'generador', 'Generador Generac-Guardian 22KW a Gas', 27.50, 22.00, 'gas', 'Generac', 'monofasico', 11634.00, NULL, NULL, 1, '2026-10-19 22:41:43', '2026-10-19 22:41:43'),
(30, 'generador', 'Generador Generac-Guardian 26KW a Gas', 32.50, 26.00, 'gas', 'Generac', 'monofasico', 10250.00, NULL, NULL, 1, '2026-10-19 22:56:20', '2026-10-19 22:56:20'),
(31, 'bateria', 'Bateria GSL 10 kw ', NULL, 10.00, NULL, 'GSL ENERGY', NULL, 3000.00, NULL, NULL, 1, '2026-10-20 02:22:44', '2026-10-20 02:22:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equiposelectricos`
--

CREATE TABLE `equiposelectricos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `icono` varchar(255) DEFAULT NULL,
  `potencia_nominal_w` int(10) UNSIGNED NOT NULL,
  `potencia_arranque_w` int(10) UNSIGNED DEFAULT NULL,
  `es_motor` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `equiposelectricos`
--

INSERT INTO `equiposelectricos` (`id`, `nombre`, `icono`, `potencia_nominal_w`, `potencia_arranque_w`, `es_motor`, `created_at`, `updated_at`) VALUES
(1, 'Refrigeradora', '🧊', 150, 450, 1, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(2, 'Aire Acond. 12k BTU', '❄️', 1200, 3600, 1, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(3, 'Luces LED', '💡', 10, NULL, 0, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(4, 'TV', '📺', 120, NULL, 0, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(5, 'Lavadora', '🧺', 500, 1500, 1, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(6, 'Bomba de Agua', '🚰', 750, 2200, 1, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(7, 'Microondas', '🍽️', 1100, NULL, 0, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(8, 'Computadora', '💻', 300, NULL, 0, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(9, 'Cafetera', '☕', 900, NULL, 0, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(10, 'Congelador', '🧊', 200, 600, 1, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(11, 'Herramienta Eléctrica', '🛠️', 1000, 2500, 1, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(12, 'Cocina de Inducción', '🍳', 1800, NULL, 0, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(13, 'Router / Módem', '📶', 15, NULL, 0, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(14, 'Ventilador', '🌀', 75, 150, 1, '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(15, 'Cargador de Celular', '🔌', 10, NULL, 0, '2026-10-19 19:50:46', '2026-10-19 19:50:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_07_09_120210_create_entornos_table', 1),
(5, '2026_07_09_120435_create_equiposelectricos_table', 1),
(6, '2026_07_09_121120_create_generadores_table', 1),
(7, '2026_07_09_121542_create_entorno_equipo_table', 1),
(8, '2026_07_09_121543_create_cotizaciones_table', 1),
(9, '2026_10_16_015657_add_two_factor_columns_to_users_table', 1),
(10, '2026_10_16_015658_create_passkeys_table', 1),
(11, '2026_10_16_015733_create_personal_access_tokens_table', 1),
(12, '2026_10_17_044551_create_cotizacion_detalles_table', 1),
(13, '2026_10_17_044610_add_tipo_and_total_to_cotizaciones_table', 1),
(14, '2026_10_18_171957_add_cedula_ruc_to_cotizaciones_table', 1),
(15, '2026_10_18_195951_create_permission_tables', 1),
(16, '2026_10_19_192008_rename_generadores_to_equipos_table', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `passkeys`
--

CREATE TABLE `passkeys` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `credential_id` varchar(255) NOT NULL,
  `credential` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`credential`)),
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Acceso al Dashboard', 'web', '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(2, 'Gestion de generadores', 'web', '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(3, 'Ver cotizaciones', 'web', '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(4, 'Crear cotizaciones', 'web', '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(5, 'Editar cotizaciones', 'web', '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(6, 'Eliminar cotizaciones', 'web', '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(7, 'Descargar PDF cotizaciones', 'web', '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(8, 'Gestion de roles', 'web', '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(9, 'Gestion de permisos', 'web', '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(10, 'Gestion de Usuarios', 'web', '2026-10-19 19:50:46', '2026-10-19 19:50:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'web', '2026-10-19 19:50:46', '2026-10-19 19:50:46'),
(2, 'Asesor', 'web', '2026-10-19 19:50:46', '2026-10-19 19:50:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(3, 1),
(3, 2),
(4, 1),
(4, 2),
(5, 1),
(5, 2),
(6, 1),
(7, 1),
(7, 2),
(8, 1),
(9, 1),
(10, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('LMCnjxqL8tuHcwMoNxT3NfLMvdTmB7p7VcO5k32z', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoic0lmcTYyNnlKaUFPODNBSnFlVFpTcWV0NlpnSGgxbmRpbTlQSTRERSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9lcXVpcG9zIjtzOjU6InJvdXRlIjtzOjE3OiJhZG1pbi5nZW5lcmFkb3JlcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6IjAyNDY1YWNhZjdhNGE3M2U0MTRmNzA0NzQ2M2Y1ODM0MzkzMzcwZWZmYmMzOGQxZmEyYTFmNmM1ZDcxYTk2NjIiO30=', 1792446926);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `current_team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `profile_photo_path` varchar(2048) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `current_team_id`, `profile_photo_path`, `created_at`, `updated_at`) VALUES
(1, 'Robert Rivera', 'rxrc1819@gmail.com', NULL, '$2y$12$PqnaiehtAr6g609PHYkdueIce8/5wssg0c.GayWOhZ3gBhaPgOqDa', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-19 19:50:46', '2026-10-19 20:01:14');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cotizaciones_entorno_id_foreign` (`entorno_id`),
  ADD KEY `cotizaciones_generador_id_foreign` (`generador_id`);

--
-- Indices de la tabla `cotizacion_detalles`
--
ALTER TABLE `cotizacion_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cotizacion_detalles_cotizacion_id_foreign` (`cotizacion_id`),
  ADD KEY `cotizacion_detalles_generador_id_foreign` (`equipo_id`);

--
-- Indices de la tabla `entornos`
--
ALTER TABLE `entornos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `entornos_slug_unique` (`slug`);

--
-- Indices de la tabla `entorno_equipo`
--
ALTER TABLE `entorno_equipo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entorno_equipo_entorno_id_foreign` (`entorno_id`),
  ADD KEY `entorno_equipo_equipo_id_foreign` (`equipo_id`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `equiposelectricos`
--
ALTER TABLE `equiposelectricos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `passkeys`
--
ALTER TABLE `passkeys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `passkeys_credential_id_unique` (`credential_id`),
  ADD KEY `passkeys_user_id_index` (`user_id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `cotizacion_detalles`
--
ALTER TABLE `cotizacion_detalles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `entornos`
--
ALTER TABLE `entornos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `entorno_equipo`
--
ALTER TABLE `entorno_equipo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `equiposelectricos`
--
ALTER TABLE `equiposelectricos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `passkeys`
--
ALTER TABLE `passkeys`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  ADD CONSTRAINT `cotizaciones_entorno_id_foreign` FOREIGN KEY (`entorno_id`) REFERENCES `entornos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cotizaciones_generador_id_foreign` FOREIGN KEY (`generador_id`) REFERENCES `equipos` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `cotizacion_detalles`
--
ALTER TABLE `cotizacion_detalles`
  ADD CONSTRAINT `cotizacion_detalles_cotizacion_id_foreign` FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cotizacion_detalles_generador_id_foreign` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `entorno_equipo`
--
ALTER TABLE `entorno_equipo`
  ADD CONSTRAINT `entorno_equipo_entorno_id_foreign` FOREIGN KEY (`entorno_id`) REFERENCES `entornos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `entorno_equipo_equipo_id_foreign` FOREIGN KEY (`equipo_id`) REFERENCES `equiposelectricos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `passkeys`
--
ALTER TABLE `passkeys`
  ADD CONSTRAINT `passkeys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
