-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-11-2024 a las 21:20:28
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
-- Base de datos: `projectdwes_1term`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appuser`
--

DROP DATABASE IF EXISTS projectdwes_1term;
CREATE DATABASE projectdwes_1term;
USE projectdwes_1term;

CREATE TABLE IF NOT EXISTS `appuser` (
  `idUser` int(10) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `passwd` varchar(16) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `rol` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `appuser`
--

INSERT INTO `appuser` (`idUser`, `email`, `passwd`, `name`, `lastname`, `rol`) VALUES
(1, 'alexmm@empresa.com', 'alexmm', 'Álex', 'Mayo Martín', 2),
(2, 'danivs@soporte.empresa.com', 'danivs', 'Dani', 'Vals Simón', 1),
(3, 'ivanag@soporte.empresa.com', 'ivanag', 'Iván', 'Arroyo González', 1),
(4, 'daniss@sempresa.com', 'daniss', 'Daniel', 'Sierra Solís', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `priority`
--

CREATE TABLE IF NOT EXISTS  `priority` (
  `idPr` int(1) NOT NULL,
  `name` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `priority`
--

INSERT INTO `priority` (`idPr`, `name`) VALUES
(2, 'high'),
(4, 'low'),
(3, 'standard'),
(1, 'very high');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE  IF NOT EXISTS `rol` (
  `idRol` int(1) NOT NULL,
  `name` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`idRol`, `name`) VALUES
(2, 'employee'),
(1, 'technician');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `state`
--

CREATE TABLE IF NOT EXISTS  `state` (
  `idState` int(1) NOT NULL,
  `name` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `state`
--

INSERT INTO `state` (`idState`, `name`) VALUES
(3, 'closed'),
(2, 'in progress'),
(1, 'solved');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticket`
--

CREATE TABLE IF NOT EXISTS  `ticket` (
  `idTicket` int(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `priority` int(1) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `messBody` varchar(255) DEFAULT NULL,
  `state` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `appuser`
--
ALTER TABLE `appuser`
  ADD PRIMARY KEY (`idUser`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `rol` (`rol`);

--
-- Indices de la tabla `priority`
--
ALTER TABLE `priority`
  ADD PRIMARY KEY (`idPr`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idRol`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`idState`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`idTicket`),
  ADD KEY `email` (`email`),
  ADD KEY `priority` (`priority`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `appuser`
--
ALTER TABLE `appuser`
  MODIFY `idUser` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `ticket`
--
ALTER TABLE `ticket`
  MODIFY `idTicket` int(100) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `appuser`
--
ALTER TABLE `appuser`
  ADD CONSTRAINT `appuser_ibfk_1` FOREIGN KEY (`rol`) REFERENCES `rol` (`idRol`);

--
-- Filtros para la tabla `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`email`) REFERENCES `appuser` (`email`),
  ADD CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`priority`) REFERENCES `priority` (`idPr`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
