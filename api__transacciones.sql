-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-10-2024 a las 09:52:12
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `api_ transacciones`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas`
--

CREATE TABLE `cuentas` (
  `id` int(11) NOT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `tipoCuenta` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuentas`
--

INSERT INTO `cuentas` (`id`, `saldo`, `tipoCuenta`) VALUES
(1, '4198.97', 'CuentaEstandar'),
(2, '840.00', 'CuentaEstandar'),
(3, '1518.00', 'CuentaPremium');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `titulares`
--

CREATE TABLE `titulares` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `titulares`
--

INSERT INTO `titulares` (`id`, `nombre`, `direccion`) VALUES
(1, 'John Doe', '123 Main St'),
(2, 'Jane Smith', '456 Elm St'),
(3, 'miller Smith', '220 San diego');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transacciones`
--

CREATE TABLE `transacciones` (
  `id` int(11) NOT NULL,
  `cuenta_id` int(11) DEFAULT NULL,
  `tipo` varchar(50) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `saldo_anterior` decimal(10,2) NOT NULL,
  `saldo_posterior` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `transacciones`
--

INSERT INTO `transacciones` (`id`, `cuenta_id`, `tipo`, `monto`, `saldo_anterior`, `saldo_posterior`, `fecha`) VALUES
(59, 3, 'depósito', '2000.00', '1018.00', '3018.00', '2024-10-03 02:15:22'),
(60, 1, 'depósito', '100.00', '5120.00', '5220.00', '2024-10-03 02:16:11'),
(61, 2, 'depósito', '100.00', '1264.00', '1364.00', '2024-10-03 02:16:17'),
(62, 2, 'Retiro', '1000.00', '1364.00', '344.00', '2024-10-03 02:17:33'),
(63, 1, 'Retiro', '1000.00', '5220.00', '4200.00', '2024-10-03 02:17:37'),
(64, 3, 'Retiro', '1000.00', '3018.00', '2018.00', '2024-10-03 02:17:41'),
(65, 3, 'transferencia', '500.00', '2018.00', '1518.00', '2024-10-03 02:18:15'),
(66, 2, 'depósito', '500.00', '344.00', '844.00', '2024-10-03 02:18:15'),
(67, 2, 'transferencia', '500.00', '844.00', '339.00', '2024-10-03 02:19:33'),
(68, 2, 'depósito', '500.00', '339.00', '839.00', '2024-10-03 02:19:33'),
(69, 1, 'transferencia', '1.00', '4200.00', '4198.99', '2024-10-03 02:23:29'),
(70, 2, 'depósito', '1.00', '839.00', '840.00', '2024-10-03 02:23:29'),
(71, 1, 'Retiro', '1.00', '4198.99', '4197.97', '2024-10-03 02:24:24'),
(72, 1, 'depósito', '1.00', '4197.97', '4198.97', '2024-10-03 02:24:52');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `titulares`
--
ALTER TABLE `titulares`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cuenta_id` (`cuenta_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
