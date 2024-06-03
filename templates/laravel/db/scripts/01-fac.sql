
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";



CREATE DATABASE IF NOT EXISTS `fac` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE `fac`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `categoria_id` int(7) NOT NULL,
  `categoria_nombre` varchar(50) NOT NULL,
  `categoria_ubicacion` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`categoria_id`, `categoria_nombre`, `categoria_ubicacion`) VALUES
(1, 'Escaner', 'Edificio1'),
(2, 'Impresoras', 'Edificio R'),
(3, 'Laptop', 'Edificio H'),
(4, 'Desktop', ''),
(5, 'aire acondicionado AC', 'Oficina 1'),
(6, 'Monitor', ''),
(7, 'Mouse', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `producto_id` int(20) NOT NULL,
  `producto_codigo` varchar(70) NOT NULL,
  `producto_nombre` varchar(70) NOT NULL,
  `producto_marca` varchar(70) NOT NULL,
  `producto_modelo` varchar(70) NOT NULL,
  `producto_foto` varchar(500) NOT NULL,
  `categoria_id` int(7) NOT NULL,
  `usuario_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`producto_id`, `producto_codigo`, `producto_nombre`, `producto_marca`, `producto_modelo`, `producto_foto`, `categoria_id`, `usuario_id`) VALUES
(1, '1412414', 'pruebadidier', 'Furukawa', 'hhhh', '', 1, 1),
(2, '2142141', 'Productoprueba', 'Aruba', 'HB123', '', 1, 1),
(4, '767676', 'pc', 'Lenovo', 'M90T', '', 2, 2),
(5, '1231232', 'Laptop de Miguel', 'Lenovo', 'Gamer x2', '', 3, 1),
(6, '2323243', 'llkk', 'lklk', 'k90', 'llkk_19.png', 6, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `servicio_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `tipo_servicio` enum('Preventivo','Correctivo') NOT NULL,
  `fecha_servicio` date NOT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `servicio_foto` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `usuario_asignador_id` int(11) NOT NULL,
  `usuario_realizador_id` int(11) DEFAULT NULL,
  `estado` enum('Pendiente','Terminado') NOT NULL DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`servicio_id`, `producto_id`, `tipo_servicio`, `fecha_servicio`, `observaciones`, `servicio_foto`, `usuario_asignador_id`, `usuario_realizador_id`, `estado`) VALUES
(1, 2, 'Preventivo', '2024-05-14', 'Revisión general y actualización de software', '', 1, 2, 'Pendiente'),
(2, 2, 'Preventivo', '2024-05-16', 'Limpieza interna y verificación de conexiones', '', 1, 1, 'Pendiente'),
(3, 4, 'Correctivo', '2024-05-30', 'Reemplazo de la fuente de alimentación', '', 1, 1, 'Pendiente'),
(4, 4, 'Correctivo', '2024-05-01', 'Reparación de la placa base dañada', '', 1, 3, 'Pendiente'),
(5, 5, 'Preventivo', '2024-05-29', 'Chequeo de rendimiento y optimización del sistema', '', 1, 3, 'Pendiente'),
(6, 5, 'Correctivo', '2024-05-28', 'Reemplazo del disco duro defectuoso', '', 1, 2, 'Pendiente'),
(7, 5, 'Correctivo', '2024-05-16', 'Reparación del ventilador del sistema de enfriamiento', '', 1, 3, 'Pendiente'),
(8, 1, 'Correctivo', '2024-05-15', 'Limpieza y aspirado de polvo excesivo en componentes internos', '', 1, 3, 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `usuario_id` int(10) NOT NULL,
  `usuario_nombre` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `usuario_apellido` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `usuario_usuario` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `usuario_clave` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `usuario_email` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`usuario_id`, `usuario_nombre`, `usuario_apellido`, `usuario_usuario`, `usuario_clave`, `usuario_email`) VALUES
(1, 'Admin', 'Principal', 'Admin', '$2y$10$k2Bdl26qo4q.iWJPFjwWAePauN8XtxCNf.zIWYdMox0i4ZLPVZtfK', ''),
(2, 'don', 'benitez', 'mfdams', '$2y$10$ZBnGgJoCLc8CxX1HJQJA4uIx1v.ASf8Yp7al3JTlQSXCafldw7cr.', 'mfdams@example.com'),
(3, 'gonzalo', 'chanclas', 'pegas', '$2y$10$S77uGUkzQQK2TjqzFixVwunu0CbqSAosaPlwOHSLlrVF38k8UniYy', 'gchale@examplel.com'),
(4, 'didier', 'didier2', 'didier3', '$2y$10$EE8JehLGDsAOSwP.U9adqOjGYgr/j9Oru4IxpwWDcFRBKCF/wDybm', 'didier@example.com'),
(5, 'eduardo', 'eduardo2', 'eduardo3', '$2y$10$ixeqRppcDqPOrxsYvdrrReJzZWJFcF2TvvvQAdVZuvUeRw8Yy65zi', 'bernes@example.com'),
(6, 'gatovsky', 'gatovsky2', 'gatovsky3', '$2y$10$k2Bdl26qo4q.iWJPFjwWAePauN8XtxCNf.zIWYdMox0i4ZLPVZtfK', 'gatovsky@example');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`categoria_id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`producto_id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`servicio_id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `usuario_asignador_id` (`usuario_asignador_id`),
  ADD KEY `usuario_realizador_id` (`usuario_realizador_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `categoria_id` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `producto_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `servicio_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `usuario_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`categoria_id`),
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD CONSTRAINT `servicio_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`),
  ADD CONSTRAINT `servicio_ibfk_2` FOREIGN KEY (`usuario_asignador_id`) REFERENCES `usuario` (`usuario_id`),
  ADD CONSTRAINT `servicio_ibfk_3` FOREIGN KEY (`usuario_realizador_id`) REFERENCES `usuario` (`usuario_id`);
COMMIT;



