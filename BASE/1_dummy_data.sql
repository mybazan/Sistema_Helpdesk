-- =============================================================================
-- Datos dummy para sistema_soporte
-- Conserva: user, role, permiso
-- Ejecutar: mysql -u root sistema_soporte < seeds/dummy_data.sql
-- =============================================================================

USE `sistema_soporte`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `planilla_equipo_acceso`;
TRUNCATE TABLE `planilla_equipo_almacenamiento`;
TRUNCATE TABLE `planilla_equipo_sistema_operativo`;
TRUNCATE TABLE `caracteristica`;
TRUNCATE TABLE `pedido_historial_estado`;
TRUNCATE TABLE `pedido_tecnico_asignado`;
TRUNCATE TABLE `pedido_equipo`;
TRUNCATE TABLE `usuario_equipo`;
TRUNCATE TABLE `equipo_historial`;
TRUNCATE TABLE `pedido`;
TRUNCATE TABLE `equipo`;
TRUNCATE TABLE `planilla_equipo`;
TRUNCATE TABLE `personal`;
TRUNCATE TABLE `caracteristica_tipo_equipo`;
TRUNCATE TABLE `ubicacion`;
TRUNCATE TABLE `pedido_estado`;
TRUNCATE TABLE `tipo_equipo`;
TRUNCATE TABLE `tipo_componente`;
TRUNCATE TABLE `reset_password_request`;
TRUNCATE TABLE `imagenes`;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- Usuarios técnicos de demo (password: demo123)
-- Rol: ROLE_TECNICO_SOPORTE (ver seeds/tecnicos_soporte.sql para detalle de permisos)
-- -----------------------------------------------------------------------------
INSERT INTO `role` (`role_name`, `created_at`, `updated_at`)
SELECT 'ROLE_TECNICO_SOPORTE', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `role` WHERE `role_name` = 'ROLE_TECNICO_SOPORTE');

INSERT INTO `permiso` (`role_id`, `nombre`)
SELECT r.id, p.nombre
FROM `role` r
CROSS JOIN (
    SELECT 'VER_INICIO' AS nombre UNION ALL
    SELECT 'TICKET_VER' UNION ALL
    SELECT 'TICKET_EDITAR'
) p
WHERE r.role_name = 'ROLE_TECNICO_SOPORTE'
  AND NOT EXISTS (
    SELECT 1 FROM `permiso` x WHERE x.role_id = r.id AND x.nombre = p.nombre
  );

INSERT INTO `user` (`username`, `roles`, `nombre`, `apellido`, `email`, `dni`, `suspended`, `deleted`, `password`, `created_at`, `updated_at`, `is_tecnico`, `rol_actual_id`)
SELECT 'tecnico1', '["ROLE_TECNICO_SOPORTE"]', 'Carlos', 'Gómez', 'carlos.gomez@demo.local', '30111222', 0, 0,
       '$2y$10$0Ghqg2gImgDN5rlYj6uyP.HWL.LT29hc7hVOE0S4UMYVxCfjmj0JG', NOW(), NOW(), 1, r.id
FROM `role` r
WHERE r.role_name = 'ROLE_TECNICO_SOPORTE'
  AND NOT EXISTS (SELECT 1 FROM `user` u WHERE u.username = 'tecnico1')
LIMIT 1;

INSERT INTO `user` (`username`, `roles`, `nombre`, `apellido`, `email`, `dni`, `suspended`, `deleted`, `password`, `created_at`, `updated_at`, `is_tecnico`, `rol_actual_id`)
SELECT 'tecnico2', '["ROLE_TECNICO_SOPORTE"]', 'Laura', 'Fernández', 'laura.fernandez@demo.local', '30222333', 0, 0,
       '$2y$10$0Ghqg2gImgDN5rlYj6uyP.HWL.LT29hc7hVOE0S4UMYVxCfjmj0JG', NOW(), NOW(), 1, r.id
FROM `role` r
WHERE r.role_name = 'ROLE_TECNICO_SOPORTE'
  AND NOT EXISTS (SELECT 1 FROM `user` u WHERE u.username = 'tecnico2')
LIMIT 1;

SET @admin_id    := (SELECT id FROM `user` WHERE is_tecnico = 0 ORDER BY id LIMIT 1);
SET @tecnico1_id := (SELECT id FROM `user` WHERE username = 'tecnico1' LIMIT 1);
SET @tecnico2_id := (SELECT id FROM `user` WHERE username = 'tecnico2' LIMIT 1);

-- -----------------------------------------------------------------------------
-- Ubicaciones (catálogo pequeño)
-- -----------------------------------------------------------------------------
INSERT INTO `ubicacion` (`id`, `nombre`, `nomenclatura`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Planta baja - recepción', 'PB-REC', 1, NOW(), NOW()),
(2, 'Primer piso - administración', 'P1-ADM', 1, NOW(), NOW()),
(3, 'Primer piso - sistemas', 'P1-SIS', 1, NOW(), NOW()),
(4, 'Segundo piso - secretaría', 'P2-SEC', 1, NOW(), NOW()),
(5, 'Segundo piso - archivo', 'P2-ARC', 1, NOW(), NOW()),
(6, 'Tercer piso - salas', 'P3-SAL', 1, NOW(), NOW()),
(7, 'Depósito técnico', 'DEP-TEC', 1, NOW(), NOW()),
(8, 'Sala de servidores', 'SRV-01', 1, NOW(), NOW());

UPDATE `user` SET ubicacion_id = 3 WHERE id IN (@admin_id, @tecnico1_id, @tecnico2_id);

-- -----------------------------------------------------------------------------
-- Tipos de componente (catálogo medio)
-- -----------------------------------------------------------------------------
INSERT INTO `tipo_componente` (`id`, `identificacion`, `is_active`) VALUES
(1, 'Procesador', 1),
(2, 'Memoria ram', 1),
(3, 'Disco ssd', 1),
(4, 'Disco hdd', 1),
(5, 'Placa de video', 1),
(6, 'Fuente', 1),
(7, 'Monitor', 1),
(8, 'Teclado', 1),
(9, 'Mouse', 1),
(10, 'Sistema operativo', 1);

-- -----------------------------------------------------------------------------
-- Tipos de equipo (catálogo pequeño)
-- -----------------------------------------------------------------------------
INSERT INTO `tipo_equipo` (`id`, `nombre`, `is_active`, `nomenclatura`) VALUES
(1, 'Pc de escritorio', 1, 'PC'),
(2, 'Notebook', 1, 'NB'),
(3, 'Impresora', 1, 'IM'),
(4, 'Servidor', 1, 'SV'),
(5, 'Cámara ip', 1, 'CM'),
(6, 'Dvr', 1, 'DV');

-- -----------------------------------------------------------------------------
-- Características por tipo de equipo
-- -----------------------------------------------------------------------------
INSERT INTO `caracteristica_tipo_equipo` (`id`, `tipo_id`, `nombre`, `is_active`, `tipoComponente_id`) VALUES
(1, 1, 'Procesador', 1, 1),
(2, 1, 'Memoria ram', 1, 2),
(3, 1, 'Disco ssd', 1, 3),
(4, 2, 'Procesador', 1, 1),
(5, 2, 'Memoria ram', 1, 2),
(6, 2, 'Disco ssd', 1, 3),
(7, 3, 'Toner', 1, NULL),
(8, 3, 'Velocidad impresión', 1, NULL),
(9, 4, 'Procesador', 1, 1),
(10, 4, 'Memoria ram', 1, 2),
(11, 4, 'Disco hdd', 1, 4),
(12, 5, 'Resolución', 1, NULL),
(13, 5, 'Megapixeles', 1, NULL),
(14, 6, 'Canales', 1, NULL),
(15, 6, 'Almacenamiento', 1, 3);

-- -----------------------------------------------------------------------------
-- Personal (empleados que usan equipos)
-- -----------------------------------------------------------------------------
INSERT INTO `personal` (`id`, `nombre`, `apellido`, `email`, `dni`, `cuil`, `telefono`, `suspended`, `deleted`, `ubicacion_id`, `created_at`, `updated_at`) VALUES
(1, 'María', 'López', 'maria.lopez@demo.local', '27111222', '27271112223', '3804123456', 0, 0, 2, NOW(), NOW()),
(2, 'Juan', 'Pérez', 'juan.perez@demo.local', '28122333', '20281223334', '3804234567', 0, 0, 2, NOW(), NOW()),
(3, 'Ana', 'García', 'ana.garcia@demo.local', '29133444', '27291334445', '3804345678', 0, 0, 4, NOW(), NOW()),
(4, 'Roberto', 'Martínez', 'roberto.martinez@demo.local', '30144555', '20301445556', '3804456789', 0, 0, 4, NOW(), NOW()),
(5, 'Claudia', 'Rodríguez', 'claudia.rodriguez@demo.local', '31155666', '27311556667', '3804567890', 0, 0, 6, NOW(), NOW()),
(6, 'Diego', 'Sánchez', 'diego.sanchez@demo.local', '32166777', '20321667778', '3804678901', 0, 0, 1, NOW(), NOW()),
(7, 'Patricia', 'Torres', 'patricia.torres@demo.local', '33177888', '27331778889', '3804789012', 0, 0, 3, NOW(), NOW()),
(8, 'Fernando', 'Ruiz', 'fernando.ruiz@demo.local', '34188999', '20341889990', '3804890123', 0, 0, 3, NOW(), NOW()),
(9, 'Lucía', 'Díaz', 'lucia.diaz@demo.local', '35199000', '27351990001', '3804901234', 0, 0, 5, NOW(), NOW()),
(10, 'Miguel', 'Álvarez', 'miguel.alvarez@demo.local', '36200111', '20362001112', '3804012345', 0, 0, 5, NOW(), NOW()),
(11, 'Sofía', 'Romero', 'sofia.romero@demo.local', '37211222', '27372112223', '3804123450', 0, 0, 6, NOW(), NOW()),
(12, 'Pablo', 'Benítez', 'pablo.benitez@demo.local', '38222333', '20382223334', '3804234560', 0, 0, 2, NOW(), NOW());

-- -----------------------------------------------------------------------------
-- Planillas de equipo (solo pcs, notebooks y servidores)
-- -----------------------------------------------------------------------------
INSERT INTO `planilla_equipo` (`id`, `marca`, `modelo`, `nro_serie`, `nro_inventario`, `procesador`, `memoria_ram`, `monitor`, `observacion`, `created_at`, `updated_at`) VALUES
(1, 'Dell', 'Optiplex 7090', 'DL7090-001', 'INV-PC-001', 'Intel i5-11500', '16 GB', 'Dell 24"', 'Equipo administración', NOW(), NOW()),
(2, 'Dell', 'Optiplex 7090', 'DL7090-002', 'INV-PC-002', 'Intel i5-11500', '16 GB', 'Dell 24"', NULL, NOW(), NOW()),
(3, 'HP', 'ProBook 450', 'HP450-001', 'INV-NB-001', 'Intel i7-1255U', '16 GB', '15.6"', 'Notebook secretaría', NOW(), NOW()),
(4, 'HP', 'ProBook 450', 'HP450-002', 'INV-NB-002', 'Intel i7-1255U', '16 GB', '15.6"', NULL, NOW(), NOW()),
(5, 'Lenovo', 'ThinkCentre M70q', 'LN-M70-001', 'INV-PC-003', 'Intel i5-12400', '8 GB', 'Lenovo 22"', NULL, NOW(), NOW()),
(6, 'Lenovo', 'ThinkCentre M70q', 'LN-M70-002', 'INV-PC-004', 'Intel i5-12400', '8 GB', 'Lenovo 22"', NULL, NOW(), NOW()),
(7, 'Dell', 'PowerEdge T340', 'PE-T340-001', 'INV-SV-001', 'Intel Xeon E-2224', '32 GB', NULL, 'Servidor archivos', NOW(), NOW()),
(8, 'Dell', 'PowerEdge T340', 'PE-T340-002', 'INV-SV-002', 'Intel Xeon E-2224', '32 GB', NULL, 'Servidor respaldos', NOW(), NOW());

INSERT INTO `planilla_equipo_sistema_operativo` (`id`, `planilla_equipo_id`, `nombre`, `version`, `observacion`) VALUES
(1, 1, 'Windows', '11 Pro', NULL),
(2, 2, 'Windows', '11 Pro', NULL),
(3, 3, 'Windows', '11 Pro', NULL),
(4, 7, 'Windows Server', '2019', NULL),
(5, 8, 'Windows Server', '2022', NULL);

INSERT INTO `planilla_equipo_almacenamiento` (`id`, `planilla_equipo_id`, `tipo`, `capacidad`, `rol`, `observacion`) VALUES
(1, 1, 'SSD', 512, 1, 'Disco principal'),
(2, 2, 'SSD', 512, 1, NULL),
(3, 3, 'SSD', 256, 1, NULL),
(4, 5, 'SSD', 256, 1, NULL),
(5, 7, 'HDD', 2000, 1, 'Almacenamiento datos'),
(6, 7, 'SSD', 500, 2, 'Sistema operativo'),
(7, 8, 'HDD', 4000, 1, 'Backups');

-- -----------------------------------------------------------------------------
-- Equipos (inventario principal - mayor volumen)
-- -----------------------------------------------------------------------------
INSERT INTO `equipo` (`id`, `tipo_id`, `ubicacion_id`, `planilla_id`, `nombre`, `mac`, `ip`, `observacion`, `condicion`) VALUES
(1, 1, 2, 1, 'PC-ADM-01', '00:1A:2B:3C:4D:01', '192.168.1.101', NULL, 1),
(2, 1, 2, 2, 'PC-ADM-02', '00:1A:2B:3C:4D:02', '192.168.1.102', NULL, 1),
(3, 2, 4, 3, 'NB-SEC-01', '00:1A:2B:3C:4D:03', '192.168.1.103', NULL, 1),
(4, 2, 4, 4, 'NB-SEC-02', '00:1A:2B:3C:4D:04', '192.168.1.104', NULL, 1),
(5, 1, 3, 5, 'PC-SIS-01', '00:1A:2B:3C:4D:05', '192.168.1.105', 'Mesa soporte', 1),
(6, 1, 3, 6, 'PC-SIS-02', '00:1A:2B:3C:4D:06', '192.168.1.106', NULL, 1),
(7, 4, 8, 7, 'SRV-FILES-01', '00:1A:2B:3C:4D:07', '192.168.1.10', NULL, 1),
(8, 4, 8, 8, 'SRV-BACKUP-01', '00:1A:2B:3C:4D:08', '192.168.1.11', NULL, 1),
(9, 3, 2, NULL, 'IMP-ADM-01', '00:1A:2B:3C:4D:09', '192.168.1.150', 'Impresora láser', 1),
(10, 3, 4, NULL, 'IMP-SEC-01', '00:1A:2B:3C:4D:10', '192.168.1.151', NULL, 1),
(11, 3, 6, NULL, 'IMP-SAL-01', '00:1A:2B:3C:4D:11', '192.168.1.152', NULL, 1),
(12, 5, 1, NULL, 'CAM-REC-01', '00:1A:2B:3C:4D:12', '192.168.1.201', NULL, 1),
(13, 5, 6, NULL, 'CAM-SAL-01', '00:1A:2B:3C:4D:13', '192.168.1.202', NULL, 1),
(14, 6, 8, NULL, 'DVR-01', '00:1A:2B:3C:4D:14', '192.168.1.210', NULL, 1),
(15, 1, 5, NULL, 'PC-ARC-01', '00:1A:2B:3C:4D:15', '192.168.1.107', 'Sin planilla cargada', 1),
(16, 1, 1, NULL, 'PC-REC-01', '00:1A:2B:3C:4D:16', '192.168.1.108', NULL, 1),
(17, 2, 2, NULL, 'NB-ADM-01', '00:1A:2B:3C:4D:17', '192.168.1.109', NULL, 1),
(18, 2, 3, NULL, 'NB-SIS-01', '00:1A:2B:3C:4D:18', '192.168.1.110', NULL, 1),
(19, 3, 7, NULL, 'IMP-DEP-01', '00:1A:2B:3C:4D:19', '192.168.1.153', 'Depósito', 1),
(20, 1, 7, NULL, 'PC-DEP-01', '00:1A:2B:3C:4D:20', '192.168.1.111', 'Repuesto', 2);

-- -----------------------------------------------------------------------------
-- Características de equipos
-- -----------------------------------------------------------------------------
INSERT INTO `caracteristica` (`id`, `tipo_caracteristica_id`, `equipo_id`, `descripcion`, `componente`) VALUES
(1, 1, 1, 'Intel i5-11500', 'Procesador'),
(2, 2, 1, '16 GB DDR4', 'Memoria ram'),
(3, 3, 1, '512 GB', 'Disco ssd'),
(4, 1, 5, 'Intel i5-12400', 'Procesador'),
(5, 2, 5, '8 GB DDR4', 'Memoria ram'),
(6, 9, 7, 'Intel Xeon E-2224', 'Procesador'),
(7, 10, 7, '32 GB ECC', 'Memoria ram'),
(8, 11, 7, '2 TB RAID', 'Disco hdd'),
(9, 1, 3, 'Intel i7-1255U', 'Procesador'),
(10, 2, 3, '16 GB DDR4', 'Memoria ram');

-- -----------------------------------------------------------------------------
-- Asignación personal ↔ equipo
-- -----------------------------------------------------------------------------
INSERT INTO `usuario_equipo` (`id`, `usuario_id`, `equipo_id`, `fecha_inicio`, `fecha_fin`, `modificadoPor_id`, `is_actual`) VALUES
(1, 1, 1, '2025-01-10 08:00:00', NULL, @admin_id, 1),
(2, 2, 2, '2025-01-10 08:00:00', NULL, @admin_id, 1),
(3, 3, 3, '2025-02-01 09:00:00', NULL, @admin_id, 1),
(4, 4, 4, '2025-02-01 09:00:00', NULL, @admin_id, 1),
(5, 7, 5, '2025-03-01 08:30:00', NULL, @admin_id, 1),
(6, 8, 6, '2025-03-01 08:30:00', NULL, @admin_id, 1),
(7, 5, 9, '2025-01-15 10:00:00', NULL, @admin_id, 1),
(8, 9, 15, '2025-04-01 11:00:00', NULL, @admin_id, 1),
(9, 10, 16, '2025-04-15 11:00:00', NULL, @admin_id, 1),
(10, 11, 17, '2025-05-01 08:00:00', NULL, @admin_id, 1),
(11, 12, 3, '2024-06-01 08:00:00', '2025-01-31 17:00:00', @admin_id, 0),
(12, 6, 1, '2024-01-01 08:00:00', '2024-12-31 17:00:00', @admin_id, 0);

-- -----------------------------------------------------------------------------
-- Historial de ubicación / ip de equipos
-- -----------------------------------------------------------------------------
INSERT INTO `equipo_historial` (`id`, `equipo_id`, `ubicacion_id`, `ip`, `host`, `modificadoPor_id`, `fecha_inicio`, `fecha_fin`, `es_ubicacion`) VALUES
(1, 1, 2, '192.168.1.101', 'pc-adm-01', @admin_id, '2025-01-10 08:00:00', NULL, 1),
(2, 1, 1, '192.168.1.50', 'pc-adm-01-old', @admin_id, '2024-06-01 08:00:00', '2025-01-09 18:00:00', 1),
(3, 5, 3, '192.168.1.105', 'pc-sis-01', @admin_id, '2025-03-01 08:30:00', NULL, 1),
(4, 7, 8, '192.168.1.10', 'srv-files-01', @admin_id, '2024-01-01 00:00:00', NULL, 1),
(5, 3, 4, '192.168.1.103', 'nb-sec-01', @admin_id, '2025-02-01 09:00:00', NULL, 1),
(6, 9, 2, '192.168.1.150', 'imp-adm-01', @admin_id, '2025-01-15 10:00:00', NULL, 1);

-- -----------------------------------------------------------------------------
-- Estados de ticket (flujo de trabajo)
-- -----------------------------------------------------------------------------
INSERT INTO `pedido_estado` (`id`, `nombre`, `is_active`) VALUES
(1, 'Recibido', 1),
(2, 'Asignado', 1),
(3, 'Pendiente', 1),
(4, 'En Proceso', 1),
(5, 'Resuelto', 1),
(6, 'Finalizado', 1),
(7, 'Demorado', 1),
(8, 'Desestimado', 1);

-- -----------------------------------------------------------------------------
-- Tickets de soporte
-- -----------------------------------------------------------------------------
INSERT INTO `pedido` (`id`, `tecnico_asignado_id`, `solicitante_id`, `ubicacion_id`, `solicitante_texto`, `ubicacion_texto`, `solicitud`, `solucion`, `prioridad`, `observacion`, `fecha`) VALUES
(1, NULL, NULL, NULL, 'María lópez', 'Primer piso - administración', 'No enciende el monitor', NULL, 2, NULL, '2026-05-20 09:15:00'),
(2, NULL, NULL, NULL, 'Juan pérez', 'Primer piso - administración', 'Impresora atascada', NULL, 3, NULL, '2026-05-21 10:30:00'),
(3, NULL, NULL, NULL, 'Ana garcía', 'Segundo piso - secretaría', 'Solicitud de mouse nuevo', NULL, 3, NULL, '2026-05-22 11:00:00'),
(4, @tecnico1_id, NULL, NULL, 'Roberto martínez', 'Segundo piso - secretaría', 'Notebook lenta al iniciar', NULL, 2, NULL, '2026-05-23 08:45:00'),
(5, @tecnico1_id, NULL, NULL, 'Claudia rodríguez', 'Tercer piso - salas', 'Sin conexión a red', NULL, 1, 'Urgente sala de audiencias', '2026-05-23 14:20:00'),
(6, @tecnico2_id, NULL, NULL, 'Diego sánchez', 'Planta baja - recepción', 'Cámara sin imagen', NULL, 2, NULL, '2026-05-24 09:00:00'),
(7, @tecnico1_id, NULL, NULL, 'Patricia torres', 'Primer piso - sistemas', 'Instalar software contable', 'Software instalado y probado', 2, NULL, '2026-05-18 10:00:00'),
(8, @tecnico2_id, NULL, NULL, 'Fernando ruiz', 'Primer piso - sistemas', 'Ampliar disco servidor', 'Disco ampliado 500 GB', 1, NULL, '2026-05-15 11:30:00'),
(9, @tecnico1_id, NULL, NULL, 'Lucía díaz', 'Segundo piso - archivo', 'Pc no reconoce usb', 'Puerto usb reemplazado', 2, NULL, '2026-05-10 15:00:00'),
(10, @tecnico2_id, NULL, NULL, 'Miguel álvarez', 'Segundo piso - archivo', 'Cambio de toner impresora', 'Toner reemplazado', 3, NULL, '2026-05-05 16:00:00'),
(11, @tecnico1_id, NULL, NULL, 'Sofía romero', 'Tercer piso - salas', 'Proyector sin señal', 'Cable hdmi reemplazado', 2, NULL, '2026-05-01 09:30:00'),
(12, @tecnico2_id, NULL, NULL, 'Pablo benítez', 'Primer piso - administración', 'Migración de datos pendiente', NULL, 2, 'Esperando ventana de mantenimiento', '2026-05-25 08:00:00'),
(13, NULL, NULL, NULL, 'Visitante externo', 'Planta baja - recepción', 'Solicitud fuera de alcance', NULL, 3, 'Derivar a proveedor', '2026-05-26 10:00:00'),
(14, @tecnico1_id, NULL, NULL, 'María lópez', 'Primer piso - administración', 'Actualizar antivirus', NULL, 2, NULL, '2026-05-27 09:00:00'),
(15, NULL, NULL, NULL, 'Juan pérez', 'Depósito técnico', 'Consulta por equipo repuesto', NULL, 3, NULL, '2026-05-28 11:00:00');

-- -----------------------------------------------------------------------------
-- Historial de estados por ticket
-- -----------------------------------------------------------------------------
INSERT INTO `pedido_historial_estado` (`id`, `pedido_estado_id`, `pedido_id`, `usuario_id`, `observacion`, `fecha`) VALUES
-- Recibidos (1-3, 15)
(1, 1, 1, @admin_id, NULL, '2026-05-20 09:15:00'),
(2, 1, 2, @admin_id, NULL, '2026-05-21 10:30:00'),
(3, 1, 3, @admin_id, NULL, '2026-05-22 11:00:00'),
(4, 1, 15, @admin_id, NULL, '2026-05-28 11:00:00'),
-- Asignados en curso (4-6, 14)
(5, 1, 4, @admin_id, NULL, '2026-05-23 08:45:00'),
(6, 2, 4, @admin_id, 'Asignado a técnico', '2026-05-23 09:00:00'),
(7, 1, 5, @admin_id, NULL, '2026-05-23 14:20:00'),
(8, 2, 5, @admin_id, 'Prioridad alta', '2026-05-23 14:30:00'),
(9, 4, 5, @tecnico1_id, 'Diagnóstico en curso', '2026-05-23 15:00:00'),
(10, 1, 6, @admin_id, NULL, '2026-05-24 09:00:00'),
(11, 2, 6, @admin_id, NULL, '2026-05-24 09:15:00'),
(12, 1, 14, @admin_id, NULL, '2026-05-27 09:00:00'),
(13, 2, 14, @admin_id, NULL, '2026-05-27 09:10:00'),
-- Resueltos (7-9)
(14, 1, 7, @admin_id, NULL, '2026-05-18 10:00:00'),
(15, 2, 7, @admin_id, NULL, '2026-05-18 10:30:00'),
(16, 4, 7, @tecnico1_id, NULL, '2026-05-18 11:00:00'),
(17, 5, 7, @tecnico1_id, 'Software instalado y probado', '2026-05-18 14:00:00'),
(18, 1, 8, @admin_id, NULL, '2026-05-15 11:30:00'),
(19, 2, 8, @admin_id, NULL, '2026-05-15 12:00:00'),
(20, 5, 8, @tecnico2_id, 'Disco ampliado', '2026-05-16 10:00:00'),
(21, 1, 9, @admin_id, NULL, '2026-05-10 15:00:00'),
(22, 2, 9, @admin_id, NULL, '2026-05-10 15:30:00'),
(23, 5, 9, @tecnico1_id, 'Puerto reemplazado', '2026-05-11 09:00:00'),
-- Finalizados (10-11)
(24, 1, 10, @admin_id, NULL, '2026-05-05 16:00:00'),
(25, 2, 10, @admin_id, NULL, '2026-05-05 16:15:00'),
(26, 5, 10, @tecnico2_id, NULL, '2026-05-06 10:00:00'),
(27, 6, 10, @admin_id, 'Usuario confirmó', '2026-05-06 11:00:00'),
(28, 1, 11, @admin_id, NULL, '2026-05-01 09:30:00'),
(29, 2, 11, @admin_id, NULL, '2026-05-01 10:00:00'),
(30, 5, 11, @tecnico1_id, NULL, '2026-05-02 09:00:00'),
(31, 6, 11, @admin_id, NULL, '2026-05-02 10:00:00'),
-- Demorado (12)
(32, 1, 12, @admin_id, NULL, '2026-05-25 08:00:00'),
(33, 2, 12, @admin_id, NULL, '2026-05-25 08:30:00'),
(34, 7, 12, @tecnico2_id, 'Esperando ventana de mantenimiento', '2026-05-25 09:00:00'),
-- Desestimado (13)
(35, 1, 13, @admin_id, NULL, '2026-05-26 10:00:00'),
(36, 8, 13, @admin_id, 'Fuera de alcance del área', '2026-05-26 10:30:00');

-- -----------------------------------------------------------------------------
-- Equipo operativo en tickets
-- -----------------------------------------------------------------------------
INSERT INTO `pedido_tecnico_asignado` (`id`, `pedido_id`, `tecnico_asignado_id`, `usuario_asignacion_id`, `fecha_asignacion`, `es_operativo`) VALUES
(1, 5, @tecnico1_id, @admin_id, '2026-05-23 14:30:00', 1),
(2, 7, @tecnico1_id, @admin_id, '2026-05-18 10:30:00', 1),
(3, 8, @tecnico2_id, @admin_id, '2026-05-15 12:00:00', 1),
(4, 10, @tecnico2_id, @admin_id, '2026-05-05 16:15:00', 1),
(5, 11, @tecnico1_id, @admin_id, '2026-05-01 10:00:00', 1);

-- -----------------------------------------------------------------------------
-- Vincular algunos tickets con equipos
-- -----------------------------------------------------------------------------
INSERT INTO `pedido_equipo` (`id`, `pedido_id`, `equipo_id`, `fecha_inicio`, `fecha_fin`) VALUES
(1, 1, 1, '2026-05-20 09:15:00', NULL),
(2, 2, 9, '2026-05-21 10:30:00', NULL),
(3, 4, 3, '2026-05-23 08:45:00', NULL),
(4, 5, 5, '2026-05-23 14:20:00', NULL),
(5, 6, 12, '2026-05-24 09:00:00', NULL),
(6, 7, 5, '2026-05-18 10:00:00', '2026-05-18 14:00:00'),
(7, 8, 7, '2026-05-15 11:30:00', '2026-05-16 10:00:00');

-- Restaurar auto_increment
ALTER TABLE `ubicacion` AUTO_INCREMENT = 9;
ALTER TABLE `tipo_componente` AUTO_INCREMENT = 11;
ALTER TABLE `tipo_equipo` AUTO_INCREMENT = 7;
ALTER TABLE `caracteristica_tipo_equipo` AUTO_INCREMENT = 16;
ALTER TABLE `personal` AUTO_INCREMENT = 13;
ALTER TABLE `planilla_equipo` AUTO_INCREMENT = 9;
ALTER TABLE `equipo` AUTO_INCREMENT = 21;
ALTER TABLE `pedido` AUTO_INCREMENT = 16;
ALTER TABLE `pedido_estado` AUTO_INCREMENT = 9;

SELECT 'Datos dummy cargados correctamente.' AS resultado;
SELECT CONCAT('Usuarios técnicos demo: tecnico1 / tecnico2 (password: demo123)') AS nota;
