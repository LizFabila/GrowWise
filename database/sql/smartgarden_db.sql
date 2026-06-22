-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 16-06-2026 a las 20:15:04
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
-- Base de datos: `smartgarden_db`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_costo_beneficio_vendedor` (IN `p_vendedor_id` INT)   BEGIN
    DECLARE v_total_vendido DECIMAL(10,2) DEFAULT 0;
    DECLARE v_inversion_total DECIMAL(10,2) DEFAULT 0;
    DECLARE v_beneficio_neto DECIMAL(10,2) DEFAULT 0;
    DECLARE v_rentabilidad DECIMAL(10,2) DEFAULT 0;
    DECLARE v_roi DECIMAL(10,2) DEFAULT 0;
    DECLARE v_producto_mas_rentable VARCHAR(100) DEFAULT NULL;
    DECLARE v_producto_mas_vendido VARCHAR(100) DEFAULT NULL;
    DECLARE v_productos_publicados INT DEFAULT 0;
    DECLARE v_stock_restante DECIMAL(10,2) DEFAULT 0;
    DECLARE v_productos_vendidos INT DEFAULT 0;

    
    SELECT COALESCE(SUM(total), 0) INTO v_total_vendido
    FROM ventas
    WHERE user_id_vendedor = p_vendedor_id AND estado = 'completada';

    
    SET v_inversion_total = v_total_vendido * 0.25;

    
    SET v_beneficio_neto = v_total_vendido - v_inversion_total;

    
    IF v_inversion_total > 0 THEN
        SET v_rentabilidad = (v_beneficio_neto / v_inversion_total) * 100;
    ELSE
        SET v_rentabilidad = 0;
    END IF;

    
    IF v_inversion_total > 0 THEN
        SET v_roi = (v_beneficio_neto / v_inversion_total) * 100;
    ELSE
        SET v_roi = 0;
    END IF;

    
    SELECT c.nombre INTO v_producto_mas_rentable
    FROM ventas v
    JOIN pedidos p ON v.pedido_id = p.id
    JOIN pedidos_detalle pd ON p.id = pd.pedido_id
    JOIN productos_venta pv ON pd.producto_venta_id = pv.id
    JOIN cultivos c ON pv.cultivo_id = c.id
    WHERE v.user_id_vendedor = p_vendedor_id
    GROUP BY c.id, c.nombre
    ORDER BY SUM(pd.subtotal) DESC
    LIMIT 1;

    
    SELECT c.nombre INTO v_producto_mas_vendido
    FROM ventas v
    JOIN pedidos p ON v.pedido_id = p.id
    JOIN pedidos_detalle pd ON p.id = pd.pedido_id
    JOIN productos_venta pv ON pd.producto_venta_id = pv.id
    JOIN cultivos c ON pv.cultivo_id = c.id
    WHERE v.user_id_vendedor = p_vendedor_id
    GROUP BY c.id, c.nombre
    ORDER BY SUM(pd.cantidad) DESC
    LIMIT 1;

    
    SELECT COUNT(*) INTO v_productos_publicados
    FROM productos_venta
    WHERE user_id = p_vendedor_id AND estado != 'eliminado';

    
    SELECT COALESCE(SUM(stock), 0) INTO v_stock_restante
    FROM productos_venta
    WHERE user_id = p_vendedor_id AND estado = 'disponible';

    
    SELECT COALESCE(SUM(pd.cantidad), 0) INTO v_productos_vendidos
    FROM ventas v
    JOIN pedidos p ON v.pedido_id = p.id
    JOIN pedidos_detalle pd ON p.id = pd.pedido_id
    WHERE v.user_id_vendedor = p_vendedor_id;

    
    SELECT 
        v_total_vendido AS total_vendido,
        v_inversion_total AS inversion_total,
        v_beneficio_neto AS beneficio_neto,
        v_rentabilidad AS rentabilidad,
        v_roi AS roi,
        v_producto_mas_rentable AS producto_mas_rentable,
        v_producto_mas_vendido AS producto_mas_vendido,
        v_productos_publicados AS productos_publicados,
        v_stock_restante AS stock_restante,
        v_productos_vendidos AS productos_vendidos;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_generar_alerta_por_lectura` (IN `p_sensor_id` INT, IN `p_valor` DECIMAL(8,2))   BEGIN
    
    
    
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas`
--

CREATE TABLE `alertas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `modulo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sensor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `siembra_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo` varchar(50) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `prioridad` varchar(20) DEFAULT 'Media',
  `estado` enum('Pendiente','Resuelta','Ignorada') DEFAULT 'Pendiente',
  `fecha_resolucion` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `alertas`
--

INSERT INTO `alertas` (`id`, `user_id`, `modulo_id`, `sensor_id`, `siembra_id`, `tipo`, `titulo`, `mensaje`, `prioridad`, `estado`, `fecha_resolucion`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 5, 2, 'humedad_baja', 'Humedad baja en m?dulo 2', 'La humedad en el m?dulo 2 (Espinaca) est? en 32%, por debajo del umbral ?ptimo.', 'Alta', 'Pendiente', NULL, '2026-03-14 18:32:13', '2026-03-14 18:32:13'),
(2, 1, 3, 8, 3, 'luz_insuficiente', 'Luz insuficiente en m?dulo 3', 'El nivel de luz en m?dulo 3 (Albahaca) es de 3500 lux, por debajo de lo recomendado.', 'Media', 'Pendiente', NULL, '2026-03-14 18:32:13', '2026-03-14 18:32:13'),
(3, 1, 1, 1, 1, 'temperatura_alta', 'Temperatura alta', 'La temperatura en el invernadero ha superado los 28?C.', 'Alta', 'Pendiente', NULL, '2026-03-14 18:32:13', '2026-03-14 18:32:13'),
(4, 1, 4, 10, 4, 'ph_bajo', 'pH bajo en m?dulo 4', 'El pH en el m?dulo 4 (Tomate) es de 5.2, por debajo del rango ?ptimo.', 'Critica', 'Pendiente', NULL, '2026-03-14 18:32:13', '2026-05-13 06:42:02'),
(5, 7, NULL, 12, NULL, 'temperatura_critica', 'Temperatura Critica', 'La temperatura en el invernadero es de 50°C, por encima del nivel critico (28°C).', 'Critica', 'Resuelta', '2026-05-13 12:43:25', '2026-05-13 12:42:51', '2026-05-13 12:43:25'),
(6, 7, NULL, 18, NULL, 'ph_critico', 'pH Critico', 'El pH de la solucion nutritiva es de 50. Ajuste urgente a rango 5.5-6.8.', 'Critica', 'Pendiente', NULL, '2026-05-13 12:42:51', '2026-05-13 12:42:51'),
(7, 7, NULL, 12, NULL, 'temperatura_critica', 'Temperatura Critica', 'La temperatura en el invernadero es de 50°C, por encima del nivel critico (28°C).', 'Critica', 'Pendiente', NULL, '2026-05-13 12:43:26', '2026-05-13 12:43:26'),
(8, 7, NULL, 17, NULL, 'luz_insuficiente', 'Luz Insuficiente', 'El nivel de luz es de 50 lux, muy por debajo de lo recomendado (3000-8000 lux).', 'Media', 'Pendiente', NULL, '2026-05-13 14:10:20', '2026-05-13 14:10:20'),
(9, 7, NULL, 12, NULL, 'temperatura_critica', 'Temperatura Critica', 'La temperatura en el invernadero es de 50°C, por encima del nivel critico (28°C).', 'Critica', 'Pendiente', NULL, '2026-05-14 23:43:11', '2026-05-14 23:43:11'),
(10, 7, NULL, 18, NULL, 'ph_critico', 'pH Critico', 'El pH de la solucion nutritiva es de 50. Ajuste urgente a rango 5.5-6.8.', 'Critica', 'Pendiente', NULL, '2026-05-14 23:43:11', '2026-05-14 23:43:11'),
(11, 7, NULL, 17, NULL, 'luz_insuficiente', 'Luz Insuficiente', 'El nivel de luz es de 50 lux, muy por debajo de lo recomendado (3000-8000 lux).', 'Media', 'Pendiente', NULL, '2026-05-14 23:43:11', '2026-05-14 23:43:11'),
(12, 7, NULL, 12, NULL, 'temperatura_critica', 'Temperatura Critica', 'La temperatura en el invernadero es de 50°C, por encima del nivel critico (28°C).', 'Critica', 'Pendiente', NULL, '2026-06-02 02:31:45', '2026-06-02 02:31:45'),
(13, 7, NULL, 18, NULL, 'ph_critico', 'pH Critico', 'El pH de la solucion nutritiva es de 50. Ajuste urgente a rango 5.5-6.8.', 'Critica', 'Pendiente', NULL, '2026-06-02 02:31:45', '2026-06-02 02:31:45'),
(14, 7, NULL, 12, NULL, 'temperatura_critica', 'Temperatura Critica', 'La temperatura en el invernadero es de 50°C, por encima del nivel critico (28°C).', 'Critica', 'Pendiente', NULL, '2026-06-06 11:34:10', '2026-06-06 11:34:10'),
(15, 7, NULL, 18, NULL, 'ph_critico', 'pH Critico', 'El pH de la solucion nutritiva es de 50. Ajuste urgente a rango 5.5-6.8.', 'Critica', 'Pendiente', NULL, '2026-06-06 11:34:10', '2026-06-06 11:34:10'),
(16, 7, NULL, 12, NULL, 'temperatura_critica', 'Temperatura Critica', 'La temperatura en el invernadero es de 50°C, por encima del nivel critico (28°C).', 'Critica', 'Pendiente', NULL, '2026-06-07 11:14:56', '2026-06-07 11:14:56'),
(17, 7, NULL, 18, NULL, 'ph_critico', 'pH Critico', 'El pH de la solucion nutritiva es de 50. Ajuste urgente a rango 5.5-6.8.', 'Critica', 'Pendiente', NULL, '2026-06-07 11:14:56', '2026-06-07 11:14:56');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `alertas_activas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `alertas_activas` (
`id` bigint(20) unsigned
,`user_id` bigint(20) unsigned
,`usuario` varchar(201)
,`titulo` varchar(100)
,`mensaje` text
,`prioridad` varchar(20)
,`created_at` timestamp
,`minutos_transcurridos` bigint(21)
,`antiguedad` varchar(8)
,`modulo` varchar(50)
,`sensor` varchar(50)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditorias`
--

CREATE TABLE `auditorias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tabla` varchar(255) NOT NULL,
  `accion` varchar(255) NOT NULL,
  `antes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`antes`)),
  `despues` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`despues`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `auditorias`
--

INSERT INTO `auditorias` (`id`, `user_id`, `tabla`, `accion`, `antes`, `despues`, `created_at`, `updated_at`) VALUES
(1, 3, 'cultivos', 'update', NULL, '{\"updated_at\": \"2026-04-07 01:41:22\", \"descripcion\": \"Color rojo y dulce\", \"dias_cosecha\": \"65\", \"luz_optima_max\": \"4000\", \"humedad_optima_max\": \"90\", \"humedad_optima_min\": \"80\"}', '2026-04-07 07:41:22', '2026-04-07 07:41:22'),
(2, 3, 'cultivos', 'update', '[]', '{\"descripcion\": \"Crecimiento rapido\", \"dias_cosecha\": \"15\"}', '2026-04-07 07:59:29', '2026-04-07 07:59:29'),
(3, 3, 'cultivos', 'create', NULL, '{\"id\": 12, \"tipo\": \"Fruto\", \"nombre\": \"Zanahoria\", \"created_at\": \"2026-04-07T02:03:17.000000Z\", \"updated_at\": \"2026-04-07T02:03:17.000000Z\", \"descripcion\": \"Crecimiento lento\", \"dias_cosecha\": 35, \"ph_optimo_max\": \"7.0\", \"ph_optimo_min\": \"6.0\", \"luz_optima_max\": 5000, \"luz_optima_min\": 3000, \"humedad_optima_max\": 80, \"humedad_optima_min\": 60, \"temperatura_optima_max\": \"25.00\", \"temperatura_optima_min\": \"15.00\"}', '2026-04-07 08:03:17', '2026-04-07 08:03:17'),
(4, 3, 'siembras', 'delete', '{\"id\": 11, \"estado\": \"Activa\", \"charola\": 8, \"cosecha\": null, \"user_id\": 3, \"modulo_id\": 6, \"created_at\": \"2026-04-07T01:37:11.000000Z\", \"cultivo_id\": 11, \"evaluacion\": null, \"updated_at\": \"2026-04-07T01:37:11.000000Z\", \"fecha_siembra\": \"2026-04-07T00:00:00.000000Z\", \"observaciones\": \"El crecimiento es optimo y estable\", \"cantidad_semillas\": 5, \"fecha_cosecha_real\": null, \"fecha_estimada_cosecha\": null}', NULL, '2026-04-07 08:09:45', '2026-04-07 08:09:45'),
(5, 3, 'siembras', 'delete', '{\"id\": 10, \"estado\": \"Activa\", \"charola\": 4, \"cosecha\": null, \"user_id\": 3, \"modulo_id\": 6, \"created_at\": \"2026-04-06T14:15:05.000000Z\", \"cultivo_id\": 11, \"evaluacion\": null, \"updated_at\": \"2026-04-06T14:15:05.000000Z\", \"fecha_siembra\": \"2026-04-06T00:00:00.000000Z\", \"observaciones\": \"Etc\", \"cantidad_semillas\": 3, \"fecha_cosecha_real\": null, \"fecha_estimada_cosecha\": null}', NULL, '2026-04-07 08:10:27', '2026-04-07 08:10:27'),
(6, 3, 'cultivos', 'update', '[]', '{\"descripcion\": \"Hojas grandes\"}', '2026-04-07 08:14:09', '2026-04-07 08:14:09'),
(7, 3, 'siembras', 'delete', '{\"id\": 12, \"estado\": \"Activa\", \"charola\": 4, \"cosecha\": null, \"user_id\": 3, \"modulo_id\": 6, \"created_at\": \"2026-04-07T02:11:26.000000Z\", \"cultivo_id\": 3, \"evaluacion\": null, \"updated_at\": \"2026-04-07T02:11:26.000000Z\", \"fecha_siembra\": \"2026-04-07T00:00:00.000000Z\", \"observaciones\": null, \"cantidad_semillas\": 5, \"fecha_cosecha_real\": null, \"fecha_estimada_cosecha\": null}', NULL, '2026-04-07 08:14:29', '2026-04-07 08:14:29'),
(8, 3, 'siembras', 'create', NULL, '{\"id\": 13, \"estado\": \"Activa\", \"charola\": \"4\", \"user_id\": 3, \"modulo_id\": \"6\", \"created_at\": \"2026-04-07 02:14:49\", \"cultivo_id\": \"2\", \"updated_at\": \"2026-04-07 02:14:49\", \"fecha_siembra\": \"2026-04-07 00:00:00\", \"observaciones\": null, \"cantidad_semillas\": \"5\"}', '2026-04-07 08:14:49', '2026-04-07 08:14:49'),
(9, 3, 'siembras', 'update', '[]', '{\"estado\": \"Completada\"}', '2026-04-07 08:15:21', '2026-04-07 08:15:21'),
(10, 3, 'cosechas', 'delete', '{\"id\": 5, \"calidad\": \"Buena\", \"user_id\": 3, \"created_at\": \"2026-04-07T02:16:07.000000Z\", \"siembra_id\": 9, \"updated_at\": \"2026-04-07T02:16:07.000000Z\", \"cantidad_kg\": \"7.00\", \"fecha_cosecha\": \"2026-04-07T00:00:00.000000Z\", \"observaciones\": \"Buena cosecha el fruto en optimas condiciones\"}', NULL, '2026-04-07 08:22:32', '2026-04-07 08:22:32'),
(11, 3, 'cosechas', 'update', '[]', '{\"calidad\": \"Excelente\", \"cantidad_kg\": \"7.00\"}', '2026-04-07 08:23:06', '2026-04-07 08:23:06'),
(12, 3, 'siembras', 'delete', '{\"id\": 9, \"estado\": \"Activa\", \"charola\": 4, \"cosecha\": null, \"user_id\": 3, \"modulo_id\": 5, \"created_at\": \"2026-04-06T14:12:27.000000Z\", \"cultivo_id\": 10, \"evaluacion\": null, \"updated_at\": \"2026-04-06T14:12:27.000000Z\", \"fecha_siembra\": \"2026-04-06T00:00:00.000000Z\", \"observaciones\": \"Crecimiento optimo\", \"cantidad_semillas\": 5, \"fecha_cosecha_real\": null, \"fecha_estimada_cosecha\": null}', NULL, '2026-04-14 06:05:22', '2026-04-14 06:05:22'),
(13, 3, 'cultivos', 'delete', '{\"id\": 10, \"tipo\": \"Fruto\", \"activo\": true, \"imagen\": null, \"nombre\": \"Fresa\", \"created_at\": \"2026-04-06T13:55:38.000000Z\", \"updated_at\": \"2026-04-07T01:41:22.000000Z\", \"descripcion\": \"Color rojo y dulce\", \"dias_cosecha\": 65, \"ph_optimo_max\": \"7.0\", \"ph_optimo_min\": \"4.0\", \"luz_optima_max\": 4000, \"luz_optima_min\": 2000, \"humedad_optima_max\": 90, \"humedad_optima_min\": 80, \"temperatura_optima_max\": \"35.00\", \"temperatura_optima_min\": \"25.00\"}', NULL, '2026-04-14 06:05:58', '2026-04-14 06:05:58'),
(14, 3, 'cultivos', 'delete', '{\"id\": 11, \"tipo\": \"Hoja\", \"activo\": true, \"imagen\": null, \"nombre\": \"Col\", \"created_at\": \"2026-04-06T14:13:56.000000Z\", \"updated_at\": \"2026-04-06T14:13:56.000000Z\", \"descripcion\": null, \"dias_cosecha\": 65, \"ph_optimo_max\": \"6.0\", \"ph_optimo_min\": \"2.0\", \"luz_optima_max\": 2500, \"luz_optima_min\": 2000, \"humedad_optima_max\": 30, \"humedad_optima_min\": 25, \"temperatura_optima_max\": \"45.00\", \"temperatura_optima_min\": \"20.00\"}', NULL, '2026-04-14 07:14:27', '2026-04-14 07:14:27'),
(15, 3, 'cultivos', 'update', '[]', '{\"descripcion\": \"Color Rojo, tama?o peque?o\"}', '2026-04-14 07:17:10', '2026-04-14 07:17:10'),
(16, 3, 'cultivos', 'delete', '{\"id\": 12, \"tipo\": \"Fruto\", \"activo\": true, \"imagen\": null, \"nombre\": \"Zanahoria\", \"created_at\": \"2026-04-07T02:03:17.000000Z\", \"updated_at\": \"2026-04-07T02:03:17.000000Z\", \"descripcion\": \"Crecimiento lento\", \"dias_cosecha\": 35, \"ph_optimo_max\": \"7.0\", \"ph_optimo_min\": \"6.0\", \"luz_optima_max\": 5000, \"luz_optima_min\": 3000, \"humedad_optima_max\": 80, \"humedad_optima_min\": 60, \"temperatura_optima_max\": \"25.00\", \"temperatura_optima_min\": \"15.00\"}', NULL, '2026-04-14 07:17:41', '2026-04-14 07:17:41'),
(17, 3, 'cultivos', 'create', NULL, '{\"id\": 13, \"tipo\": \"Fruto\", \"nombre\": \"Zanahoria\", \"created_at\": \"2026-04-14 17:18:25\", \"updated_at\": \"2026-04-14 17:18:25\", \"descripcion\": null, \"dias_cosecha\": \"40\", \"ph_optimo_max\": \"8.0\", \"ph_optimo_min\": \"4.0\", \"luz_optima_max\": \"2000\", \"luz_optima_min\": \"1000\", \"humedad_optima_max\": \"65\", \"humedad_optima_min\": \"45\", \"temperatura_optima_max\": \"19\", \"temperatura_optima_min\": \"14\"}', '2026-04-14 23:18:25', '2026-04-14 23:18:25'),
(18, 3, 'cultivos', 'update', '[]', '{\"descripcion\": \"Necesita una humedad constante\"}', '2026-04-14 23:18:53', '2026-04-14 23:18:53'),
(19, 3, 'cultivos', 'delete', '{\"id\": 13, \"tipo\": \"Fruto\", \"activo\": true, \"imagen\": null, \"nombre\": \"Zanahoria\", \"created_at\": \"2026-04-14T23:18:25.000000Z\", \"updated_at\": \"2026-04-14T23:18:53.000000Z\", \"descripcion\": \"Necesita una humedad constante\", \"dias_cosecha\": 40, \"ph_optimo_max\": \"8.0\", \"ph_optimo_min\": \"4.0\", \"luz_optima_max\": 2000, \"luz_optima_min\": 1000, \"humedad_optima_max\": 65, \"humedad_optima_min\": 45, \"temperatura_optima_max\": \"19.00\", \"temperatura_optima_min\": \"14.00\"}', NULL, '2026-04-14 17:20:57', '2026-04-14 17:20:57'),
(20, 3, 'cultivos', 'create', NULL, '{\"id\": 14, \"tipo\": \"Fruto\", \"nombre\": \"Zanahoria\", \"created_at\": \"2026-04-14 11:22:30\", \"updated_at\": \"2026-04-14 11:22:30\", \"descripcion\": null, \"dias_cosecha\": \"45\", \"ph_optimo_max\": \"7.0\", \"ph_optimo_min\": \"5.0\", \"luz_optima_max\": \"2000\", \"luz_optima_min\": \"1000\", \"humedad_optima_max\": \"54\", \"humedad_optima_min\": \"30\", \"temperatura_optima_max\": \"45\", \"temperatura_optima_min\": \"15\"}', '2026-04-14 17:22:30', '2026-04-14 17:22:30'),
(21, NULL, 'users', 'create', NULL, '{\"id\": 6, \"email\": \"l20223@gmail.com\", \"avatar\": \"https://ui-avatars.com/api/?name=Pedro%2BGuillermo&background=2E7D32&color=fff&size=40\", \"nombre\": \"Pedro\", \"apellido\": \"Guillermo\", \"password\": \"$2y$12$q5Ye2Yi2SJtiBvyHrwmErusmHQq230S1CjO2dU34FFQ9Gd8fh3nA2\", \"created_at\": \"2026-04-24 08:49:16\", \"updated_at\": \"2026-04-24 08:49:16\"}', '2026-04-24 14:49:16', '2026-04-24 14:49:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Estructura de tabla para la tabla `configuraciones`
--

CREATE TABLE `configuraciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuraciones`
--

INSERT INTO `configuraciones` (`id`, `user_id`, `tipo`, `clave`, `valor`, `created_at`, `updated_at`) VALUES
(1, 1, 'general', 'nombre_huerto', '\"Mi Huerto SmartGarden\"', '2026-03-14 18:32:14', '2026-03-14 18:32:14'),
(2, 1, 'general', 'ubicacion', '\"Ciudad de M?xico\"', '2026-03-14 18:32:14', '2026-03-14 18:32:14'),
(3, 1, 'general', 'zona_horaria', '\"America/Mexico_City\"', '2026-03-14 18:32:14', '2026-03-14 18:32:14'),
(4, 1, 'monitoreo', 'intervalo_medicion', '\"15\"', '2026-03-14 18:32:14', '2026-03-14 18:32:14'),
(5, 1, 'monitoreo', 'temperatura', '\"true\"', '2026-03-14 18:32:14', '2026-03-14 18:32:14'),
(6, 1, 'monitoreo', 'humedad', '\"true\"', '2026-03-14 18:32:14', '2026-03-14 18:32:14'),
(7, 1, 'monitoreo', 'luz', '\"true\"', '2026-03-14 18:32:14', '2026-03-14 18:32:14'),
(8, 1, 'alertas', 'umbral_humedad_baja', '\"30\"', '2026-03-14 18:32:14', '2026-03-14 18:32:14'),
(9, 1, 'alertas', 'umbral_temperatura_alta', '\"35\"', '2026-03-14 18:32:14', '2026-03-14 18:32:14'),
(10, 1, 'alertas', 'email_notificaciones', '\"true\"', '2026-03-14 18:32:14', '2026-03-14 18:32:14'),
(11, 1, 'riego', 'automatico', '\"true\"', '2026-03-14 18:32:14', '2026-03-14 18:32:14'),
(12, 1, 'riego', 'hora_inicio', '\"08:00\"', '2026-03-14 18:32:14', '2026-03-14 18:32:14'),
(13, 1, 'riego', 'duracion', '\"10\"', '2026-03-14 18:32:14', '2026-03-14 18:32:14'),
(14, 7, 'costos', 'costo_electricidad_semana', '57', '2026-05-12 17:47:30', '2026-05-12 17:47:30'),
(15, 7, 'costos', 'costo_electricidad_mes', '246', '2026-05-12 17:47:30', '2026-05-12 17:47:30'),
(16, 7, 'costos', 'costo_electricidad_a?o', '2950', '2026-05-12 17:47:30', '2026-05-12 17:47:30'),
(17, 7, 'costos', 'costo_semillas', '5', '2026-05-12 17:47:30', '2026-05-12 17:47:30'),
(18, 7, 'costos', 'inversion_sustrato', '150', '2026-05-12 17:47:30', '2026-05-12 17:47:30'),
(19, 7, 'monitoreo', 'intervalo_medicion', '\"60\"', '2026-05-13 13:20:48', '2026-05-13 13:20:48'),
(20, 7, 'monitoreo', 'temperatura', 'true', '2026-05-13 13:20:48', '2026-05-13 13:20:48'),
(21, 7, 'monitoreo', 'humedad', 'true', '2026-05-13 13:20:48', '2026-05-13 13:20:48'),
(22, 7, 'monitoreo', 'luz', 'true', '2026-05-13 13:20:48', '2026-05-13 13:20:48');

-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cosechas`
--

CREATE TABLE `cosechas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `siembra_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_cosecha` date NOT NULL,
  `cantidad_kg` decimal(8,2) NOT NULL,
  `calidad` enum('Excelente','Buena','Regular','Mala') NOT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cosechas`
--

INSERT INTO `cosechas` (`id`, `siembra_id`, `user_id`, `fecha_cosecha`, `cantidad_kg`, `calidad`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 3, 1, '2025-03-07', 1.20, 'Excelente', 'Aroma intenso, hojas grandes', '2026-03-14 18:32:13', '2026-03-14 18:32:13'),
(3, 7, 3, '2026-03-15', 6.00, 'Excelente', 'El pimiento esta en condiciones optimas', '2026-03-16 01:31:34', '2026-03-16 01:31:34'),
(4, 8, 3, '2026-03-15', 7.00, 'Excelente', NULL, '2026-03-16 01:39:58', '2026-04-07 08:23:06'),
(9, 17, 7, '2026-06-06', 2.50, 'Excelente', 'Ra?ces de buen tama?o (3-5 cm), sabor suave y textura crujiente. Cosecha en punto ?ptimo.', '2026-05-13 07:12:44', '2026-06-07 21:08:09'),
(10, 15, 7, '2026-06-20', 2.00, 'Buena', 'Hojas tiernas para baby leaf. Crecimiento uniforme.', '2026-05-13 07:12:44', '2026-06-07 21:08:09'),
(11, 16, 7, '2026-06-27', 3.20, 'Excelente', 'Hojas grandes y verdes, textura suave. Cosecha selectiva de hojas exteriores.', '2026-05-13 07:12:44', '2026-06-07 21:08:14'),
(12, 21, 7, '2026-06-13', 1.80, 'Buena', 'Hojas tiernas y arom?ticas. Cosecha de hojas exteriores para consumo fresco.', '2026-05-13 07:12:44', '2026-06-07 21:08:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `crecimiento`
--

CREATE TABLE `crecimiento` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `siembra_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_medicion` date NOT NULL,
  `altura_cm` decimal(5,2) DEFAULT NULL,
  `numero_hojas` int(11) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `crecimiento`
--

INSERT INTO `crecimiento` (`id`, `siembra_id`, `fecha_medicion`, `altura_cm`, `numero_hojas`, `observaciones`, `created_at`, `updated_at`) VALUES
(5, 17, '2026-05-12', 6.50, NULL, 'Hojas verdes, crecimiento vigoroso. A?n sin bulbo visible.', '2026-05-13 04:36:58', '2026-05-13 04:36:58'),
(6, 15, '2026-05-12', 2.50, NULL, 'Crecimiento lento, hojas peque?as.', '2026-05-13 04:36:58', '2026-05-13 04:36:58'),
(7, 16, '2026-05-12', 4.00, NULL, 'Crecimiento medio, hojas verdes.', '2026-05-13 04:36:58', '2026-05-13 04:36:58'),
(8, 21, '2026-05-12', 3.00, NULL, 'Germinaci?n avanzada, brotes visibles.', '2026-05-13 04:36:58', '2026-05-13 04:36:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cultivos`
--

CREATE TABLE `cultivos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('Hoja','Fruto','Arom?tica','Ra?z','Otro') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `temperatura_optima_min` decimal(4,2) DEFAULT NULL,
  `temperatura_optima_max` decimal(4,2) DEFAULT NULL,
  `humedad_optima_min` tinyint(3) UNSIGNED DEFAULT NULL,
  `humedad_optima_max` tinyint(3) UNSIGNED DEFAULT NULL,
  `luz_optima_min` int(10) UNSIGNED DEFAULT NULL,
  `luz_optima_max` int(10) UNSIGNED DEFAULT NULL,
  `ph_optimo_min` decimal(3,1) DEFAULT NULL,
  `ph_optimo_max` decimal(3,1) DEFAULT NULL,
  `dias_cosecha` smallint(5) UNSIGNED DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cultivos`
--

INSERT INTO `cultivos` (`id`, `nombre`, `tipo`, `descripcion`, `temperatura_optima_min`, `temperatura_optima_max`, `humedad_optima_min`, `humedad_optima_max`, `luz_optima_min`, `luz_optima_max`, `ph_optimo_min`, `ph_optimo_max`, `dias_cosecha`, `imagen`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Lechuga Romana', 'Hoja', 'Cosecha baby leaf o planta entera. Altura ?ptima: 10-15 cm. Altura actual: 2.5 cm.', 17.80, 22.00, 40, 70, 3000, 5000, 6.0, 7.0, 60, NULL, 1, '2026-03-14 18:32:10', '2026-05-13 04:36:34'),
(2, 'Espinaca', 'Hoja', 'Cosecha baby leaf. Altura ?ptima: 10-15 cm. Altura actual: 4 cm.', 15.00, 20.00, 70, 85, 2500, 4500, 6.0, 7.5, 38, NULL, 1, '2026-03-14 18:32:10', '2026-05-13 04:36:34'),
(3, 'Albahaca', 'Arom�tica', NULL, 18.00, 25.00, 60, 75, 4000, 6000, 5.5, 6.5, 25, NULL, 1, '2026-03-14 18:32:10', '2026-03-14 18:32:10'),
(4, 'Tomate cherry', 'Fruto', 'Color Rojo, tama?o peque?o', 20.00, 28.00, 65, 80, 5000, 8000, 5.5, 6.8, 60, NULL, 1, '2026-03-14 18:32:10', '2026-04-14 07:17:10'),
(5, 'Pimiento', 'Fruto', NULL, 20.00, 26.00, 60, 75, 4500, 7000, 6.0, 7.0, 70, NULL, 1, '2026-03-14 18:32:10', '2026-03-14 18:32:10'),
(7, 'Cilantro', 'Arom�tica', 'Cosecha continua: corta hojas exteriores. Altura ?ptima: 10-15 cm. Altura actual: 3 cm.', 15.00, 25.00, 50, 70, 3000, 5000, 6.0, 7.0, 50, NULL, 1, '2026-03-14 18:32:10', '2026-05-13 04:36:35'),
(8, 'R?bano', 'Ra�z', NULL, 10.00, 20.00, 60, 75, 3500, 5500, 6.0, 7.0, 25, NULL, 1, '2026-03-14 18:32:10', '2026-03-14 18:32:10'),
(14, 'Zanahoria', 'Fruto', NULL, 15.00, 45.00, 30, 54, 1000, 2000, 5.0, 7.0, 45, NULL, 1, '2026-04-14 17:22:30', '2026-04-14 17:22:30');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `dashboard_resumen`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `dashboard_resumen` (
`user_id` bigint(20) unsigned
,`siembras_activas` bigint(21)
,`total_siembras` bigint(21)
,`alertas_pendientes` bigint(21)
,`modulos_activos` bigint(21)
,`total_cosechas` bigint(21)
,`kg_ultimo_mes` decimal(30,2)
,`rendimiento_promedio` decimal(7,5)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones_envio`
--

CREATE TABLE `direcciones_envio` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `calle` varchar(150) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `colonia` varchar(100) NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `codigo_postal` varchar(10) NOT NULL,
  `referencias` text DEFAULT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `direcciones_envio`
--

INSERT INTO `direcciones_envio` (`id`, `user_id`, `calle`, `numero`, `colonia`, `ciudad`, `estado`, `codigo_postal`, `referencias`, `principal`, `created_at`, `updated_at`) VALUES
(1, 7, 'Cipres', '7223752123', 'El arco', 'Ciudad de México', 'Estado de México', '51200', NULL, 1, '2026-05-09 11:47:10', '2026-05-09 11:47:10'),
(3, 8, 'Sin Calle', '12', 'Los saucos', 'Ciudad de México', 'Estado de México', '51200', NULL, 1, '2026-05-10 13:09:55', '2026-05-10 13:09:55'),
(4, 8, '16 de Septiembre', '34', 'Valle de Bravo', 'Ciudad de México', 'Estado de México', '51200', NULL, 0, '2026-05-10 13:10:45', '2026-05-10 13:10:45');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `estadisticas_cosechas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `estadisticas_cosechas` (
`user_id` bigint(20) unsigned
,`usuario` varchar(201)
,`año` int(4)
,`mes` int(2)
,`total_cosechas` bigint(21)
,`total_kg` decimal(30,2)
,`promedio_kg` decimal(12,6)
,`excelentes` bigint(21)
,`buenas` bigint(21)
,`regulares` bigint(21)
,`malas` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciones`
--

CREATE TABLE `evaluaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `siembra_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_evaluacion` date NOT NULL,
  `rendimiento` decimal(3,1) NOT NULL,
  `eficiencia` tinyint(3) UNSIGNED DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `evaluaciones`
--

INSERT INTO `evaluaciones` (`id`, `siembra_id`, `user_id`, `fecha_evaluacion`, `rendimiento`, `eficiencia`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 3, 1, '2025-03-08', 9.5, 95, 'Excelente desarrollo, aroma intenso', '2026-03-14 18:32:13', '2026-03-14 18:32:13'),
(2, 6, 3, '2026-03-15', 9.0, 75, 'Lleva un creciemiento bastante bueno', '2026-03-16 01:48:40', '2026-03-16 01:55:06'),
(3, 8, 3, '2026-03-15', 6.5, 45, 'Su creciemiento es lento', '2026-03-16 01:50:02', '2026-03-16 01:50:02'),
(4, 17, 7, '2026-05-26', 9.5, 95, 'Excelente cosecha de r?bano. Ra?ces firmes, sabor suave. Rendimiento superior al esperado.', '2026-05-13 07:19:24', '2026-05-13 07:19:24'),
(5, 15, 7, '2026-06-08', 8.0, 80, 'Buena cosecha de lechuga. Hojas tiernas, crecimiento uniforme.', '2026-05-13 07:19:24', '2026-05-13 07:19:24'),
(6, 16, 7, '2026-06-20', 9.0, 90, 'Excelente producci?n de espinaca. Hojas grandes y verdes.', '2026-05-13 07:19:24', '2026-05-13 07:19:24'),
(7, 21, 7, '2026-06-05', 7.5, 75, 'Cosecha de cilantro aceptable. Aroma bueno pero crecimiento m?s lento de lo esperado.', '2026-05-13 07:19:24', '2026-05-13 07:19:24');

-- --------------------------------------------------------


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
-- Estructura de tabla para la tabla `lecturas_sensores`
--

CREATE TABLE `lecturas_sensores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sensor_id` bigint(20) UNSIGNED NOT NULL,
  `valor` decimal(8,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lecturas_sensores`
--

INSERT INTO `lecturas_sensores` (`id`, `sensor_id`, `valor`, `created_at`) VALUES
(187, 32, 70.00, '2026-05-13 12:05:11'),
(188, 33, 83.00, '2026-05-13 12:05:11'),
(189, 34, 70.00, '2026-05-13 12:05:11'),
(190, 35, 73.00, '2026-05-13 12:05:11'),
(191, 36, 22.00, '2026-05-13 12:05:11'),
(192, 37, 5000.00, '2026-05-13 12:05:11'),
(193, 38, 6.30, '2026-05-13 12:05:11'),
(194, 39, 1200.00, '2026-05-13 12:05:11'),
(195, 12, 50.00, '2026-05-13 12:05:11'),
(196, 17, 50.00, '2026-05-13 12:05:11'),
(197, 18, 50.00, '2026-05-13 12:05:11'),
(198, 19, 50.00, '2026-05-13 12:05:11'),
(199, 20, 50.00, '2026-05-13 12:05:11'),
(200, 21, 50.00, '2026-05-13 12:05:11'),
(201, 22, 50.00, '2026-05-13 12:05:11'),
(202, 23, 50.00, '2026-05-13 12:05:11'),
(203, 25, 50.00, '2026-05-13 12:05:11'),
(204, 30, 50.00, '2026-05-13 12:05:11'),
(205, 31, 50.00, '2026-05-13 12:05:11'),
(210, 32, 69.80, '2026-05-14 11:00:07'),
(211, 33, 82.80, '2026-05-14 11:00:07'),
(212, 34, 69.80, '2026-05-14 11:00:07'),
(213, 35, 72.80, '2026-05-14 11:00:07'),
(214, 36, 21.20, '2026-05-14 11:00:07'),
(215, 37, 3000.00, '2026-05-14 11:00:07'),
(216, 38, 6.30, '2026-05-14 11:00:07'),
(217, 39, 1200.00, '2026-05-14 11:00:07'),
(218, 12, 50.00, '2026-05-14 11:00:07'),
(219, 17, 50.00, '2026-05-14 11:00:07'),
(220, 18, 50.00, '2026-05-14 11:00:07'),
(221, 19, 50.00, '2026-05-14 11:00:07'),
(222, 20, 50.00, '2026-05-14 11:00:07'),
(223, 21, 50.00, '2026-05-14 11:00:07'),
(224, 22, 50.00, '2026-05-14 11:00:07'),
(225, 23, 50.00, '2026-05-14 11:00:07'),
(226, 25, 50.00, '2026-05-14 11:00:07'),
(227, 30, 50.00, '2026-05-14 11:00:07'),
(228, 31, 50.00, '2026-05-14 11:00:07'),
(229, 32, 60.20, '2026-05-14 23:42:26'),
(230, 33, 73.20, '2026-05-14 23:42:26'),
(231, 34, 60.20, '2026-05-14 23:42:26'),
(232, 35, 63.20, '2026-05-14 23:42:26'),
(233, 36, 22.80, '2026-05-14 23:42:26'),
(234, 37, 5776.50, '2026-05-14 23:42:26'),
(235, 38, 6.30, '2026-05-14 23:42:26'),
(236, 39, 1200.00, '2026-05-14 23:42:26'),
(237, 12, 50.00, '2026-05-14 23:42:26'),
(238, 17, 50.00, '2026-05-14 23:42:26'),
(239, 18, 50.00, '2026-05-14 23:42:26'),
(240, 19, 50.00, '2026-05-14 23:42:26'),
(241, 20, 50.00, '2026-05-14 23:42:26'),
(242, 21, 50.00, '2026-05-14 23:42:26'),
(243, 22, 50.00, '2026-05-14 23:42:26'),
(244, 23, 50.00, '2026-05-14 23:42:26'),
(245, 25, 50.00, '2026-05-14 23:42:26'),
(246, 30, 50.00, '2026-05-14 23:42:26'),
(247, 31, 50.00, '2026-05-14 23:42:26'),
(248, 32, 60.70, '2026-06-02 02:29:46'),
(249, 33, 73.70, '2026-06-02 02:29:46'),
(250, 34, 60.70, '2026-06-02 02:29:46'),
(251, 35, 63.70, '2026-06-02 02:29:46'),
(252, 36, 20.50, '2026-06-02 02:29:46'),
(253, 37, 3000.00, '2026-06-02 02:29:46'),
(254, 38, 6.30, '2026-06-02 02:29:46'),
(255, 39, 1200.00, '2026-06-02 02:29:46'),
(256, 12, 50.00, '2026-06-02 02:29:46'),
(257, 17, 50.00, '2026-06-02 02:29:46'),
(258, 18, 50.00, '2026-06-02 02:29:46'),
(259, 19, 50.00, '2026-06-02 02:29:46'),
(260, 20, 50.00, '2026-06-02 02:29:46'),
(261, 21, 50.00, '2026-06-02 02:29:46'),
(262, 22, 50.00, '2026-06-02 02:29:46'),
(263, 23, 50.00, '2026-06-02 02:29:46'),
(264, 25, 50.00, '2026-06-02 02:29:46'),
(265, 30, 50.00, '2026-06-02 02:29:46'),
(266, 31, 50.00, '2026-06-02 02:29:46'),
(267, 32, 69.80, '2026-06-06 11:34:03'),
(268, 33, 82.80, '2026-06-06 11:34:03'),
(269, 34, 69.80, '2026-06-06 11:34:03'),
(270, 35, 72.80, '2026-06-06 11:34:03'),
(271, 36, 21.20, '2026-06-06 11:34:03'),
(272, 37, 3000.00, '2026-06-06 11:34:03'),
(273, 38, 6.30, '2026-06-06 11:34:03'),
(274, 39, 1200.00, '2026-06-06 11:34:03'),
(275, 12, 50.00, '2026-06-06 11:34:03'),
(276, 17, 50.00, '2026-06-06 11:34:03'),
(277, 18, 50.00, '2026-06-06 11:34:03'),
(278, 19, 50.00, '2026-06-06 11:34:03'),
(279, 20, 50.00, '2026-06-06 11:34:03'),
(280, 21, 50.00, '2026-06-06 11:34:03'),
(281, 22, 50.00, '2026-06-06 11:34:03'),
(282, 23, 50.00, '2026-06-06 11:34:03'),
(283, 25, 50.00, '2026-06-06 11:34:03'),
(284, 30, 50.00, '2026-06-06 11:34:03'),
(285, 31, 50.00, '2026-06-06 11:34:03'),
(286, 32, 69.80, '2026-06-07 11:38:21'),
(287, 33, 82.80, '2026-06-07 11:38:21'),
(288, 34, 69.80, '2026-06-07 11:38:21'),
(289, 35, 72.80, '2026-06-07 11:38:21'),
(290, 36, 21.20, '2026-06-07 11:38:21'),
(291, 37, 3000.00, '2026-06-07 11:38:21'),
(292, 38, 6.30, '2026-06-07 11:38:21'),
(293, 39, 1200.00, '2026-06-07 11:38:21'),
(294, 12, 50.00, '2026-06-07 11:38:21'),
(295, 17, 50.00, '2026-06-07 11:38:21'),
(296, 18, 50.00, '2026-06-07 11:38:21'),
(297, 19, 50.00, '2026-06-07 11:38:21'),
(298, 20, 50.00, '2026-06-07 11:38:21'),
(299, 21, 50.00, '2026-06-07 11:38:21'),
(300, 22, 50.00, '2026-06-07 11:38:21'),
(301, 23, 50.00, '2026-06-07 11:38:21'),
(302, 25, 50.00, '2026-06-07 11:38:21'),
(303, 30, 50.00, '2026-06-07 11:38:21'),
(304, 31, 50.00, '2026-06-07 11:38:21'),
(305, 32, 60.70, '2026-06-10 02:48:50'),
(306, 33, 73.70, '2026-06-10 02:48:50'),
(307, 35, 63.70, '2026-06-10 02:48:50'),
(308, 36, 20.50, '2026-06-10 02:48:50'),
(309, 37, 3000.00, '2026-06-10 02:48:50'),
(310, 38, 6.30, '2026-06-10 02:48:50'),
(311, 39, 1200.00, '2026-06-10 02:48:50'),
(312, 12, 50.00, '2026-06-10 02:48:50'),
(313, 17, 50.00, '2026-06-10 02:48:50'),
(314, 18, 50.00, '2026-06-10 02:48:50'),
(315, 19, 50.00, '2026-06-10 02:48:50'),
(316, 20, 50.00, '2026-06-10 02:48:50'),
(317, 21, 50.00, '2026-06-10 02:48:50'),
(318, 22, 50.00, '2026-06-10 02:48:50'),
(319, 23, 50.00, '2026-06-10 02:48:50'),
(320, 25, 50.00, '2026-06-10 02:48:50'),
(321, 30, 50.00, '2026-06-10 02:48:50'),
(322, 31, 50.00, '2026-06-10 02:48:50');

-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pago`
--

CREATE TABLE `metodos_pago` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `metodos_pago`
--

INSERT INTO `metodos_pago` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Efectivo', 'Pago en efectivo al momento de la entrega', 1, '2026-05-09 02:01:44', NULL),
(2, 'Tarjeta de crédito/débito', 'Pago con tarjeta VISA o Mastercard', 1, '2026-05-09 02:01:44', NULL),
(3, 'Transferencia bancaria', 'Transferencia desde tu cuenta bancaria', 1, '2026-05-09 02:01:44', NULL),
(4, 'Efectivo', 'Pago en efectivo al momento de la entrega', 1, '2026-05-10 12:36:21', NULL);

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
(8, '0001_01_01_000000_create_users_table', 1),
(9, '0001_01_01_000001_create_cache_table', 1),
(10, '0001_01_01_000002_create_jobs_table', 1),
(11, '2026_03_14_183530_create_cultivos_table', 1),
(12, '2026_03_14_183531_create_modulos_table', 1),
(13, '2026_03_14_183532_create_siembras_table', 1),
(14, '2026_03_14_183533_create_lectura_sensors_table', 1),
(15, '2026_03_14_183533_create_sensors_table', 1),
(16, '2026_03_14_183534_create_alertas_table', 1),
(17, '2026_03_14_183535_create_cosechas_table', 1),
(18, '2026_03_14_183536_create_evaluacions_table', 1),
(19, '2026_03_14_183537_create_reportes_table', 1),
(20, '2026_03_14_183540_create_configuracions_table', 1),
(21, '2026_04_06_235147_create_auditorias_table', 2),
(22, '2026_05_08_182317_create_direcciones_envio_table', 3),
(23, '2026_05_08_182317_create_productos_venta_table', 3),
(24, '2026_05_08_182318_create_metodos_pago_table', 3),
(25, '2026_05_08_182319_create_pedidos_table', 3),
(26, '2026_05_08_182320_create_pedidos_detalle_table', 3),
(27, '2026_05_08_182320_create_ventas_table', 3),
(28, '2026_05_10_044937_add_role_to_users_table', 4),
(29, '2026_05_13_064608_create_reportes_table', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `num_charolas` tinyint(3) UNSIGNED DEFAULT 4,
  `tipo_riego` enum('Manual','Autom?tico','Mixto') DEFAULT 'Manual',
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id`, `user_id`, `nombre`, `ubicacion`, `num_charolas`, `tipo_riego`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'M?dulo 1', 'Terraza este', 4, 'Autom�tico', 1, '2026-03-14 18:32:11', '2026-03-14 18:32:11'),
(2, 1, 'M?dulo 2', 'Terraza oeste', 4, 'Autom�tico', 1, '2026-03-14 18:32:11', '2026-03-14 18:32:11'),
(3, 1, 'M?dulo 3', 'Interior', 4, 'Manual', 1, '2026-03-14 18:32:11', '2026-03-14 18:32:11'),
(4, 1, 'M?dulo 4', 'Jard?n', 4, 'Mixto', 1, '2026-03-14 18:32:11', '2026-03-14 18:32:11'),
(5, 3, 'Modulo 8', 'Planta baja', 4, 'Autom�tico', 1, '2026-03-16 00:10:35', '2026-03-16 00:10:35'),
(6, 3, 'Modulo 7', 'Planta alta', 4, 'Autom�tico', 1, '2026-03-16 00:12:40', '2026-03-16 00:12:40'),
(7, 7, 'Invernadero Valle de Bravo', 'Valle de Bravo - Invernadero', 4, 'Autom�tico', 1, '2026-05-12 17:46:44', '2026-05-12 17:46:44');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `monitoreo_actual`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `monitoreo_actual` (
`modulo_id` bigint(20) unsigned
,`modulo` varchar(50)
,`user_id` bigint(20) unsigned
,`sensor_id` bigint(20) unsigned
,`sensor` varchar(50)
,`tipo` enum('Temperatura','Humedad','Luz','pH','Nutrientes')
,`unidad` varchar(10)
,`valor` decimal(8,2)
,`ultima_lectura` timestamp
,`minutos_desde_lectura` bigint(21)
,`estado_lectura` varchar(11)
);

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
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id_cliente` bigint(20) UNSIGNED NOT NULL,
  `user_id_vendedor` bigint(20) UNSIGNED NOT NULL,
  `id_direccion_envio` bigint(20) UNSIGNED NOT NULL,
  `id_metodo_pago` bigint(20) UNSIGNED NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `impuesto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_final` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','confirmado','enviado','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  `fecha_pedido` datetime NOT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `user_id_cliente`, `user_id_vendedor`, `id_direccion_envio`, `id_metodo_pago`, `subtotal`, `impuesto`, `total_final`, `estado`, `fecha_pedido`, `notas`, `created_at`, `updated_at`) VALUES
(1, 7, 7, 1, 2, 60.00, 9.60, 69.60, 'confirmado', '2026-05-09 05:50:28', NULL, '2026-05-09 11:50:28', '2026-05-09 11:50:28'),
(2, 7, 7, 1, 3, 30.00, 4.80, 34.80, 'confirmado', '2026-05-09 05:55:41', NULL, '2026-05-09 11:55:41', '2026-05-09 11:55:41'),
(3, 8, 7, 3, 2, 20.00, 3.20, 23.20, 'cancelado', '2026-05-10 07:11:32', NULL, '2026-05-10 13:11:32', '2026-05-12 01:54:23'),
(4, 8, 7, 3, 4, 20.00, 3.20, 23.20, 'entregado', '2026-05-10 07:11:42', NULL, '2026-05-10 13:11:42', '2026-05-12 01:54:22'),
(5, 8, 7, 3, 3, 20.00, 3.20, 23.20, 'enviado', '2026-05-10 07:11:49', NULL, '2026-05-10 13:11:49', '2026-05-10 14:47:08'),
(6, 8, 7, 3, 1, 30.00, 4.80, 34.80, 'confirmado', '2026-05-10 07:24:44', NULL, '2026-05-10 13:24:44', '2026-05-12 01:54:21'),
(7, 8, 7, 3, 1, 100.00, 16.00, 116.00, 'pendiente', '2026-05-10 09:11:14', NULL, '2026-05-10 15:11:14', '2026-05-12 01:54:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_detalle`
--

CREATE TABLE `pedidos_detalle` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pedido_id` bigint(20) UNSIGNED NOT NULL,
  `producto_venta_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos_detalle`
--

INSERT INTO `pedidos_detalle` (`id`, `pedido_id`, `producto_venta_id`, `cantidad`, `precio_unitario`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 2, 30.00, 60.00, '2026-05-09 11:50:28', '2026-05-09 11:50:28'),
(2, 2, 2, 1, 30.00, 30.00, '2026-05-09 11:55:41', '2026-05-09 11:55:41'),
(3, 3, 1, 1, 20.00, 20.00, '2026-05-10 13:11:32', '2026-05-10 13:11:32'),
(4, 4, 1, 1, 20.00, 20.00, '2026-05-10 13:11:42', '2026-05-10 13:11:42'),
(5, 5, 1, 1, 20.00, 20.00, '2026-05-10 13:11:49', '2026-05-10 13:11:49'),
(6, 6, 2, 1, 30.00, 30.00, '2026-05-10 13:24:44', '2026-05-10 13:24:44'),
(7, 7, 1, 5, 20.00, 100.00, '2026-05-10 15:11:14', '2026-05-10 15:11:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_venta`
--

CREATE TABLE `productos_venta` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `cultivo_id` bigint(20) UNSIGNED NOT NULL,
  `cosecha_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `unidad` varchar(10) NOT NULL DEFAULT 'kg',
  `precio_unitario` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `estado` enum('disponible','agotado','eliminado') NOT NULL DEFAULT 'disponible',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos_venta`
--

INSERT INTO `productos_venta` (`id`, `user_id`, `cultivo_id`, `cosecha_id`, `cantidad`, `unidad`, `precio_unitario`, `stock`, `estado`, `created_at`, `updated_at`) VALUES
(1, 7, 2, NULL, 2.00, 'kg', 20.00, 0, 'agotado', '2026-05-09 11:43:11', '2026-05-10 15:11:14'),
(2, 7, 14, NULL, 3.00, 'kg', 30.00, 20, 'disponible', '2026-05-09 11:44:21', '2026-05-10 14:14:27'),
(3, 7, 3, NULL, 500.00, 'g', 15.00, 12, 'disponible', '2026-05-10 15:08:39', '2026-05-10 15:08:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes`
--

CREATE TABLE `reportes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `periodo_inicio` date DEFAULT NULL,
  `periodo_fin` date DEFAULT NULL,
  `formato` enum('PDF','Excel','CSV') NOT NULL DEFAULT 'PDF',
  `archivo_url` varchar(255) DEFAULT NULL,
  `tamaño_kb` int(10) UNSIGNED DEFAULT NULL,
  `parametros` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`parametros`)),
  `descargado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reportes`
--

INSERT INTO `reportes` (`id`, `user_id`, `nombre`, `tipo`, `periodo_inicio`, `periodo_fin`, `formato`, `archivo_url`, `tamaño_kb`, `parametros`, `descargado`, `created_at`, `updated_at`) VALUES
(1, 7, 'Reporte de Cultivos - 13/05/2026 06:54', 'cultivos', '2026-05-06', '2026-05-13', 'PDF', '#', 1, '{\"tipo\":\"cultivos\",\"periodo\":\"semana\",\"formato\":\"PDF\"}', 0, '2026-05-13 12:54:44', '2026-05-13 12:54:44'),
(2, 7, 'Reporte de Siembras - 13/05/2026 06:56', 'siembras', '2026-04-13', '2026-05-13', 'PDF', '#', 1, '{\"tipo\":\"siembras\",\"periodo\":\"mes\",\"formato\":\"PDF\"}', 0, '2026-05-13 12:56:00', '2026-05-13 12:56:00'),
(3, 7, 'Reporte de Cosechas - 07/06/2026 06:29', 'cosechas', '2026-05-07', '2026-06-07', 'CSV', '#', 1, '{\"tipo\":\"cosechas\",\"periodo\":\"mes\",\"formato\":\"CSV\"}', 0, '2026-06-07 12:29:42', '2026-06-07 12:29:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sensores`
--

CREATE TABLE `sensores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `modulo_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `tipo` enum('Temperatura','Humedad','Luz','pH','Nutrientes') NOT NULL,
  `unidad` varchar(10) NOT NULL,
  `ubicacion` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `ultima_lectura` decimal(8,2) DEFAULT NULL,
  `ultima_lectura_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sensores`
--

INSERT INTO `sensores` (`id`, `modulo_id`, `nombre`, `tipo`, `unidad`, `ubicacion`, `activo`, `ultima_lectura`, `ultima_lectura_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'S-TEMP-01', 'Temperatura', '?C', 'Centro', 1, NULL, NULL, '2026-03-14 18:32:12', '2026-03-14 18:32:12'),
(2, 1, 'S-HUM-01', 'Humedad', '%', 'Charola 1', 1, NULL, NULL, '2026-03-14 18:32:12', '2026-03-14 18:32:12'),
(3, 1, 'S-LUX-01', 'Luz', 'lux', 'Superior', 1, NULL, NULL, '2026-03-14 18:32:12', '2026-03-14 18:32:12'),
(4, 2, 'S-TEMP-02', 'Temperatura', '?C', 'Centro', 1, NULL, NULL, '2026-03-14 18:32:12', '2026-03-14 18:32:12'),
(5, 2, 'S-HUM-02', 'Humedad', '%', 'Charola 2', 1, NULL, NULL, '2026-03-14 18:32:12', '2026-03-14 18:32:12'),
(6, 2, 'S-LUX-02', 'Luz', 'lux', 'Superior', 1, NULL, NULL, '2026-03-14 18:32:12', '2026-03-14 18:32:12'),
(7, 3, 'S-TEMP-03', 'Temperatura', '?C', 'Centro', 1, NULL, NULL, '2026-03-14 18:32:12', '2026-03-14 18:32:12'),
(8, 3, 'S-HUM-03', 'Humedad', '%', 'Charola 1', 1, NULL, NULL, '2026-03-14 18:32:12', '2026-03-14 18:32:12'),
(9, 4, 'S-TEMP-04', 'Temperatura', '?C', 'Centro', 1, NULL, NULL, '2026-03-14 18:32:12', '2026-03-14 18:32:12'),
(10, 4, 'S-HUM-04', 'Humedad', '%', 'Charola 3', 1, NULL, NULL, '2026-03-14 18:32:12', '2026-03-14 18:32:12'),
(11, 4, 'S-PH-01', 'pH', 'pH', 'Soluci?n', 1, NULL, NULL, '2026-03-14 18:32:12', '2026-03-14 18:32:12'),
(12, 7, 'Sensor Temp Invernadero', 'Temperatura', '?C', 'Centro del invernadero', 1, 50.00, '2026-06-10 02:48:50', '2026-05-12 17:47:07', '2026-06-10 02:48:50'),
(13, 7, 'Sensor Humedad Charola 1 - R?bano', 'Humedad', '%', 'Charola 1', 1, NULL, NULL, '2026-05-12 17:47:07', '2026-05-12 17:47:07'),
(14, 7, 'Sensor Humedad Charola 2 - Lechuga', 'Humedad', '%', 'Charola 2', 1, NULL, NULL, '2026-05-12 17:47:07', '2026-05-12 17:47:07'),
(15, 7, 'Sensor Humedad Charola 3 - Espinaca', 'Humedad', '%', 'Charola 3', 1, NULL, NULL, '2026-05-12 17:47:07', '2026-05-12 17:47:07'),
(16, 7, 'Sensor Humedad Charola 4 - Cilantro', 'Humedad', '%', 'Charola 4', 1, NULL, NULL, '2026-05-12 17:47:07', '2026-05-12 17:47:07'),
(17, 7, 'Sensor Luz Invernadero', 'Luz', 'lux', 'Parte superior', 1, 50.00, '2026-06-10 02:48:50', '2026-05-12 17:47:07', '2026-06-10 02:48:50'),
(18, 7, 'Sensor pH Soluci?n', 'pH', 'pH', 'Soluci?n nutritiva', 1, 50.00, '2026-06-10 02:48:50', '2026-05-12 17:47:07', '2026-06-10 02:48:50'),
(19, 7, 'Sensor Altura R?bano', 'Nutrientes', 'cm', 'Charola 1 - R?bano', 1, 50.00, '2026-06-10 02:48:50', '2026-05-12 17:53:22', '2026-06-10 02:48:50'),
(20, 7, 'Sensor Altura Lechuga', 'Nutrientes', 'cm', 'Charola 2 - Lechuga', 1, 50.00, '2026-06-10 02:48:50', '2026-05-12 17:53:22', '2026-06-10 02:48:50'),
(21, 7, 'Sensor Altura Espinaca', 'Nutrientes', 'cm', 'Charola 3 - Espinaca', 1, 50.00, '2026-06-10 02:48:50', '2026-05-12 17:53:22', '2026-06-10 02:48:50'),
(22, 7, 'Sensor Altura Cilantro', 'Nutrientes', 'cm', 'Charola 4 - Cilantro', 1, 50.00, '2026-06-10 02:48:50', '2026-05-12 17:53:22', '2026-06-10 02:48:50'),
(23, 7, 'Sensor Temp DHT22', 'Temperatura', '?C', 'Centro del invernadero - altura 80cm', 1, 50.00, '2026-06-10 02:48:50', '2026-05-13 05:44:07', '2026-06-10 02:48:50'),
(24, 7, 'Sensor Humedad DHT22', 'Humedad', '%', 'Centro del invernadero - altura 80cm', 1, NULL, NULL, '2026-05-13 05:44:07', '2026-05-13 05:44:07'),
(25, 7, 'Sensor Luz LDR', 'Luz', 'lux', 'Parte superior - bajo malla 30% sombra', 1, 50.00, '2026-06-10 02:48:50', '2026-05-13 05:44:07', '2026-06-10 02:48:50'),
(26, 7, 'Humedad Sustrato R?bano', 'Humedad', '%', 'Charola 1 - R?bano (34.5x43 cm)', 1, NULL, NULL, '2026-05-13 05:44:07', '2026-05-13 05:44:07'),
(27, 7, 'Humedad Sustrato Lechuga', 'Humedad', '%', 'Charola 2 - Lechuga (34.5x43 cm)', 1, NULL, NULL, '2026-05-13 05:44:07', '2026-05-13 05:44:07'),
(28, 7, 'Humedad Sustrato Espinaca', 'Humedad', '%', 'Charola 3 - Espinaca (34.5x43 cm)', 1, NULL, NULL, '2026-05-13 05:44:07', '2026-05-13 05:44:07'),
(29, 7, 'Humedad Sustrato Cilantro', 'Humedad', '%', 'Charola 4 - Cilantro (34.5x43 cm)', 1, NULL, NULL, '2026-05-13 05:44:07', '2026-05-13 05:44:07'),
(30, 7, 'Sensor pH', 'pH', 'pH', 'Soluci?n nutritiva - tanque 20L', 1, 50.00, '2026-06-10 02:48:50', '2026-05-13 05:44:07', '2026-06-10 02:48:50'),
(31, 7, 'Sensor Nutrientes TDS', 'Nutrientes', 'ppm', 'Soluci?n nutritiva - tanque 20L', 1, 50.00, '2026-06-10 02:48:50', '2026-05-13 05:44:07', '2026-06-10 02:48:50'),
(32, 7, 'Humedad Sustrato - Lechuga Romana', 'Humedad', '%', 'Charola 2 - Lechuga Romana (34.5x43 cm)', 1, 60.70, '2026-06-10 02:48:50', '2026-05-13 12:02:16', '2026-06-10 02:48:50'),
(33, 7, 'Humedad Sustrato - Espinaca', 'Humedad', '%', 'Charola 3 - Espinaca (34.5x43 cm)', 1, 73.70, '2026-06-10 02:48:50', '2026-05-13 12:05:11', '2026-06-10 02:48:50'),
(34, 7, 'Humedad Sustrato - R?bano', 'Humedad', '%', 'Charola 1 - R?bano (34.5x43 cm)', 1, 69.80, '2026-06-07 11:38:21', '2026-05-13 12:05:11', '2026-06-07 11:38:21'),
(35, 7, 'Humedad Sustrato - Cilantro', 'Humedad', '%', 'Charola 4 - Cilantro (34.5x43 cm)', 1, 63.70, '2026-06-10 02:48:50', '2026-05-13 12:05:11', '2026-06-10 02:48:50'),
(36, 7, 'Temperatura Ambiental', 'Temperatura', '°C', 'General - Invernadero', 1, 20.50, '2026-06-10 02:48:50', '2026-05-13 12:05:11', '2026-06-10 02:48:50'),
(37, 7, 'Luz LED', 'Luz', 'lux', 'General - Invernadero', 1, 3000.00, '2026-06-10 02:48:50', '2026-05-13 12:05:11', '2026-06-10 02:48:50'),
(38, 7, 'pH Solución', 'pH', 'pH', 'General - Invernadero', 1, 6.30, '2026-06-10 02:48:50', '2026-05-13 12:05:11', '2026-06-10 02:48:50'),
(39, 7, 'Nutrientes TDS', 'Nutrientes', 'ppm', 'General - Invernadero', 1, 1200.00, '2026-06-10 02:48:50', '2026-05-13 12:05:11', '2026-06-10 02:48:50'),
(40, 7, 'Humedad R?bano', 'Humedad', '%', 'Charola 1 - R?bano (34.5x43 cm)', 1, 68.00, '2026-05-13 08:06:37', '2026-05-13 08:06:37', '2026-05-13 08:06:37');

-- --------------------------------------------------------


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
('JlFafImsmhLpGcl2MEEQlHJLFhnHlHwHSYbxp5mO', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicEZFZ2ZZNzFpWXRrQnhjWk5xdE5pbG5SakRlT2tOR3czemJOWTlROSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM0OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZXZhbHVhY2lvbmVzIjtzOjU6InJvdXRlIjtzOjE4OiJldmFsdWFjaW9uZXMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo3O30=', 1781038142),
('KhBV0IlmrYgJQaiuHYGV4Ig2ommw9jAPK9pJa1vL', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidWxaVDZJVVhjRGd0Q3VCYko3ZHRDSm5CaWxhUE5NT0JqdWl0bGFIViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jb3NlY2hhcy9wcm94aW1hcyI7czo1OiJyb3V0ZSI7czoxNzoiY29zZWNoYXMucHJveGltYXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo3O30=', 1781626110),
('qmsQg5psbQ2wDDuWKNVxPkxTBv4GTnEOba4bQjl4', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoidnE5c3FwRmhZYXc4RktLc0o5bVFJSzMzdVBOQjJ2U0JwSHV0NmcwUyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvY29zZWNoYXMvcHJveGltYXMiO3M6NToicm91dGUiO3M6MTc6ImNvc2VjaGFzLnByb3hpbWFzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Nzt9', 1780869217),
('wPWZTJnh1g0hghGZckWCEynG7jamg7B6rmWCi6AN', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoielN5b3kzV013ZjcxN0dkeWxWS3RGS0xZZ3NSd0Z5THlWMHVyR1pIVyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvY29zZWNoYXMvcHJveGltYXMiO3M6NToicm91dGUiO3M6MTc6ImNvc2VjaGFzLnByb3hpbWFzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Nzt9', 1780936954),
('xCBm80OVG4EqB4kMRSjbXP3j2L9zkNaBrMug0IDf', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibk5VNUFscGVOSkx1YjhWWEdtMTFrcG42RHZOOGhoVU55aG14bTVXRCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMDoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2N1bHRpdm9zIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jbGllbnRlL3RpZW5kYSI7czo1OiJyb3V0ZSI7czoyMDoiY2xpZW50ZS50aWVuZGEuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxMTt9', 1781625602);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `siembras`
--

CREATE TABLE `siembras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `cultivo_id` bigint(20) UNSIGNED NOT NULL,
  `modulo_id` bigint(20) UNSIGNED NOT NULL,
  `charola` tinyint(3) UNSIGNED NOT NULL,
  `fecha_siembra` date NOT NULL,
  `cantidad_semillas` int(10) UNSIGNED DEFAULT NULL,
  `fecha_estimada_cosecha` date DEFAULT NULL,
  `fecha_cosecha_real` date DEFAULT NULL,
  `estado` enum('Activa','Completada','Problema','Cancelada') DEFAULT 'Activa',
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `siembras`
--

INSERT INTO `siembras` (`id`, `user_id`, `cultivo_id`, `modulo_id`, `charola`, `fecha_siembra`, `cantidad_semillas`, `fecha_estimada_cosecha`, `fecha_cosecha_real`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, '2025-02-15', 10, '2025-03-17', NULL, 'Activa', NULL, '2026-03-14 18:32:11', '2026-03-14 18:32:11'),
(2, 1, 2, 2, 2, '2025-02-20', 15, '2025-03-27', NULL, 'Activa', NULL, '2026-03-14 18:32:11', '2026-03-14 18:32:11'),
(3, 1, 3, 3, 1, '2025-02-10', 20, '2025-03-07', NULL, 'Completada', NULL, '2026-03-14 18:32:11', '2026-03-14 18:32:11'),
(4, 1, 4, 4, 3, '2025-02-05', 8, '2025-04-06', NULL, 'Activa', NULL, '2026-03-14 18:32:11', '2026-03-14 18:32:11'),
(5, 1, 2, 2, 1, '2025-03-01', 12, '2025-04-05', NULL, 'Problema', NULL, '2026-03-14 18:32:11', '2026-03-14 18:32:11'),
(6, 3, 1, 5, 4, '2026-03-15', 5, NULL, '2026-03-15', 'Completada', NULL, '2026-03-16 00:10:35', '2026-03-16 00:39:08'),
(7, 3, 5, 6, 3, '2026-03-15', 5, NULL, '2026-03-15', 'Completada', NULL, '2026-03-16 00:12:40', '2026-03-16 01:31:34'),
(8, 3, 7, 6, 2, '2026-03-15', 5, NULL, '2026-03-15', 'Completada', NULL, '2026-03-16 01:39:32', '2026-03-16 01:39:58'),
(13, 3, 2, 6, 4, '2026-04-07', 5, NULL, NULL, 'Completada', NULL, '2026-04-07 08:14:49', '2026-04-07 08:15:21'),
(15, 7, 1, 7, 2, '2026-04-30', 10, '2026-06-20', NULL, 'Activa', '?? Altura actual: 2.5 cm. ?? Altura ?ptima cosecha: 10-20 cm seg?n variedad.', '2026-05-12 17:46:53', '2026-06-07 21:07:51'),
(16, 7, 2, 7, 3, '2026-04-30', 20, '2026-06-27', NULL, 'Activa', '?? Altura actual: 4.0 cm. ?? Altura ?ptima cosecha: 10-20 cm seg?n variedad.', '2026-05-12 17:46:53', '2026-06-07 21:07:52'),
(17, 7, 8, 7, 1, '2026-04-30', 40, '2026-06-06', '2026-06-06', 'Completada', '?? Altura actual: 6.5 cm. ?? Altura ?ptima cosecha: 10-20 cm seg?n variedad.', '2026-05-12 17:46:55', '2026-06-07 21:08:31'),
(21, 7, 7, 7, 4, '2026-04-30', 25, '2026-06-13', NULL, 'Activa', '?? Altura actual: 3.0 cm. ?? Altura ?ptima cosecha: 10-20 cm seg?n variedad.', '2026-05-12 17:52:52', '2026-06-07 21:10:59');

--
-- Disparadores `siembras`
--
DELIMITER $$
CREATE TRIGGER `after_siembra_update` AFTER UPDATE ON `siembras` FOR EACH ROW BEGIN
    IF NEW.estado = 'Completada' AND OLD.estado != 'Completada' THEN
        
        UPDATE alertas 
        SET estado = 'Resuelta', 
            fecha_resolucion = NOW(),
            updated_at = NOW()
        WHERE siembra_id = NEW.id 
          AND estado = 'Pendiente';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `siembras_detalle`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `siembras_detalle` (
`id` bigint(20) unsigned
,`user_id` bigint(20) unsigned
,`usuario` varchar(201)
,`cultivo` varchar(100)
,`tipo_cultivo` enum('Hoja','Fruto','Arom?tica','Ra?z','Otro')
,`modulo` varchar(50)
,`ubicacion` varchar(100)
,`charola` tinyint(3) unsigned
,`fecha_siembra` date
,`fecha_estimada_cosecha` date
,`fecha_cosecha_real` date
,`estado` enum('Activa','Completada','Problema','Cancelada')
,`dias_restantes` int(7)
,`porcentaje_progreso` decimal(10,0)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('cliente','vendedor','admin') NOT NULL DEFAULT 'cliente',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `nombre`, `apellido`, `email`, `role`, `email_verified_at`, `password`, `telefono`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Christopher', 'Kevin', 'christopher@example.com', 'vendedor', NULL, '$2y$12$85WpKWjiszbt1LE7oyB/J.QMtLLhSJixHOPeYoaMc1MCqpwei/Kl2', '+52 55 1234 5678', 'https://ui-avatars.com/api/?name=Christopher+Kevin&background=2E7D32&color=fff&size=40', NULL, '2026-03-14 18:32:09', '2026-05-10 12:42:15'),
(2, 'Mar?a', 'Garc?a', 'maria@example.com', 'vendedor', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+52 55 8765 4321', 'https://ui-avatars.com/api/?name=Maria+Garcia&background=2E7D32&color=fff&size=40', NULL, '2026-03-14 18:32:09', '2026-05-10 12:36:22'),
(3, 'Kai', 'Beckett', 'kc234204@gmail.com', 'vendedor', NULL, '$2y$12$0BGah5FEjVc4Y1f0kwdqLOF/OXF2eOOZapGbB.MIgZrkiFGATOv6G', NULL, 'https://ui-avatars.com/api/?name=Kai%2BBeckett&background=2E7D32&color=fff&size=40', NULL, '2026-03-15 01:01:46', '2026-05-10 12:36:22'),
(4, 'Perla', 'Gardu?o', 'perla2308@gmail.com', 'vendedor', NULL, '$2y$12$wt0qV2eDBcJv2NJbXItRn.E25EGfwh7Xz9oF10szyv8maLQBeAlCG', NULL, 'https://ui-avatars.com/api/?name=Perla%2BGardu%C3%B1o&background=2E7D32&color=fff&size=40', NULL, '2026-03-17 22:53:28', '2026-05-10 12:36:22'),
(5, 'Natalia', 'Guadarrama Cambr?n', 'natalia@gmail.com', 'vendedor', NULL, '$2y$12$0tSzwglOfiOG.XDJRl1Bh.mJe4aES7JzSZNwHG00QH0cpsq3COL/2', NULL, 'https://ui-avatars.com/api/?name=Natalia%2BGuadarrama+Cambr%C3%B3n&background=2E7D32&color=fff&size=40', NULL, '2026-04-06 19:46:18', '2026-05-10 12:36:22'),
(6, 'Pedro', 'Guillermo', 'l20223@gmail.com', 'vendedor', NULL, '$2y$12$q5Ye2Yi2SJtiBvyHrwmErusmHQq230S1CjO2dU34FFQ9Gd8fh3nA2', NULL, 'https://ui-avatars.com/api/?name=Pedro%2BGuillermo&background=2E7D32&color=fff&size=40', NULL, '2026-04-24 14:49:16', '2026-05-10 12:36:22'),
(7, 'Liz', 'Fabila', 'lizbethfabila812@gmail.com', 'vendedor', NULL, '$2y$12$AEgzrmU7ki6dvylLSrWuxOkKgD7eDa3IedPIznWvCBULJHvtKAPSC', NULL, 'https://ui-avatars.com/api/?name=Liz%2BFabila&background=2E7D32&color=fff&size=40', 'mhZuzcCcyZqRG67Rq8WBKRxvAaeMeDPytKjWthDISnlw4H77cbc2atEDffEZ', '2026-05-08 23:30:22', '2026-05-10 12:36:22'),
(8, 'Cliente', 'Prueba', 'cliente@test.com', 'cliente', NULL, '$2y$12$AMp9EohFS0tExR7vj1Nz1upTCfTbfVWZnOHognNW9jMp3/ZiPgo9q', NULL, 'https://ui-avatars.com/api/?name=Cliente+Prueba&background=2E7D32&color=fff&size=40', 'kpnWQfk2fB9QeURkieP83LAS4yn7S5OYwVYYEqsfga5tkkOhViFn2fL5Hdcw', '2026-05-10 12:36:22', '2026-05-13 02:28:55'),
(9, 'Vendedor', 'Prueba', 'vendedor@test.com', 'vendedor', NULL, '$2y$12$MTRKa27AEyGTdcS1Wujiy.Usw9VXWuJo8DP9p/HjvQ7/UzYVP4csu', NULL, 'https://ui-avatars.com/api/?name=Vendedor+Prueba&background=2E7D32&color=fff&size=40', 'OVdRQMfFde78T1tPF2LvqFhat0Ft2BaRea86718BWGVy89qxoqeT006BxKVS', '2026-05-10 12:36:22', '2026-05-10 08:10:13'),
(10, 'Jisel', 'Martinez', 'jisel@gmail.com', 'cliente', NULL, '$2y$12$PfyrCe/E27WwzvqpISU5nuBdkJ1uFbxL9HG0CNFPNFBlrxPa5aPv6', NULL, 'https://ui-avatars.com/api/?name=Jisel%2BMartinez&background=2E7D32&color=fff&size=40', NULL, '2026-05-10 12:43:37', '2026-05-10 12:43:37'),
(11, 'Kevin', 'Cruz', 'kevin12@gmail.com', 'cliente', NULL, '$2y$12$ZS/XoAgr4OMWWs7RBYEwSeF6byDP4urtBPdOXlKgs1QFXdaOwkK7e', NULL, 'https://ui-avatars.com/api/?name=Kevin%2BCruz&background=2E7D32&color=fff&size=40', NULL, '2026-06-16 22:00:01', '2026-06-16 22:00:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id_vendedor` bigint(20) UNSIGNED NOT NULL,
  `user_id_cliente` bigint(20) UNSIGNED NOT NULL,
  `pedido_id` bigint(20) UNSIGNED NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha_venta` date NOT NULL,
  `estado` enum('completada','cancelada') NOT NULL DEFAULT 'completada',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `user_id_vendedor`, `user_id_cliente`, `pedido_id`, `total`, `fecha_venta`, `estado`, `created_at`, `updated_at`) VALUES
(1, 7, 7, 1, 69.60, '2026-05-09', 'completada', '2026-05-09 11:50:28', '2026-05-09 11:50:28'),
(2, 7, 7, 2, 34.80, '2026-05-09', 'completada', '2026-05-09 11:55:41', '2026-05-09 11:55:41'),
(3, 7, 8, 3, 23.20, '2026-05-10', 'completada', '2026-05-10 13:11:32', '2026-05-10 13:11:32'),
(4, 7, 8, 4, 23.20, '2026-05-10', 'completada', '2026-05-10 13:11:42', '2026-05-10 13:11:42'),
(5, 7, 8, 5, 23.20, '2026-05-10', 'completada', '2026-05-10 13:11:49', '2026-05-10 13:11:49'),
(6, 7, 8, 6, 34.80, '2026-05-10', 'completada', '2026-05-10 13:24:44', '2026-05-10 13:24:44'),
(7, 7, 8, 7, 116.00, '2026-05-10', 'completada', '2026-05-10 15:11:14', '2026-05-10 15:11:14');

-- --------------------------------------------------------

--
-- Estructura para la vista `alertas_activas`
--
DROP TABLE IF EXISTS `alertas_activas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `alertas_activas`  AS SELECT `a`.`id` AS `id`, `a`.`user_id` AS `user_id`, concat(`u`.`nombre`,' ',`u`.`apellido`) AS `usuario`, `a`.`titulo` AS `titulo`, `a`.`mensaje` AS `mensaje`, `a`.`prioridad` AS `prioridad`, `a`.`created_at` AS `created_at`, timestampdiff(MINUTE,`a`.`created_at`,current_timestamp()) AS `minutos_transcurridos`, CASE WHEN timestampdiff(HOUR,`a`.`created_at`,current_timestamp()) < 1 THEN 'Reciente' WHEN timestampdiff(HOUR,`a`.`created_at`,current_timestamp()) < 24 THEN 'Hoy' ELSE 'Antigua' END AS `antiguedad`, `m`.`nombre` AS `modulo`, `s`.`nombre` AS `sensor` FROM (((`alertas` `a` join `users` `u` on(`a`.`user_id` = `u`.`id`)) left join `modulos` `m` on(`a`.`modulo_id` = `m`.`id`)) left join `sensores` `s` on(`a`.`sensor_id` = `s`.`id`)) WHERE `a`.`estado` = 'Pendiente' ORDER BY field(`a`.`prioridad`,'Crítica','Alta','Media','Baja') ASC, `a`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `dashboard_resumen`
--
DROP TABLE IF EXISTS `dashboard_resumen`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `dashboard_resumen`  AS SELECT `u`.`id` AS `user_id`, (select count(0) from `siembras` where `siembras`.`user_id` = `u`.`id` and `siembras`.`estado` = 'Activa') AS `siembras_activas`, (select count(0) from `siembras` where `siembras`.`user_id` = `u`.`id`) AS `total_siembras`, (select count(0) from `alertas` where `alertas`.`user_id` = `u`.`id` and `alertas`.`estado` = 'Pendiente') AS `alertas_pendientes`, (select count(0) from `modulos` where `modulos`.`user_id` = `u`.`id` and `modulos`.`activo` = 1) AS `modulos_activos`, (select count(0) from `cosechas` where `cosechas`.`user_id` = `u`.`id`) AS `total_cosechas`, (select coalesce(sum(`cosechas`.`cantidad_kg`),0) from `cosechas` where `cosechas`.`user_id` = `u`.`id` and `cosechas`.`fecha_cosecha` >= curdate() - interval 30 day) AS `kg_ultimo_mes`, (select coalesce(avg(`e`.`rendimiento`),0) from (`evaluaciones` `e` join `siembras` `s` on(`e`.`siembra_id` = `s`.`id`)) where `s`.`user_id` = `u`.`id`) AS `rendimiento_promedio` FROM `users` AS `u` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `estadisticas_cosechas`
--
DROP TABLE IF EXISTS `estadisticas_cosechas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `estadisticas_cosechas`  AS SELECT `u`.`id` AS `user_id`, concat(`u`.`nombre`,' ',`u`.`apellido`) AS `usuario`, year(`c`.`fecha_cosecha`) AS `año`, month(`c`.`fecha_cosecha`) AS `mes`, count(0) AS `total_cosechas`, sum(`c`.`cantidad_kg`) AS `total_kg`, avg(`c`.`cantidad_kg`) AS `promedio_kg`, count(case when `c`.`calidad` = 'Excelente' then 1 end) AS `excelentes`, count(case when `c`.`calidad` = 'Buena' then 1 end) AS `buenas`, count(case when `c`.`calidad` = 'Regular' then 1 end) AS `regulares`, count(case when `c`.`calidad` = 'Mala' then 1 end) AS `malas` FROM (`users` `u` join `cosechas` `c` on(`u`.`id` = `c`.`user_id`)) GROUP BY `u`.`id`, `u`.`nombre`, `u`.`apellido`, year(`c`.`fecha_cosecha`), month(`c`.`fecha_cosecha`) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `monitoreo_actual`
--
DROP TABLE IF EXISTS `monitoreo_actual`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `monitoreo_actual`  AS SELECT `m`.`id` AS `modulo_id`, `m`.`nombre` AS `modulo`, `m`.`user_id` AS `user_id`, `s`.`id` AS `sensor_id`, `s`.`nombre` AS `sensor`, `s`.`tipo` AS `tipo`, `s`.`unidad` AS `unidad`, `l`.`valor` AS `valor`, `l`.`created_at` AS `ultima_lectura`, timestampdiff(MINUTE,`l`.`created_at`,current_timestamp()) AS `minutos_desde_lectura`, CASE WHEN timestampdiff(MINUTE,`l`.`created_at`,current_timestamp()) > 60 THEN 'Obsoleto' ELSE 'Actualizado' END AS `estado_lectura` FROM ((`modulos` `m` join `sensores` `s` on(`m`.`id` = `s`.`modulo_id`)) left join `lecturas_sensores` `l` on(`s`.`id` = `l`.`sensor_id`)) WHERE `l`.`id` in (select max(`lecturas_sensores`.`id`) from `lecturas_sensores` group by `lecturas_sensores`.`sensor_id`) OR `l`.`id` is null ;

-- --------------------------------------------------------

--
-- Estructura para la vista `siembras_detalle`
--
DROP TABLE IF EXISTS `siembras_detalle`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `siembras_detalle`  AS SELECT `s`.`id` AS `id`, `s`.`user_id` AS `user_id`, concat(`u`.`nombre`,' ',`u`.`apellido`) AS `usuario`, `c`.`nombre` AS `cultivo`, `c`.`tipo` AS `tipo_cultivo`, `m`.`nombre` AS `modulo`, `m`.`ubicacion` AS `ubicacion`, `s`.`charola` AS `charola`, `s`.`fecha_siembra` AS `fecha_siembra`, `s`.`fecha_estimada_cosecha` AS `fecha_estimada_cosecha`, `s`.`fecha_cosecha_real` AS `fecha_cosecha_real`, `s`.`estado` AS `estado`, to_days(`s`.`fecha_estimada_cosecha`) - to_days(curdate()) AS `dias_restantes`, CASE WHEN `s`.`estado` = 'Completada' THEN 100 WHEN `s`.`fecha_estimada_cosecha` is null THEN 0 ELSE least(round((to_days(curdate()) - to_days(`s`.`fecha_siembra`)) * 100.0 / greatest(to_days(`s`.`fecha_estimada_cosecha`) - to_days(`s`.`fecha_siembra`),1),0),100) END AS `porcentaje_progreso` FROM (((`siembras` `s` join `users` `u` on(`s`.`user_id` = `u`.`id`)) join `cultivos` `c` on(`s`.`cultivo_id` = `c`.`id`)) join `modulos` `m` on(`s`.`modulo_id` = `m`.`id`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_alertas_user` (`user_id`),
  ADD KEY `idx_alertas_modulo` (`modulo_id`),
  ADD KEY `idx_alertas_sensor` (`sensor_id`),
  ADD KEY `idx_alertas_siembra` (`siembra_id`),
  ADD KEY `idx_alertas_estado` (`estado`),
  ADD KEY `idx_alertas_prioridad` (`prioridad`),
  ADD KEY `idx_alertas_created` (`created_at`),
  ADD KEY `idx_alertas_pendientes` (`user_id`,`estado`,`prioridad`,`created_at`);

--
-- Indices de la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auditorias_user_id_foreign` (`user_id`);

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
-- Indices de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_configuraciones_user_tipo_clave` (`user_id`,`tipo`,`clave`),
  ADD KEY `idx_configuraciones_user` (`user_id`),
  ADD KEY `idx_configuraciones_tipo` (`tipo`);

--
-- Indices de la tabla `cosechas`
--
ALTER TABLE `cosechas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_cosechas_siembra` (`siembra_id`),
  ADD KEY `idx_cosechas_user` (`user_id`),
  ADD KEY `idx_cosechas_fecha` (`fecha_cosecha`),
  ADD KEY `idx_cosechas_calidad` (`calidad`);

--
-- Indices de la tabla `crecimiento`
--
ALTER TABLE `crecimiento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crecimiento_siembra_id_foreign` (`siembra_id`);

--
-- Indices de la tabla `cultivos`
--
ALTER TABLE `cultivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cultivos_nombre` (`nombre`),
  ADD KEY `idx_cultivos_tipo` (`tipo`),
  ADD KEY `idx_cultivos_activo` (`activo`);

--
-- Indices de la tabla `direcciones_envio`
--
ALTER TABLE `direcciones_envio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `direcciones_envio_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_evaluaciones_siembra` (`siembra_id`),
  ADD KEY `idx_evaluaciones_user` (`user_id`),
  ADD KEY `idx_evaluaciones_fecha` (`fecha_evaluacion`);

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
-- Indices de la tabla `lecturas_sensores`
--
ALTER TABLE `lecturas_sensores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lecturas_sensor` (`sensor_id`),
  ADD KEY `idx_lecturas_created` (`created_at`),
  ADD KEY `idx_lecturas_sensor_fecha` (`sensor_id`,`created_at`);

--
-- Indices de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_modulos_user_nombre` (`user_id`,`nombre`),
  ADD KEY `idx_modulos_user` (`user_id`),
  ADD KEY `idx_modulos_activo` (`activo`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedidos_user_id_cliente_foreign` (`user_id_cliente`),
  ADD KEY `pedidos_user_id_vendedor_foreign` (`user_id_vendedor`),
  ADD KEY `pedidos_id_direccion_envio_foreign` (`id_direccion_envio`),
  ADD KEY `pedidos_id_metodo_pago_foreign` (`id_metodo_pago`);

--
-- Indices de la tabla `pedidos_detalle`
--
ALTER TABLE `pedidos_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedidos_detalle_pedido_id_foreign` (`pedido_id`),
  ADD KEY `pedidos_detalle_producto_venta_id_foreign` (`producto_venta_id`);

--
-- Indices de la tabla `productos_venta`
--
ALTER TABLE `productos_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `productos_venta_user_id_foreign` (`user_id`),
  ADD KEY `productos_venta_cultivo_id_foreign` (`cultivo_id`),
  ADD KEY `productos_venta_cosecha_id_foreign` (`cosecha_id`);

--
-- Indices de la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reportes_user_id_index` (`user_id`),
  ADD KEY `reportes_tipo_index` (`tipo`),
  ADD KEY `reportes_created_at_index` (`created_at`);

--
-- Indices de la tabla `sensores`
--
ALTER TABLE `sensores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_sensores_modulo_nombre` (`modulo_id`,`nombre`),
  ADD KEY `idx_sensores_modulo` (`modulo_id`),
  ADD KEY `idx_sensores_tipo` (`tipo`),
  ADD KEY `idx_sensores_activo` (`activo`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `siembras`
--
ALTER TABLE `siembras`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_siembras_modulo_charola_activa` (`modulo_id`,`charola`,`estado`),
  ADD KEY `idx_siembras_user` (`user_id`),
  ADD KEY `idx_siembras_cultivo` (`cultivo_id`),
  ADD KEY `idx_siembras_modulo` (`modulo_id`),
  ADD KEY `idx_siembras_estado` (`estado`),
  ADD KEY `idx_siembras_fecha_siembra` (`fecha_siembra`),
  ADD KEY `idx_siembras_fecha_cosecha_estimada` (`fecha_estimada_cosecha`),
  ADD KEY `idx_siembras_user_estado` (`user_id`,`estado`,`fecha_estimada_cosecha`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_created` (`created_at`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ventas_user_id_vendedor_foreign` (`user_id_vendedor`),
  ADD KEY `ventas_user_id_cliente_foreign` (`user_id_cliente`),
  ADD KEY `ventas_pedido_id_foreign` (`pedido_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alertas`
--
ALTER TABLE `alertas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `auditorias`
--
ALTER TABLE `auditorias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `cosechas`
--
ALTER TABLE `cosechas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `crecimiento`
--
ALTER TABLE `crecimiento`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `cultivos`
--
ALTER TABLE `cultivos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `direcciones_envio`
--
ALTER TABLE `direcciones_envio`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- AUTO_INCREMENT de la tabla `lecturas_sensores`
--
ALTER TABLE `lecturas_sensores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=323;

--
-- AUTO_INCREMENT de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `pedidos_detalle`
--
ALTER TABLE `pedidos_detalle`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `productos_venta`
--
ALTER TABLE `productos_venta`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `reportes`
--
ALTER TABLE `reportes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sensores`
--
ALTER TABLE `sensores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `siembras`
--
ALTER TABLE `siembras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD CONSTRAINT `alertas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `alertas_ibfk_2` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `alertas_ibfk_3` FOREIGN KEY (`sensor_id`) REFERENCES `sensores` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `alertas_ibfk_4` FOREIGN KEY (`siembra_id`) REFERENCES `siembras` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD CONSTRAINT `auditorias_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  ADD CONSTRAINT `configuraciones_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cosechas`
--
ALTER TABLE `cosechas`
  ADD CONSTRAINT `cosechas_ibfk_1` FOREIGN KEY (`siembra_id`) REFERENCES `siembras` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cosechas_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `crecimiento`
--
ALTER TABLE `crecimiento`
  ADD CONSTRAINT `crecimiento_siembra_id_foreign` FOREIGN KEY (`siembra_id`) REFERENCES `siembras` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `direcciones_envio`
--
ALTER TABLE `direcciones_envio`
  ADD CONSTRAINT `direcciones_envio_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  ADD CONSTRAINT `evaluaciones_ibfk_1` FOREIGN KEY (`siembra_id`) REFERENCES `siembras` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluaciones_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `lecturas_sensores`
--
ALTER TABLE `lecturas_sensores`
  ADD CONSTRAINT `lecturas_sensores_ibfk_1` FOREIGN KEY (`sensor_id`) REFERENCES `sensores` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD CONSTRAINT `modulos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_id_direccion_envio_foreign` FOREIGN KEY (`id_direccion_envio`) REFERENCES `direcciones_envio` (`id`),
  ADD CONSTRAINT `pedidos_id_metodo_pago_foreign` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodos_pago` (`id`),
  ADD CONSTRAINT `pedidos_user_id_cliente_foreign` FOREIGN KEY (`user_id_cliente`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedidos_user_id_vendedor_foreign` FOREIGN KEY (`user_id_vendedor`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedidos_detalle`
--
ALTER TABLE `pedidos_detalle`
  ADD CONSTRAINT `pedidos_detalle_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedidos_detalle_producto_venta_id_foreign` FOREIGN KEY (`producto_venta_id`) REFERENCES `productos_venta` (`id`);

--
-- Filtros para la tabla `productos_venta`
--
ALTER TABLE `productos_venta`
  ADD CONSTRAINT `productos_venta_cosecha_id_foreign` FOREIGN KEY (`cosecha_id`) REFERENCES `cosechas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `productos_venta_cultivo_id_foreign` FOREIGN KEY (`cultivo_id`) REFERENCES `cultivos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `productos_venta_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD CONSTRAINT `reportes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sensores`
--
ALTER TABLE `sensores`
  ADD CONSTRAINT `sensores_ibfk_1` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `siembras`
--
ALTER TABLE `siembras`
  ADD CONSTRAINT `siembras_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `siembras_ibfk_2` FOREIGN KEY (`cultivo_id`) REFERENCES `cultivos` (`id`),
  ADD CONSTRAINT `siembras_ibfk_3` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `ventas_user_id_cliente_foreign` FOREIGN KEY (`user_id_cliente`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ventas_user_id_vendedor_foreign` FOREIGN KEY (`user_id_vendedor`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
