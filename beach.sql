-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-10-2024 a las 17:11:22
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
-- Base de datos: `beach`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `accion` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) DEFAULT NULL,
  `usuario_nombre` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id`, `usuario`, `accion`, `fecha`, `usuario_id`, `usuario_nombre`) VALUES
(1, '', 'Usuario editado: Fernando (Feer@example.com), Rol: TI, ID: 4', '2024-10-07 14:02:55', NULL, NULL),
(2, '', 'Usuario editado: Fernando (Feer@example.com), Rol: TI, ID: 4', '2024-10-07 14:03:02', NULL, NULL),
(3, '', 'Usuario editado: Fernando (Feer@example.com), Rol: TI, ID: 4', '2024-10-07 14:04:21', NULL, NULL),
(4, '', 'Usuario editado: Fernando (Feer@example.com), Rol: TI, ID: 4', '2024-10-07 14:04:24', NULL, NULL),
(5, '', 'Venta registrada: Monto: 2975210, Comentario: Retiro cajas, Fecha: 2024-10-07 16:28:21, Sucursal ID: 1', '2024-10-07 19:28:21', 4, NULL),
(6, '', 'Gasto agregado: Tipo: Internet, Monto: 768758, Fecha: 2024-10-07, Sucursal ID: 1', '2024-10-07 19:28:33', 4, NULL),
(7, '', 'Venta registrada: Monto: 123333, Comentario: Retiro cajas, Fecha: 2024-10-07 16:30:44, Sucursal ID: 1', '2024-10-07 19:30:44', 4, NULL),
(8, '', 'Venta registrada: Monto: 999999, Comentario: Retiro cajas, Fecha: 2024-10-07 11:33:32, Sucursal ID: 1', '2024-10-07 14:33:32', 4, NULL),
(9, '', 'Venta registrada: Monto: 543210, Comentario: Retiro cajas, Fecha: 2024-10-07 11:48:43, Sucursal ID: 1', '2024-10-07 14:48:43', 4, 'Fernando'),
(10, '', 'Venta registrada: Monto: 200000, Comentario: Retiro cajas, Fecha: 2024-10-07 11:50:34, Sucursal ID: 1', '2024-10-07 14:50:34', 4, 'Fernando'),
(11, '', 'Gasto agregado: Tipo: Gas, Monto: 99999, Fecha: 2024-10-07, Sucursal ID: 1', '2024-10-07 14:54:28', 4, 'Fernando'),
(12, '', 'Sucursal agregada: 18 Septiembre', '2024-10-07 14:58:32', 4, ''),
(13, '', 'Sucursal agregada: 18 Septiembre', '2024-10-07 14:58:38', 4, ''),
(14, '', 'Usuario agregado: lnova (lnova@beachmarket.cl), Rol: jefe, Sucursal ID: 1', '2024-10-07 15:02:07', 4, ''),
(15, '', 'Usuario 6 actualizado', '2024-10-07 15:14:09', NULL, NULL),
(16, '', 'Usuario 6 actualizado', '2024-10-07 15:18:21', NULL, NULL),
(17, '', 'Usuario 6 actualizado', '2024-10-07 18:50:28', NULL, NULL),
(18, '', 'Usuario agregado: encargado (encargado@example.cl), Rol: encargado, Sucursal ID: 8', '2024-10-08 13:23:11', 4, ''),
(19, '', 'Usuario 6 actualizado', '2024-10-08 13:38:47', NULL, NULL),
(20, '', 'Usuario 6 actualizado', '2024-10-08 15:41:17', NULL, NULL),
(21, '', 'Usuario agregado: demo (demo@demo.cl), Rol: jefe, Sucursal ID: 1', '2024-10-08 16:02:32', 4, ''),
(22, '', 'Usuario agregado: Eudo (eudo@eudo.cl), Rol: jefe, Sucursal ID: 9', '2024-10-09 12:44:09', 6, ''),
(23, '', 'Usuario agregado: sdfsdf (lnova@beachmarket.cl), Rol: jefe, Sucursal ID: 1', '2024-10-09 12:53:46', 6, ''),
(24, '', 'Usuario 10 eliminado', '2024-10-09 13:04:01', NULL, NULL),
(25, '', 'Usuario agregado: test (q@q.com), Rol: encargado, Sucursal ID: 9', '2024-10-09 13:55:28', 4, ''),
(26, '', 'Usuario agregado: test (q@q.com), Rol: encargado, Sucursal ID: 9', '2024-10-09 13:55:31', 4, ''),
(27, '', 'Usuario agregado: test (q@q.com), Rol: encargado, Sucursal ID: 9', '2024-10-09 13:55:33', 4, ''),
(28, '', 'Usuario agregado: test (q@q.com), Rol: encargado, Sucursal ID: 9', '2024-10-09 13:55:34', 4, ''),
(29, '', 'Usuario agregado: test (q@q.com), Rol: encargado, Sucursal ID: 9', '2024-10-09 13:55:34', 4, ''),
(30, '', 'Usuario agregado: test (q@q.com), Rol: encargado, Sucursal ID: 9', '2024-10-09 13:55:34', 4, ''),
(31, '', 'Usuario agregado: test (q@q.com), Rol: encargado, Sucursal ID: 9', '2024-10-09 13:55:34', 4, ''),
(32, '', 'Usuario agregado: test (q@q.com), Rol: encargado, Sucursal ID: 9', '2024-10-09 13:55:34', 4, ''),
(33, '', 'Usuario agregado: test (q@q.com), Rol: encargado, Sucursal ID: 9', '2024-10-09 13:55:35', 4, ''),
(34, '', 'Usuario agregado: test (q@q.com), Rol: encargado, Sucursal ID: 9', '2024-10-09 13:55:35', 4, ''),
(35, '', 'Usuario agregado: test (q@q.com), Rol: encargado, Sucursal ID: 9', '2024-10-09 13:55:35', 4, ''),
(36, '', 'Usuario agregado: test (q@q.com), Rol: encargado, Sucursal ID: 9', '2024-10-09 13:55:35', 4, ''),
(37, '', 'Usuario agregado: test (q@q.com), Rol: encargado, Sucursal ID: 9', '2024-10-09 13:55:35', 4, ''),
(38, '', 'Usuario 22 eliminado', '2024-10-09 14:39:52', NULL, NULL),
(39, '', 'Usuario agregado: 18 Septiembre (Feer@example.com), Rol: jefe, Sucursal ID: 1', '2024-10-09 14:40:04', 4, ''),
(40, '', 'Usuario 24 eliminado', '2024-10-09 14:40:11', NULL, NULL),
(41, '', 'Usuario 8 eliminado', '2024-10-09 14:43:00', NULL, NULL),
(42, '', 'Usuario agregado: ghfdgdfgfd (Feer@example.com), Rol: jefe, Sucursal ID: 1', '2024-10-09 14:46:27', 4, ''),
(43, '', 'Usuario agregado: htddfsgdf (Feer@example.com), Rol: jefe, Sucursal ID: 1', '2024-10-09 14:53:42', 4, ''),
(44, '', 'Usuario agregado: fsdsdffs (Feer@example.com), Rol: jefe, Sucursal ID: 1', '2024-10-09 14:57:03', 4, ''),
(45, '', 'Datos reasignados del usuario 27 al usuario 4', '2024-10-09 15:17:51', NULL, NULL),
(46, '', 'Usuario 27 eliminado', '2024-10-09 15:17:52', NULL, NULL),
(47, '', 'Venta registrada: Monto: 200000, Comentario: Retiro cajas, Fecha: 2024-10-09 12:19:37, Sucursal ID: 8', '2024-10-09 15:19:37', 7, 'encargado'),
(48, '', 'Venta registrada: Monto: 128510, Comentario: Retiro cajas, Fecha: 2024-10-09 12:19:53, Sucursal ID: 8', '2024-10-09 15:19:53', 7, 'encargado'),
(49, '', 'Datos reasignados del usuario 7 al usuario 4', '2024-10-09 15:20:59', NULL, NULL),
(50, '', 'Usuario 7 eliminado', '2024-10-09 15:20:59', NULL, NULL),
(51, '', 'Usuario agregado: Camelias (camelias@gmail.com), Rol: jefe, Sucursal ID: 9', '2024-10-09 15:21:41', 4, ''),
(52, '', 'Venta registrada: Monto: 128510, Comentario: Retiro cajas, Fecha: 2024-10-09 12:22:06, Sucursal ID: 9', '2024-10-09 15:22:06', 28, 'Camelias'),
(53, '', 'Venta registrada: Monto: 768758, Comentario: Cierra día, Fecha: 2024-10-09 12:22:10, Sucursal ID: 9', '2024-10-09 15:22:10', 28, 'Camelias'),
(54, '', 'Gasto agregado: Tipo: Internet, Monto: 99999, Fecha: 2024-10-09, Sucursal ID: 9', '2024-10-09 15:22:22', 28, 'Camelias'),
(55, '', 'Gasto agregado: Tipo: Agua, Monto: 768758, Fecha: 2024-10-09, Sucursal ID: 9', '2024-10-09 15:22:27', 28, 'Camelias'),
(56, '', 'Gasto agregado: Tipo: Electricidad, Monto: 128510, Fecha: 2024-10-09, Sucursal ID: 9', '2024-10-09 15:22:32', 28, 'Camelias'),
(57, '', 'Datos reasignados del usuario 28 al usuario 4', '2024-10-09 15:24:24', NULL, NULL),
(58, '', 'Usuario 28 eliminado', '2024-10-09 15:24:24', NULL, NULL),
(59, '', 'Usuario agregado: demo (demo@demo.cl), Rol: jefe, Sucursal ID: 8', '2024-10-09 15:33:50', 4, ''),
(60, '', 'Usuario agregado: demo (demo@demo.cl), Rol: jefe, Sucursal ID: 8', '2024-10-09 15:34:05', 4, ''),
(61, '', 'Datos reasignados del usuario 30 al usuario 4', '2024-10-09 15:34:13', NULL, NULL),
(62, '', 'Usuario 30 eliminado', '2024-10-09 15:34:13', NULL, NULL),
(63, '', 'Usuario agregado: demo (demo1@gmail.com), Rol: jefe, Sucursal ID: 9', '2024-10-09 15:35:27', 4, ''),
(64, '', 'Usuario agregado: vbfdfgdf (Feer@example.com), Rol: jefe, Sucursal ID: 1', '2024-10-09 15:46:18', 4, ''),
(65, '', 'Usuario 32 eliminado', '2024-10-09 15:46:28', NULL, NULL),
(66, '', 'Usuario 31 eliminado', '2024-10-09 15:46:31', NULL, NULL),
(67, '', 'Usuario agregado: gdfgfdgdf (Feer@example.com), Rol: jefe, Sucursal ID: 1', '2024-10-09 16:13:25', 4, ''),
(68, '', 'Usuario agregado: gdfgfdgdf (Feer@example.com), Rol: jefe, Sucursal ID: 1', '2024-10-09 16:13:33', 4, ''),
(69, '', 'Usuario 33 eliminado', '2024-10-09 16:13:42', NULL, NULL),
(70, '', 'Usuario 34 eliminado', '2024-10-09 16:13:48', NULL, NULL),
(71, '', 'Usuario agregado: gfdgfdfd (Feer@example.co), Rol: jefe, Sucursal ID: 1', '2024-10-09 16:16:02', 4, ''),
(72, '', 'Usuario 35 eliminado', '2024-10-09 16:16:13', NULL, NULL),
(73, '', 'Usuario 29 eliminado', '2024-10-09 16:22:52', NULL, NULL),
(74, '', 'Usuario agregado: fdsfsdf (Feer@example.comsdf), Rol: jefe, Sucursal ID: 1', '2024-10-09 16:25:48', 4, ''),
(75, '', 'Usuario 36 eliminado', '2024-10-09 16:25:54', NULL, NULL),
(76, '', 'Usuario agregado: gfdgfdf (Feer@example.comfsds), Rol: jefe, Sucursal ID: 1', '2024-10-09 16:27:09', 4, ''),
(77, '', 'Usuario 37 eliminado', '2024-10-09 16:27:16', NULL, NULL),
(78, '', 'Usuario agregado: fsdfsdf (Feer@example.comfsd), Rol: jefe, Sucursal ID: 1', '2024-10-09 16:28:16', 4, 'default_user'),
(79, 'default_user', 'Usuario 38 eliminado', '2024-10-09 16:28:22', NULL, NULL),
(80, '', 'Usuario agregado: sdfsdf (Feer@example.comsdfsd), Rol: jefe, Sucursal ID: 1', '2024-10-09 16:30:36', 4, 'default_user'),
(81, 'default_user', 'Usuario 39 eliminado', '2024-10-09 16:30:41', NULL, NULL),
(82, 'default_user', 'Usuario 39 eliminado', '2024-10-09 16:30:46', NULL, NULL),
(83, '', 'Usuario agregado: sdfsdfgdf (Feer@example.comsdfsdfdg), Rol: jefe, Sucursal ID: 1', '2024-10-09 16:32:30', 4, 'default_user'),
(84, 'default_user', 'Usuario 40 eliminado', '2024-10-09 16:32:36', NULL, NULL),
(85, '', 'Usuario agregado: fggfdfggd (Feer@example.comgfdgffd), Rol: jefe, Sucursal ID: 1', '2024-10-09 16:34:47', 4, 'default_user'),
(86, 'default_user', 'Usuario 41 eliminado', '2024-10-09 16:34:51', NULL, NULL),
(87, '', 'Usuario agregado: rter (Feer@example.comtreet), Rol: jefe, Sucursal ID: 1', '2024-10-09 16:40:19', 4, 'default_user'),
(88, 'default_user', 'Usuario 42 eliminado', '2024-10-09 16:40:27', NULL, NULL),
(89, '', 'Gasto agregado: Tipo: Electricidad, Monto: rrr, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 16:45:01', 4, 'Fernando'),
(90, '', 'Gasto agregado: Tipo: Internet, Monto: ert, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 16:45:09', 4, 'Fernando'),
(91, '', 'Gasto agregado: Tipo: Internet, Monto: etrtert, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 16:45:11', 4, 'Fernando'),
(92, '', 'Gasto agregado: Tipo: Internet, Monto: 324234234, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 16:45:26', 4, 'Fernando'),
(93, '', 'Gasto agregado: Tipo: Internet, Monto: 2131321, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 16:47:19', 4, 'Fernando'),
(94, '', 'Gasto agregado: Tipo: Internet, Monto: 43222, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 16:49:27', 4, 'Fernando'),
(95, '', 'Gasto agregado: Tipo: Internet, Monto: 123213, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 16:49:35', 4, 'Fernando'),
(96, '', 'Gasto agregado: Tipo: Internet, Monto: 12123, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 16:49:37', 4, 'Fernando'),
(97, '', 'Gasto agregado: Tipo: Internet, Monto: 12123, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 16:49:44', 4, 'Fernando'),
(98, '', 'Gasto agregado: Tipo: Internet, Monto: 13213, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 16:49:49', 4, 'Fernando'),
(99, '', 'Gasto agregado: Tipo: Internet, Monto: 122222222222, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 16:50:00', 4, 'Fernando'),
(100, '', 'Gasto agregado: Tipo: Internet, Monto: 128510, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 16:59:25', 4, 'Fernando'),
(101, '', 'Gasto agregado: Tipo: Electricidad, Monto: afsfdsf, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 16:59:38', 4, 'Fernando'),
(102, '', 'Gasto agregado: Tipo: Electricidad, Monto: fddd, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 17:01:06', 4, 'Fernando'),
(103, '', 'Gasto agregado: Tipo: Internet, Monto: 1213, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 17:01:10', 4, 'Fernando'),
(104, '', 'Gasto agregado: Tipo: Internet, Monto: 29882009, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 17:04:25', 4, 'Fernando'),
(105, '', 'Gasto agregado: Tipo: Internet, Monto: 95000000000, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 17:04:38', 4, 'Fernando'),
(106, '', 'Gasto agregado: Tipo: Internet, Monto: 999999999, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 17:04:57', 4, 'Fernando'),
(107, '', 'Gasto agregado: Tipo: Internet, Monto: 999999999, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 17:10:57', 4, 'Fernando'),
(108, '', 'Gasto agregado: Tipo: Internet, Monto: 88765, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 17:11:17', 4, 'Fernando'),
(109, '', 'Gasto agregado: Tipo: Internet, Monto: 88765, Fecha: 2024-10-09, Sucursal ID: 1', '2024-10-09 17:18:52', 4, 'Fernando'),
(110, 'default_user', 'Usuario 6 actualizado', '2024-10-10 14:31:34', NULL, NULL),
(111, '', 'Usuario agregado: demoq (demo@demo.com), Rol: jefe, Sucursal ID: 8', '2024-10-10 14:32:44', 4, 'default_user'),
(112, 'default_user', 'Usuario 43 actualizado', '2024-10-10 14:32:57', NULL, NULL),
(113, '', 'Usuario agregado: DEMO (demo@demo.cl), Rol: jefe, Sucursal ID: 1', '2024-10-10 16:44:02', 4, 'default_user'),
(114, 'default_user', 'Usuario 44 actualizado', '2024-10-10 16:44:10', NULL, NULL),
(115, 'default_user', 'Usuario 44 actualizado', '2024-10-10 16:44:11', NULL, NULL),
(116, 'default_user', 'Usuario 43 eliminado', '2024-10-10 16:44:13', NULL, NULL),
(117, 'default_user', 'Usuario 44 actualizado', '2024-10-10 16:44:14', NULL, NULL),
(118, '', 'Usuario agregado: Cholocholo (cholo@gmail.com), Rol: encargado, Sucursal ID: 9', '2024-10-10 16:56:41', 44, 'default_user'),
(119, 'default_user', 'Usuario 45 actualizado', '2024-10-10 16:56:52', NULL, NULL),
(120, 'default_user', 'Usuario 45 eliminado', '2024-10-10 16:59:37', NULL, NULL),
(121, '', 'Venta registrada: Monto: 120000, Comentario: Cierra día, Fecha: 2024-10-11 14:29:48, Sucursal ID: 1', '2024-10-11 17:29:48', 4, 'Fernando'),
(122, '', 'Gasto agregado: Tipo: Electricidad, Monto: 768758, Fecha: 2024-10-11, Sucursal ID: 1', '2024-10-11 17:30:52', 4, 'Fernando'),
(123, '', 'Usuario agregado: teresa morgan quijon (amorgan@beachmarket.cl), Rol: encargado, Sucursal ID: 1', '2024-10-11 18:32:49', 44, 'default_user'),
(124, 'default_user', 'Usuario 46 actualizado', '2024-10-11 18:35:13', NULL, NULL),
(125, 'default_user', 'Usuario 46 eliminado', '2024-10-11 18:36:28', NULL, NULL),
(126, '', 'Venta registrada: Monto: 9999999999, Comentario: Cierra día, Fecha: 2024-10-11 16:02:21, Sucursal ID: 1', '2024-10-11 19:02:21', 4, 'Fernando'),
(127, '', 'Venta registrada: Monto: 200000, Comentario: Retiro cajas, Fecha: 2024-10-11 16:07:28, Sucursal ID: 8', '2024-10-11 19:07:28', 4, 'Fernando'),
(128, '', 'Venta registrada: Monto: 200000, Comentario: Retiro cajas, Fecha: 2024-10-11 16:07:36, Sucursal ID: 8', '2024-10-11 19:07:36', 4, 'Fernando'),
(129, '', 'Venta registrada: Monto: , Comentario: , Fecha: 2024-10-11 16:27:59, Sucursal ID: todas', '2024-10-11 19:27:59', 4, 'Fernando'),
(130, '', 'Venta registrada: Monto: , Comentario: , Fecha: 2024-10-12 09:24:16, Sucursal ID: todas', '2024-10-12 12:24:16', 4, 'Fernando'),
(131, '', 'Venta registrada: Monto: , Comentario: , Fecha: 2024-10-12 09:24:41, Sucursal ID: todas', '2024-10-12 12:24:41', 4, 'Fernando'),
(132, '', 'Venta registrada: Monto: , Comentario: , Fecha: 2024-10-12 09:29:04, Sucursal ID: todas', '2024-10-12 12:29:04', 4, 'Fernando'),
(133, '', 'Venta registrada: Monto: , Comentario: , Fecha: 2024-10-12 09:29:18, Sucursal ID: todas', '2024-10-12 12:29:18', 4, 'Fernando'),
(134, '', 'Venta registrada: Monto: , Comentario: , Fecha: 2024-10-12 09:29:25, Sucursal ID: todas', '2024-10-12 12:29:25', 4, 'Fernando'),
(135, '', 'Venta registrada: Monto: , Comentario: , Fecha: 2024-10-12 09:35:22, Sucursal ID: ', '2024-10-12 12:35:22', 4, 'Fernando'),
(136, '', 'Venta registrada: Monto: , Comentario: , Fecha: 2024-10-12 09:42:03, Sucursal ID: ', '2024-10-12 12:42:03', 4, 'Fernando'),
(137, '', 'Venta registrada: Monto: , Comentario: , Fecha: 2024-10-12 09:42:13, Sucursal ID: ', '2024-10-12 12:42:13', 4, 'Fernando'),
(138, '', 'Venta registrada: Monto: 0, Comentario: , Fecha: 2024-10-12 09:47:59, Sucursal ID: 1', '2024-10-12 12:47:59', 4, 'Fernando'),
(139, '', 'Venta registrada: Monto: 0, Comentario: , Fecha: 2024-10-12 09:53:58, Sucursal ID: 1', '2024-10-12 12:53:58', 4, 'Fernando'),
(140, '', 'Venta registrada: Monto: 0, Comentario: , Fecha: 2024-10-12 09:54:05, Sucursal ID: 1', '2024-10-12 12:54:05', 4, 'Fernando'),
(141, '', 'Venta registrada: Monto: 0, Comentario: , Fecha: 2024-10-12 09:57:25, Sucursal ID: 1', '2024-10-12 12:57:25', 4, 'Fernando'),
(142, '', 'Venta registrada: Monto: 0, Comentario: , Fecha: 2024-10-12 09:57:32, Sucursal ID: 1', '2024-10-12 12:57:32', 4, 'Fernando'),
(143, '', 'Venta registrada: Monto: 128510, Comentario: Retiro cajas, Fecha: 2024-10-12 09:57:40, Sucursal ID: 1', '2024-10-12 12:57:40', 4, 'Fernando'),
(144, '', 'Venta registrada: Monto: 0, Comentario: , Fecha: 2024-10-12 09:58:01, Sucursal ID: 1', '2024-10-12 12:58:01', 4, 'Fernando'),
(145, '', 'Venta registrada: Monto: 0, Comentario: , Fecha: 2024-10-12 09:58:07, Sucursal ID: 1', '2024-10-12 12:58:07', 4, 'Fernando'),
(146, '', 'Venta registrada: Monto: 0, Comentario: , Fecha: 2024-10-12 10:00:33, Sucursal ID: 1', '2024-10-12 13:00:33', 4, 'Fernando'),
(147, '', 'Venta registrada: Monto: 0, Comentario: , Fecha: 2024-10-12 10:00:57, Sucursal ID: 1', '2024-10-12 13:00:57', 4, 'Fernando'),
(148, 'default_user', 'Usuario 6 actualizado', '2024-10-12 13:02:29', NULL, NULL),
(149, 'default_user', 'Usuario 6 actualizado', '2024-10-12 13:02:32', NULL, NULL),
(150, '', 'Venta registrada: Monto: 1500, Comentario: Retiro cajas, Fecha: 2024-10-14 09:36:35, Sucursal ID: 1', '2024-10-14 12:36:35', 4, 'Fernando'),
(151, '', 'Venta registrada: Monto: 2500, Comentario: Retiro cajas, Fecha: 2024-10-14 09:36:46, Sucursal ID: 9', '2024-10-14 12:36:46', 4, 'Fernando'),
(152, '', 'Gasto registrado: Tipo: Electricidad, Monto: 123780, Fecha: 2024-10-14, Sucursal ID: todas', '2024-10-14 12:39:01', 4, 'Fernando'),
(153, '', 'Gasto registrado: Tipo: Internet, Monto: 122510, Fecha: 2024-10-14, Sucursal ID: todas', '2024-10-14 12:39:16', 4, 'Fernando'),
(154, '', 'Gasto registrado: Tipo: Agua, Monto: 27600, Fecha: 2024-10-14, Sucursal ID: 9', '2024-10-14 19:25:54', 4, 'Fernando'),
(155, '', 'Usuario agregado: engargado (encargado@encargado.cl), Rol: encargado, Sucursal ID: 9', '2024-10-14 19:27:29', 4, 'default_user'),
(156, '', 'Usuario agregado: jefe (jefe@jefe.cl), Rol: jefe, Sucursal ID: 8', '2024-10-14 19:27:46', 4, 'default_user'),
(157, 'default_user', 'Usuario 44 actualizado', '2024-10-14 19:27:56', NULL, NULL),
(158, 'default_user', 'Usuario 47 actualizado', '2024-10-15 12:36:04', NULL, NULL),
(159, '', 'Usuario agregado: admin (admin@admin.cl), Rol: jefe, Sucursal ID: 1', '2024-10-16 12:55:55', 4, ''),
(160, '', 'Gasto registrado: Tipo: bencina coelemu, Monto: 25000, Fecha: 2024-10-18, Sucursal ID: 1', '2024-10-18 16:14:15', 4, 'Fernando'),
(161, '', 'Gasto registrado: Tipo: Becina, Monto: 51500, Fecha: 2024-10-19, Sucursal ID: 1', '2024-10-19 12:39:43', 4, 'Fernando'),
(162, '', 'Gasto registrado: Tipo: Internet, Monto: 1232423, Fecha: 2024-10-19, Sucursal ID: 1', '2024-10-19 12:59:17', 4, 'Fernando'),
(163, '', 'Gasto registrado: Tipo: cartero, Monto: 510, Fecha: 2024-10-19, Sucursal ID: 1', '2024-10-19 13:06:50', 4, 'Fernando'),
(164, '', 'Gasto registrado: Tipo: cartero, Monto: 510, Fecha: 2024-10-19 10:09:33, Sucursal ID: 1', '2024-10-19 13:09:33', 4, 'Fernando'),
(165, '', 'Gasto registrado: Tipo: Electricidad, Monto: 230000, Fecha: 2024-10-19 10:10:48, Sucursal ID: 1', '2024-10-19 13:10:48', 4, 'Fernando'),
(166, '', 'Gasto registrado: Tipo: , Monto: 1000, Fecha: 2024-10-19 10:15:38, Sucursal ID: 1', '2024-10-19 13:15:38', 4, 'Fernando'),
(167, '', 'Gasto registrado: Tipo: Agua, Monto: 123467, Fecha: 2024-10-19 10:16:21, Sucursal ID: 1', '2024-10-19 13:16:21', 4, 'Fernando'),
(168, '', 'Gasto registrado: Tipo: perrito, Monto: 1500, Fecha: 2024-10-19 10:22:18, Sucursal ID: 1', '2024-10-19 13:22:18', 4, 'Fernando');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `sucursal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id`, `fecha`, `tipo`, `monto`, `usuario_id`, `sucursal_id`) VALUES
(2, '2024-01-02', 'Compra', 200.00, 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `id` int(11) NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `sucursal_id` int(11) DEFAULT NULL,
  `gasto_tipo` enum('fijo','variable') NOT NULL DEFAULT 'fijo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`id`, `fecha`, `tipo`, `monto`, `usuario_id`, `sucursal_id`, `gasto_tipo`) VALUES
(1, '2024-03-24 00:00:00', 'electricidad', 45769214.00, 4, 1, 'fijo'),
(2, '2024-03-02 00:00:00', 'luz', 37676447.00, 4, 1, 'fijo'),
(3, '2024-03-07 00:00:00', 'agua', 35707481.00, 4, 1, 'fijo'),
(4, '2024-03-11 00:00:00', 'internet', 38203578.00, 4, 1, 'fijo'),
(5, '2024-03-15 00:00:00', 'gas', 40510846.00, 4, 1, 'fijo'),
(6, '2024-04-23 00:00:00', 'electricidad', 130513.00, 4, 1, 'fijo'),
(7, '2024-04-08 00:00:00', 'luz', 41471622.00, 4, 1, 'fijo'),
(8, '2024-04-06 00:00:00', 'agua', 46906387.00, 4, 1, 'fijo'),
(9, '2024-04-14 00:00:00', 'internet', 41455808.00, 4, 1, 'fijo'),
(10, '2024-04-21 00:00:00', 'gas', 34023083.00, 4, 1, 'fijo'),
(11, '2024-05-01 00:00:00', 'electricidad', 49063742.00, 4, 1, 'fijo'),
(12, '2024-05-27 00:00:00', 'luz', 22474235.00, 4, 1, 'fijo'),
(13, '2024-05-01 00:00:00', 'agua', 20547666.00, 4, 1, 'fijo'),
(14, '2024-05-16 00:00:00', 'internet', 29412623.00, 4, 1, 'fijo'),
(15, '2024-05-28 00:00:00', 'gas', 8150476.00, 4, 1, 'fijo'),
(16, '2024-06-08 00:00:00', 'electricidad', 49580080.00, 4, 1, 'fijo'),
(17, '2024-06-07 00:00:00', 'luz', 22936205.00, 4, 1, 'fijo'),
(18, '2024-06-13 00:00:00', 'agua', 35375447.00, 4, 1, 'fijo'),
(19, '2024-06-04 00:00:00', 'internet', 47566632.00, 4, 1, 'fijo'),
(20, '2024-06-28 00:00:00', 'gas', 44601451.00, 4, 1, 'fijo'),
(21, '2024-07-18 00:00:00', 'electricidad', 49661201.00, 4, 1, 'fijo'),
(22, '2024-07-17 00:00:00', 'luz', 32243992.00, 4, 1, 'fijo'),
(23, '2024-07-02 00:00:00', 'agua', 46139900.00, 4, 1, 'fijo'),
(24, '2024-07-17 00:00:00', 'internet', 10669029.00, 4, 1, 'fijo'),
(25, '2024-07-03 00:00:00', 'gas', 16204892.00, 4, 1, 'fijo'),
(26, '2024-08-08 00:00:00', 'electricidad', 3082210.00, 4, 1, 'fijo'),
(27, '2024-08-18 00:00:00', 'luz', 5940885.00, 4, 1, 'fijo'),
(28, '2024-08-07 00:00:00', 'agua', 18161900.00, 4, 1, 'fijo'),
(29, '2024-08-26 00:00:00', 'internet', 42764400.00, 4, 1, 'fijo'),
(30, '2024-08-11 00:00:00', 'gas', 15081260.00, 4, 1, 'fijo'),
(31, '2024-09-21 00:00:00', 'electricidad', 35004967.00, 4, 1, 'fijo'),
(32, '2024-09-11 00:00:00', 'luz', 43159076.00, 4, 1, 'fijo'),
(33, '2024-09-07 00:00:00', 'agua', 47667608.00, 4, 1, 'fijo'),
(34, '2024-09-27 00:00:00', 'internet', 41353070.00, 4, 1, 'fijo'),
(35, '2024-09-12 00:00:00', 'gas', 17800120.00, 4, 1, 'fijo'),
(36, '2024-03-09 00:00:00', 'electricidad', 3299650.00, 4, 8, 'fijo'),
(37, '2024-03-21 00:00:00', 'luz', 26058248.00, 4, 8, 'fijo'),
(38, '2024-03-14 00:00:00', 'agua', 20278942.00, 4, 8, 'fijo'),
(39, '2024-03-05 00:00:00', 'internet', 36432639.00, 4, 8, 'fijo'),
(40, '2024-03-05 00:00:00', 'gas', 44573046.00, 4, 8, 'fijo'),
(41, '2024-04-21 00:00:00', 'electricidad', 45860995.00, 4, 8, 'fijo'),
(42, '2024-04-15 00:00:00', 'luz', 49466131.00, 4, 8, 'fijo'),
(43, '2024-04-15 00:00:00', 'agua', 24972869.00, 4, 8, 'fijo'),
(44, '2024-04-16 00:00:00', 'internet', 49986492.00, 4, 8, 'fijo'),
(45, '2024-04-19 00:00:00', 'gas', 18226697.00, 4, 8, 'fijo'),
(46, '2024-05-09 00:00:00', 'electricidad', 29396076.00, 4, 8, 'fijo'),
(47, '2024-05-24 00:00:00', 'luz', 31138281.00, 4, 8, 'fijo'),
(48, '2024-05-02 00:00:00', 'agua', 14493791.00, 4, 8, 'fijo'),
(49, '2024-05-20 00:00:00', 'internet', 46521683.00, 4, 8, 'fijo'),
(50, '2024-05-25 00:00:00', 'gas', 17644320.00, 4, 8, 'fijo'),
(51, '2024-06-10 00:00:00', 'electricidad', 15149125.00, 4, 8, 'fijo'),
(52, '2024-06-05 00:00:00', 'luz', 22007681.00, 4, 8, 'fijo'),
(53, '2024-06-14 00:00:00', 'agua', 18722380.00, 4, 8, 'fijo'),
(54, '2024-06-24 00:00:00', 'internet', 47316966.00, 4, 8, 'fijo'),
(55, '2024-06-18 00:00:00', 'gas', 21752199.00, 4, 8, 'fijo'),
(56, '2024-07-10 00:00:00', 'electricidad', 7968267.00, 4, 8, 'fijo'),
(57, '2024-07-15 00:00:00', 'luz', 3227254.00, 4, 8, 'fijo'),
(58, '2024-07-08 00:00:00', 'agua', 16029887.00, 4, 8, 'fijo'),
(59, '2024-07-13 00:00:00', 'internet', 42551398.00, 4, 8, 'fijo'),
(60, '2024-07-02 00:00:00', 'gas', 22108745.00, 4, 8, 'fijo'),
(61, '2024-08-14 00:00:00', 'electricidad', 23611712.00, 4, 8, 'fijo'),
(62, '2024-08-23 00:00:00', 'luz', 31376154.00, 4, 8, 'fijo'),
(63, '2024-08-01 00:00:00', 'agua', 5152988.00, 4, 8, 'fijo'),
(64, '2024-08-09 00:00:00', 'internet', 8954612.00, 4, 8, 'fijo'),
(65, '2024-08-15 00:00:00', 'gas', 21358496.00, 4, 8, 'fijo'),
(66, '2024-09-19 00:00:00', 'electricidad', 31081942.00, 4, 8, 'fijo'),
(67, '2024-09-15 00:00:00', 'luz', 16628651.00, 4, 8, 'fijo'),
(68, '2024-09-19 00:00:00', 'agua', 17405047.00, 4, 8, 'fijo'),
(69, '2024-09-26 00:00:00', 'internet', 10240104.00, 4, 8, 'fijo'),
(70, '2024-09-19 00:00:00', 'gas', 44989967.00, 4, 8, 'fijo'),
(71, '2024-10-09 00:00:00', 'Internet', 99999.00, NULL, 9, 'fijo'),
(72, '2024-10-09 00:00:00', 'Agua', 768758.00, NULL, 9, 'fijo'),
(73, '2024-10-09 00:00:00', 'Electricidad', 128510.00, NULL, 9, 'fijo'),
(74, '2024-10-09 00:00:00', 'Electricidad', 0.00, NULL, 1, 'fijo'),
(75, '2024-10-09 00:00:00', 'Internet', 0.00, NULL, 1, 'fijo'),
(76, '2024-10-09 00:00:00', 'Internet', 0.00, NULL, 1, 'fijo'),
(77, '2024-10-09 00:00:00', 'Internet', 99999999.99, NULL, 1, 'fijo'),
(78, '2024-10-09 00:00:00', 'Internet', 2131321.00, NULL, 1, 'fijo'),
(79, '2024-10-09 00:00:00', 'Internet', 43222.00, NULL, 1, 'fijo'),
(80, '2024-10-09 00:00:00', 'Internet', 123213.00, NULL, 1, 'fijo'),
(81, '2024-10-09 00:00:00', 'Internet', 12123.00, NULL, 1, 'fijo'),
(82, '2024-10-09 00:00:00', 'Internet', 12123.00, NULL, 1, 'fijo'),
(83, '2024-10-09 00:00:00', 'Internet', 13213.00, NULL, 1, 'fijo'),
(84, '2024-10-09 00:00:00', 'Internet', 99999999.99, NULL, 1, 'fijo'),
(85, '2024-10-09 00:00:00', 'Internet', 128510.00, NULL, 1, 'fijo'),
(86, '2024-10-09 00:00:00', 'Electricidad', 0.00, NULL, 1, 'fijo'),
(87, '2024-10-09 00:00:00', 'Electricidad', 0.00, NULL, 1, 'fijo'),
(88, '2024-10-09 00:00:00', 'Internet', 1213.00, NULL, 1, 'fijo'),
(89, '2024-10-09 00:00:00', 'Internet', 29882009.00, NULL, 1, 'fijo'),
(90, '2024-10-09 00:00:00', 'Internet', 99999999.99, NULL, 1, 'fijo'),
(91, '2024-10-09 00:00:00', 'Internet', 99999999.99, NULL, 1, 'fijo'),
(92, '2024-10-09 00:00:00', 'Internet', 99999999.99, NULL, 1, 'fijo'),
(93, '2024-10-09 00:00:00', 'Internet', 88765.00, NULL, 1, 'fijo'),
(94, '2024-10-09 00:00:00', 'Internet', 88765.00, NULL, 1, 'fijo'),
(95, '2024-10-11 00:00:00', 'Electricidad', 768758.00, NULL, 1, 'fijo'),
(96, '2024-10-14 00:00:00', 'Electricidad', 123780.00, 4, 0, 'fijo'),
(97, '2024-10-14 00:00:00', 'Internet', 122510.00, 4, 0, 'fijo'),
(98, '2024-10-15 00:00:00', 'Agua', 27600.00, 4, 9, 'fijo'),
(99, '2024-10-18 00:00:00', 'bencina coelemu', 25000.00, 4, 1, 'variable'),
(100, '0000-00-00 00:00:00', 'Becina', 51500.00, 4, 1, 'variable'),
(101, '0000-00-00 00:00:00', 'Internet', 1232423.00, 4, 1, 'fijo'),
(102, '0000-00-00 00:00:00', 'cartero', 510.00, 4, 1, 'variable'),
(103, '0000-00-00 00:00:00', 'cartero', 510.00, 4, 1, 'variable'),
(104, '0000-00-00 00:00:00', 'Electricidad', 230000.00, 4, 1, 'fijo'),
(105, '2024-10-19 10:15:38', '', 1000.00, 4, 1, 'fijo'),
(106, '2024-10-19 10:16:21', 'Agua', 123467.00, 4, 1, 'fijo'),
(107, '0000-00-00 00:00:00', 'perrito', 1500.00, 4, 1, 'variable');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventarios`
--

CREATE TABLE `inventarios` (
  `id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `sucursal_id` int(11) DEFAULT NULL,
  `tipo` enum('ingreso','retiro') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventarios`
--

INSERT INTO `inventarios` (`id`, `fecha`, `descripcion`, `cantidad`, `usuario_id`, `sucursal_id`, `tipo`) VALUES
(1, '2024-06-09', 'ajuste de inventario', 30, 4, 1, 'ingreso'),
(2, '2024-05-23', 'ajuste de inventario', 88, 4, 1, 'ingreso'),
(3, '2024-09-18', 'ajuste de inventario', 75, 4, 1, 'ingreso'),
(4, '2024-03-26', 'ajuste de inventario', 67, 4, 1, 'ingreso'),
(5, '2024-07-02', 'ajuste de inventario', 13, 4, 1, 'ingreso'),
(6, '2024-05-15', 'reposición de producto', 91, 4, 1, 'ingreso'),
(7, '2024-07-30', 'reposición de producto', 54, 4, 1, 'ingreso'),
(8, '2024-06-07', 'reposición de producto', 71, 4, 1, 'ingreso'),
(9, '2024-08-27', 'reposición de producto', 97, 4, 1, 'ingreso'),
(10, '2024-08-09', 'reposición de producto', 69, 4, 1, 'ingreso'),
(11, '2024-07-12', 'salida de stock', 86, 4, 1, 'ingreso'),
(12, '2024-08-10', 'salida de stock', 53, 4, 1, 'ingreso'),
(13, '2024-09-12', 'ajuste de inventario', 79, 4, 1, 'ingreso'),
(14, '2024-08-18', 'reposición de producto', 92, 4, 1, 'ingreso'),
(15, '2024-03-09', 'ajuste de inventario', 26, 4, 1, 'ingreso'),
(16, '2024-04-05', 'reposición de producto', 67, 4, 1, 'ingreso'),
(17, '2024-03-30', 'reposición de producto', 43, 4, 1, 'ingreso'),
(18, '2024-05-01', 'entrada de stock', 81, 4, 1, 'ingreso'),
(19, '2024-06-22', 'entrada de stock', 59, 4, 1, 'ingreso'),
(20, '2024-09-11', 'entrada de stock', 51, 4, 1, 'ingreso'),
(21, '2024-05-24', 'salida de stock', 65, 4, 1, 'ingreso'),
(22, '2024-05-30', 'entrada de stock', 29, 4, 1, 'ingreso'),
(23, '2024-09-14', 'ajuste de inventario', 53, 4, 1, 'ingreso'),
(24, '2024-03-16', 'reposición de producto', 47, 4, 1, 'ingreso'),
(25, '2024-04-11', 'entrada de stock', 59, 4, 1, 'ingreso'),
(26, '2024-09-26', 'reposición de producto', 72, 4, 1, 'ingreso'),
(27, '2024-03-12', 'salida de stock', 27, 4, 1, 'ingreso'),
(28, '2024-09-02', 'reposición de producto', 23, 4, 1, 'ingreso'),
(29, '2024-06-14', 'entrada de stock', 31, 4, 1, 'ingreso'),
(30, '2024-05-28', 'ajuste de inventario', 100, 4, 1, 'ingreso'),
(31, '2024-08-20', 'ajuste de inventario', 37, 4, 1, 'ingreso'),
(32, '2024-03-30', 'entrada de stock', 75, 4, 1, 'ingreso'),
(33, '2024-03-16', 'ajuste de inventario', 84, 4, 1, 'ingreso'),
(34, '2024-09-28', 'salida de stock', 99, 4, 1, 'ingreso'),
(35, '2024-05-26', 'reposición de producto', 28, 4, 1, 'ingreso'),
(36, '2024-06-08', 'entrada de stock', 33, 4, 1, 'ingreso'),
(37, '2024-07-11', 'salida de stock', 85, 4, 1, 'ingreso'),
(38, '2024-04-02', 'ajuste de inventario', 79, 4, 1, 'ingreso'),
(39, '2024-04-02', 'entrada de stock', 84, 4, 1, 'ingreso'),
(40, '2024-09-27', 'entrada de stock', 10, 4, 1, 'ingreso'),
(41, '2024-09-10', 'salida de stock', 64, 4, 1, 'ingreso'),
(42, '2024-08-22', 'entrada de stock', 69, 4, 1, 'ingreso'),
(43, '2024-06-22', 'reposición de producto', 43, 4, 1, 'ingreso'),
(44, '2024-08-30', 'salida de stock', 49, 4, 1, 'ingreso'),
(45, '2024-09-09', 'ajuste de inventario', 36, 4, 1, 'ingreso'),
(46, '2024-04-02', 'salida de stock', 50, 4, 1, 'ingreso'),
(47, '2024-04-10', 'reposición de producto', 93, 4, 1, 'ingreso'),
(48, '2024-03-12', 'salida de stock', 39, 4, 1, 'ingreso'),
(49, '2024-06-11', 'salida de stock', 58, 4, 1, 'ingreso'),
(50, '2024-10-10', 'Producto A', 45, 4, 1, 'ingreso'),
(51, '2024-10-10', '', 22, 4, 1, 'retiro'),
(52, '2024-10-10', 'Producto A', 12, 4, 1, 'retiro'),
(53, '2024-10-10', 'Producto A', 12, 4, 1, 'ingreso'),
(54, '2024-10-10', 'Producto A', 15, 4, 1, 'ingreso'),
(55, '2024-10-10', 'Producto A', 12, 4, 1, 'ingreso'),
(56, '2024-10-10', 'Producto A', 12, 4, 1, 'ingreso'),
(57, '2024-10-10', 'Producto A', 12, 4, 1, 'ingreso'),
(58, '2024-10-10', 'Producto A', 123, 4, 1, 'retiro'),
(59, '2024-10-10', 'Producto A', 123, 4, 1, 'retiro'),
(60, '2024-10-10', 'Producto A', 123, 4, 1, 'retiro'),
(61, '2024-10-10', 'Producto A', 123, 4, 1, 'ingreso'),
(62, '2024-10-10', 'Producto A', 123, 4, 1, 'ingreso'),
(63, '2024-10-10', 'Producto A', 121, 4, 1, 'retiro'),
(64, '2024-10-10', 'Producto A', 16, 4, 1, 'retiro'),
(65, '2024-10-10', 'Producto A', 162, 4, 1, 'retiro'),
(66, '2024-10-10', 'Producto A', 124, 4, 1, 'ingreso'),
(67, '2024-10-10', 'Producto A', 120, 4, 1, 'retiro'),
(68, '2024-10-10', 'Producto A', 16, 4, 1, 'ingreso'),
(69, '2024-10-10', 'Producto A', 6, 4, 1, 'ingreso'),
(70, '2024-10-10', 'Producto A', 3, 4, 1, 'retiro'),
(71, '2024-10-10', 'Producto A', 6, 4, 1, 'retiro'),
(72, '2024-10-10', 'Producto A', 1, 4, 1, 'retiro'),
(73, '2024-10-10', 'Kapo Sabor Manzana', 20, 4, 1, 'retiro'),
(74, '2024-10-11', 'Producto A', 2, 44, 1, 'ingreso'),
(75, '2024-10-14', 'Kapo Sabor Manzana', 20, 4, 1, 'ingreso'),
(76, '2024-10-14', 'Kapo Sabor Manzana', 20, 4, 1, 'retiro'),
(77, '2024-10-15', 'Kapo Sabor Manzana', 100, 4, 1, 'ingreso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'TI'),
(2, 'jefe'),
(3, 'encargado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursales`
--

CREATE TABLE `sucursales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `tipo` enum('Ferreteria','Multitienda','Cabañas','Supermercado') NOT NULL DEFAULT 'Ferreteria'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sucursales`
--

INSERT INTO `sucursales` (`id`, `nombre`, `tipo`) VALUES
(1, 'Lord cochrane', 'Ferreteria'),
(8, '18 Septiembre', 'Supermercado'),
(9, 'Camelias', 'Ferreteria'),
(12, 'Chillan', 'Ferreteria'),
(13, 'Coelemu', 'Ferreteria'),
(14, 'Coelemu', 'Supermercado'),
(15, 'Coelemu', 'Multitienda'),
(16, 'Qirihue', 'Ferreteria'),
(17, 'Dichato', 'Ferreteria'),
(18, 'Dichato', 'Cabañas'),
(19, 'Dichato', 'Supermercado'),
(20, 'Vicente Palacios', 'Ferreteria'),
(21, 'Vicente Palacios', 'Multitienda');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contraseña` varchar(255) DEFAULT NULL,
  `rol` enum('jefe','encargado','dueño','TI') DEFAULT NULL,
  `sucursal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `contraseña`, `rol`, `sucursal_id`) VALUES
(4, 'Fernando', 'Feer@example.com', '$2b$12$Yxo8rtoAkS2IV0frkQBHP.W/rogQoPchL4.taod6S9pkBTx8uBMcC', 'TI', 1),
(6, 'lnova', 'lnova@beachmarket.cl', '$2y$10$IGPGaEBQ/EMbRmSR88YV0.HbGSev8Fh2F5OtxMD056XNgUT4NbcrK', 'TI', 1),
(44, 'demo', 'demo@demo.cl', '$2y$10$IDbNg5.EOQJVPB6x3C.np.C7bbkBtMBcDg2u7JcMPFqnJkBlOeRSO', 'TI', 1),
(47, 'encargado', 'encargado@encargado.cl', '$2y$10$OIaCWELH0g78Q4SMesoGe.B7Eej5XWPp0pFy6qyrOeX3SWs6983Gu', 'encargado', 9),
(48, 'jefe', 'jefe@jefe.cl', '$2y$10$aoYz7mjvy1A.Ixid4vRD8eITuOOLoTQEibtXuC/AW96uY9hDAP6ci', 'jefe', 8),
(49, 'admin', 'admin@admin.cl', '$2y$10$YNRvp2B/fCwvGN2kuZmHmOFfdbPb2hlCn3OuLRQECvwakzdb3ai9m', 'dueño', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `sucursal_id` int(11) DEFAULT NULL,
  `comentario` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `fecha`, `monto`, `usuario_id`, `sucursal_id`, `comentario`) VALUES
(1, '2024-05-06 14:45:50', 269660.00, 4, 1, 'retiro de cajas'),
(2, '2024-04-28 00:00:00', 90451.00, 4, 1, 'retiro de cajas'),
(3, '2024-06-14 00:00:00', 140051.00, 4, 1, 'retiro de cajas'),
(4, '2024-04-10 00:00:00', 202166.00, 4, 1, 'retiro de cajas'),
(5, '2024-06-26 00:00:00', 220631.00, 4, 1, 'retiro de cajas'),
(6, '2024-03-09 00:00:00', 104917.00, 4, 1, 'retiro de cajas'),
(7, '2024-04-11 00:00:00', 281838.00, 4, 1, 'retiro de cajas'),
(8, '2024-08-26 00:00:00', 59413.00, 4, 1, 'retiro de cajas'),
(9, '2024-09-16 00:00:00', 78342.00, 4, 1, 'retiro de cajas'),
(10, '2024-03-25 00:00:00', 214155.00, 4, 1, 'retiro de cajas'),
(11, '2024-05-22 00:00:00', 160071.00, 4, 1, 'retiro de cajas'),
(12, '2024-04-09 00:00:00', 267386.00, 4, 1, 'retiro de cajas'),
(13, '2024-05-30 00:00:00', 89419.00, 4, 1, 'retiro de cajas'),
(14, '2024-05-21 00:00:00', 91391.00, 4, 1, 'retiro de cajas'),
(15, '2024-07-09 00:00:00', 144191.00, 4, 1, 'retiro de cajas'),
(16, '2024-06-30 00:00:00', 127869.00, 4, 1, 'retiro de cajas'),
(17, '2024-08-27 00:00:00', 296220.00, 4, 1, 'retiro de cajas'),
(18, '2024-09-27 00:00:00', 128494.00, 4, 1, 'retiro de cajas'),
(19, '2024-07-16 00:00:00', 261132.00, 4, 1, 'retiro de cajas'),
(20, '2024-09-25 00:00:00', 175850.00, 4, 1, 'retiro de cajas'),
(21, '2024-08-01 00:00:00', 70894.00, 4, 1, 'retiro de cajas'),
(22, '2024-06-26 00:00:00', 59084.00, 4, 1, 'retiro de cajas'),
(23, '2024-09-23 00:00:00', 95035.00, 4, 1, 'retiro de cajas'),
(24, '2024-08-26 00:00:00', 173241.00, 4, 1, 'retiro de cajas'),
(25, '2024-03-27 00:00:00', 186302.00, 4, 1, 'retiro de cajas'),
(26, '2024-09-25 00:00:00', 224227.00, 4, 1, 'retiro de cajas'),
(27, '2024-08-25 00:00:00', 113440.00, 4, 1, 'retiro de cajas'),
(28, '2024-03-25 00:00:00', 197695.00, 4, 1, 'retiro de cajas'),
(29, '2024-06-26 00:00:00', 113461.00, 4, 1, 'retiro de cajas'),
(30, '2024-04-01 00:00:00', 139968.00, 4, 1, 'retiro de cajas'),
(31, '2024-05-20 00:00:00', 230761.00, 4, 1, 'retiro de cajas'),
(32, '2024-09-20 00:00:00', 62950.00, 4, 1, 'retiro de cajas'),
(33, '2024-04-09 00:00:00', 115274.00, 4, 1, 'retiro de cajas'),
(34, '2024-09-16 00:00:00', 275230.00, 4, 1, 'retiro de cajas'),
(35, '2024-03-04 00:00:00', 133000.00, 4, 1, 'retiro de cajas'),
(36, '2024-06-07 00:00:00', 72121.00, 4, 1, 'retiro de cajas'),
(37, '2024-04-22 00:00:00', 130296.00, 4, 1, 'retiro de cajas'),
(38, '2024-04-13 00:00:00', 204948.00, 4, 1, 'retiro de cajas'),
(39, '2024-07-05 00:00:00', 138567.00, 4, 1, 'retiro de cajas'),
(40, '2024-04-01 00:00:00', 132051.00, 4, 1, 'retiro de cajas'),
(41, '2024-09-26 00:00:00', 294579.00, 4, 1, 'retiro de cajas'),
(42, '2024-06-09 00:00:00', 165213.00, 4, 1, 'retiro de cajas'),
(43, '2024-07-11 00:00:00', 272030.00, 4, 1, 'retiro de cajas'),
(44, '2024-05-25 00:00:00', 256832.00, 4, 1, 'retiro de cajas'),
(45, '2024-09-14 00:00:00', 55955.00, 4, 1, 'retiro de cajas'),
(46, '2024-09-27 00:00:00', 60206.00, 4, 1, 'retiro de cajas'),
(47, '2024-09-17 00:00:00', 163537.00, 4, 1, 'retiro de cajas'),
(48, '2024-07-25 00:00:00', 94975.00, 4, 1, 'retiro de cajas'),
(49, '2024-05-13 00:00:00', 197310.00, 4, 1, 'retiro de cajas'),
(50, '2024-10-09 12:19:37', 200000.00, 4, 8, 'Retiro cajas'),
(51, '2024-10-09 12:19:53', 128510.00, 4, 8, 'Retiro cajas'),
(52, '2024-10-09 12:22:06', 128510.00, 4, 9, 'Retiro cajas'),
(53, '2024-10-09 12:22:10', 768758.00, 4, 9, 'Cierra día'),
(54, '2024-10-11 14:29:48', 120000.00, 4, 1, 'Cierra día'),
(56, '2024-10-11 16:07:28', 200000.00, 4, 8, 'Retiro cajas'),
(57, '2024-10-11 16:07:36', 200000.00, 4, 8, 'Retiro cajas'),
(58, '2024-10-11 16:27:59', 0.00, 4, 0, NULL),
(59, '2024-10-12 09:24:16', 0.00, 4, 0, NULL),
(60, '2024-10-12 09:24:41', 0.00, 4, 0, NULL),
(61, '2024-10-12 09:29:04', 0.00, 4, 0, NULL),
(62, '2024-10-12 09:29:18', 0.00, 4, 0, NULL),
(63, '2024-10-12 09:29:25', 0.00, 4, 0, NULL),
(64, '2024-10-12 09:35:22', 0.00, 4, 0, NULL),
(65, '2024-10-12 09:42:03', 0.00, 4, 0, NULL),
(66, '2024-10-12 09:42:13', 0.00, 4, 0, NULL),
(67, '2024-10-12 09:47:59', 0.00, 4, 1, ''),
(68, '2024-10-12 09:53:58', 0.00, 4, 1, ''),
(69, '2024-10-12 09:54:05', 0.00, 4, 1, ''),
(70, '2024-10-12 09:57:25', 0.00, 4, 1, ''),
(71, '2024-10-12 09:57:32', 0.00, 4, 1, ''),
(72, '2024-10-12 09:57:40', 128510.00, 4, 1, 'Retiro cajas'),
(73, '2024-10-12 09:58:01', 0.00, 4, 1, ''),
(74, '2024-10-12 09:58:07', 0.00, 4, 1, ''),
(75, '2024-10-12 10:00:33', 0.00, 4, 1, ''),
(76, '2024-10-12 10:00:57', 0.00, 4, 1, ''),
(77, '2024-10-15 09:36:35', 1500.00, 4, 1, 'Retiro cajas'),
(78, '2024-10-15 09:36:46', 2500.00, 4, 9, 'Retiro cajas');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `inventarios`
--
ALTER TABLE `inventarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sucursal` (`sucursal_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT de la tabla `inventarios`
--
ALTER TABLE `inventarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD CONSTRAINT `gastos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `inventarios`
--
ALTER TABLE `inventarios`
  ADD CONSTRAINT `inventarios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
