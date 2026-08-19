-- -----------------------------------------------------------------------------
-- Rol y usuario coordinador de soporte (administra y asigna tickets)
-- Password: demo123
-- Ejecutar: mysql -u root sistema_soporte < seeds/coordinador_soporte.sql
-- -----------------------------------------------------------------------------

SET NAMES utf8mb4;

INSERT INTO `role` (`role_name`, `created_at`, `updated_at`)
SELECT 'ROLE_COORDINADOR_SOPORTE', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `role` WHERE `role_name` = 'ROLE_COORDINADOR_SOPORTE');

SET @rol_coord_id := (SELECT id FROM `role` WHERE `role_name` = 'ROLE_COORDINADOR_SOPORTE' LIMIT 1);

-- Permisos completos de tickets + acceso al inicio
INSERT INTO `permiso` (`role_id`, `nombre`)
SELECT @rol_coord_id, p.nombre
FROM (
    SELECT 'VER_INICIO' AS nombre UNION ALL
    SELECT 'TICKET_VER' UNION ALL
    SELECT 'TICKET_CREAR' UNION ALL
    SELECT 'TICKET_EDITAR' UNION ALL
    SELECT 'TICKET_ELIMINAR' UNION ALL
    SELECT 'TICKET_ASIGNAR'
) p
WHERE NOT EXISTS (
    SELECT 1 FROM `permiso` x
    WHERE x.role_id = @rol_coord_id AND x.nombre = p.nombre
);

SET @pwd := '$2y$10$0Ghqg2gImgDN5rlYj6uyP.HWL.LT29hc7hVOE0S4UMYVxCfjmj0JG';

INSERT INTO `user` (`username`, `roles`, `nombre`, `apellido`, `email`, `dni`, `suspended`, `deleted`, `password`, `created_at`, `updated_at`, `is_tecnico`, `rol_actual_id`)
SELECT 'coordinador1', '["ROLE_COORDINADOR_SOPORTE"]', 'María', 'Coordinadora', 'maria.coordinadora@soporte.local', '29123456', 0, 0, @pwd, NOW(), NOW(), 0, @rol_coord_id
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE username = 'coordinador1');

INSERT INTO `user` (`username`, `roles`, `nombre`, `apellido`, `email`, `dni`, `suspended`, `deleted`, `password`, `created_at`, `updated_at`, `is_tecnico`, `rol_actual_id`)
SELECT 'coordinador2', '["ROLE_COORDINADOR_SOPORTE"]', 'Jorge', 'Supervisor', 'jorge.supervisor@soporte.local', '29234567', 0, 0, @pwd, NOW(), NOW(), 0, @rol_coord_id
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE username = 'coordinador2');

SELECT 'Coordinadores listos. Usuarios: coordinador1, coordinador2 / password: demo123' AS resultado;
