-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-10-2024 a las 00:48:23
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
-- Base de datos: `login_system`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES
(1, 'johan', 'johan1@gmail.com', '$2y$10$a0whrSWs7uMxsDnVAzgz/uHCA/jv51R1VVMbZ5kK0fZHDQfDKE9I.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `visitas`
--

CREATE TABLE `visitas` (
  `id` int(11) NOT NULL,
  `dni` varchar(8) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipopersona` varchar(50) DEFAULT NULL,
  `nomoficina` varchar(100) DEFAULT NULL,
  `smotivo` varchar(100) DEFAULT NULL,
  `lugar` varchar(100) DEFAULT NULL,
  `fecha` date NOT NULL DEFAULT current_timestamp(),
  `hora_ingreso` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `visitas`
--

INSERT INTO `visitas` (`id`, `dni`, `nombre`, `tipopersona`, `nomoficina`, `smotivo`, `lugar`, `fecha`, `hora_ingreso`, `hora_salida`, `observaciones`) VALUES
(1, '72132601', 'JHANN MARCO CAMPOS TOLEDO', 'Persona Natural', 'DRE/SERVICIOS GENERALES', 'Reunion de trabajo', 'DRE', '2024-10-09', NULL, '17:14:52', ''),
(2, '71924247', '', '0', 'DRE/SERVICIOS GENERALES', '0', '', '2024-10-09', NULL, '17:41:28', 'as'),
(3, '72132601', '', '0', 'DRE/SERVICIOS GENERALES', '0', '', '2024-10-09', NULL, '17:41:25', 'asas'),
(4, '71924247', '', 'Persona Natural', 'DRE/SERVICIOS GENERALES', 'Reunion de trabajo', 'alo', '2024-10-09', NULL, '17:41:24', 'asassa'),
(5, '71924247', 'JOHANN CRISTOPHER ABAD CAMPOS', 'Persona Natural', 'DRE/SERVICIOS GENERALES', 'Reunion de trabajo', 'Ejem', '2024-10-09', NULL, '16:49:42', NULL),
(6, '71924247', 'JOHANN CRISTOPHER ABAD CAMPOS', '0', 'DRE/SERVICIOS GENERALES', 'Reunion de trabajo', 'DRE', '2024-10-09', NULL, '16:49:40', NULL),
(7, '71924247', 'JOHANN CRISTOPHER ABAD CAMPOS', '0', 'DRE/SERVICIOS GENERALES', 'Reunion de trabajo', 'DRE', '2024-10-09', '23:50:37', '16:49:39', NULL),
(8, '71924247', 'JOHANN CRISTOPHER ABAD CAMPOS', '0', 'DRE/SERVICIOS GENERALES', 'Reunion de trabajo', 'DRE', '2024-10-09', '11:59:20', '16:49:37', NULL),
(9, '71924247', 'JOHANN CRISTOPHER ABAD CAMPOS', '0', 'DRE/SERVICIOS GENERALES', 'Reunion de trabajo', 'DRE', '2024-10-09', '17:04:01', '16:49:36', NULL),
(10, '71924247', 'JOHANN CRISTOPHER ABAD CAMPOS', 'Persona Natural', 'DRE/SERVICIOS GENERALES', 'Reunion de trabajo', 'DRE', '2024-10-09', '17:49:10', '16:49:35', NULL),
(11, '72132601', 'JHANN MARCO CAMPOS TOLEDO', '0', 'DRE/SERVICIOS GENERALES', 'Reunion de trabajo', 'SERVICIOS GENERALES', '2024-10-10', '17:49:18', '16:49:33', NULL),
(12, '71924257', 'FIORELLA ANDREA ESCURRA ARTEAGA', 'Persona Natural', 'DRE/SERVICIOS GENERALES', 'Reunion de trabajo', 'SERVICIOS GENERALES', '2024-10-10', '17:52:35', '16:49:32', NULL),
(13, '72802969', '', '0', 'DRE/SERVICIOS GENERALES', '0', '', '2024-10-14', '16:08:09', '16:49:30', NULL),
(14, '72802969', 'ALEJANDRO MARCIAL RUBINA SOLORZANO', 'Entidad Privada', 'DRE/SERVICIOS GENERALES', 'Reunion de trabajo', 'EDIFICIO B -2 PISO', '2024-10-14', '16:25:57', '16:49:29', NULL),
(15, '72132645', 'JUNIOR EDGAR TORIBIO ESPIRITU', '0', '', 'Gestion de intereses', 'DIRECCIÓN DE GESTIÓN INSTITUCIONAL', '2024-10-14', '16:41:32', '16:49:27', NULL),
(16, '', '', '0', '', 'Otros', 'DIRECCIÓN REGIONAL DE EDUCACIÓN-TRAMITE DOCUMENTARIO', '2024-10-14', '16:42:23', '16:49:25', NULL),
(17, '71924247', 'JOHANN CRISTOPHER ABAD CAMPOS', 'Persona Natural', '', 'Reunion de trabajo', 'DIRECCIÓN REGIONAL DE EDUCACIÓN-TRAMITE DOCUMENTARIO', '2024-10-14', '16:50:59', NULL, NULL),
(18, '71924247', 'JOHANN CRISTOPHER ABAD CAMPOS', 'Persona Natural', '', 'Reunion de trabajo', 'DIRECCIÓN REGIONAL DE EDUCACIÓN-TRAMITE DOCUMENTARIO', '2024-10-14', '16:53:53', NULL, NULL),
(19, '71924247', 'JOHANN CRISTOPHER ABAD CAMPOS', 'Persona Natural', NULL, 'Reunion de trabajo', 'DIRECCIÓN REGIONAL DE EDUCACIÓN-TRAMITE DOCUMENTARIO', '2024-10-14', '17:15:15', NULL, NULL),
(20, '', '', '0', NULL, '', '', '2024-10-14', '17:16:00', '17:22:13', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `visitas`
--
ALTER TABLE `visitas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `visitas`
--
ALTER TABLE `visitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
