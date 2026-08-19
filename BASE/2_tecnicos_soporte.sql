-- -----------------------------------------------------------------------------
-- Usuarios técnicos de soporte (asignables en tickets)
-- Password de todos: demo123
-- Ejecutar: mysql -u root sistema_soporte < seeds/tecnicos_soporte.sql
-- -----------------------------------------------------------------------------

SET NAMES utf8mb4;

-- Rol para técnicos que atienden tickets asignados
INSERT INTO `role` (`role_name`, `created_at`, `updated_at`)
SELECT 'ROLE_TECNICO_SOPORTE', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `role` WHERE `role_name` = 'ROLE_TECNICO_SOPORTE');

SET @rol_tecnico_id := (SELECT id FROM `role` WHERE `role_name` = 'ROLE_TECNICO_SOPORTE' LIMIT 1);

-- Permisos: ver inicio, listar tickets y cambiar estados (sin asignar ni crear)
INSERT INTO `permiso` (`role_id`, `nombre`)
SELECT @rol_tecnico_id, p.nombre
FROM (
    SELECT 'VER_INICIO' AS nombre UNION ALL
    SELECT 'TICKET_VER' UNION ALL
    SELECT 'TICKET_EDITAR'
) p
WHERE NOT EXISTS (
    SELECT 1 FROM `permiso` x
    WHERE x.role_id = @rol_tecnico_id AND x.nombre = p.nombre
);

-- Hash bcrypt de "demo123"
SET @pwd := '$2y$10$0Ghqg2gImgDN5rlYj6uyP.HWL.LT29hc7hVOE0S4UMYVxCfjmj0JG';

INSERT INTO `user` (`username`, `roles`, `nombre`, `apellido`, `email`, `dni`, `suspended`, `deleted`, `password`, `created_at`, `updated_at`, `is_tecnico`, `rol_actual_id`)
SELECT 'tecnico1', '["ROLE_TECNICO_SOPORTE"]', 'Carlos', 'Gómez', 'carlos.gomez@soporte.local', '30111222', 0, 0, @pwd, NOW(), NOW(), 1, @rol_tecnico_id
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE username = 'tecnico1');

INSERT INTO `user` (`username`, `roles`, `nombre`, `apellido`, `email`, `dni`, `suspended`, `deleted`, `password`, `created_at`, `updated_at`, `is_tecnico`, `rol_actual_id`)
SELECT 'tecnico2', '["ROLE_TECNICO_SOPORTE"]', 'Laura', 'Fernández', 'laura.fernandez@soporte.local', '30222333', 0, 0, @pwd, NOW(), NOW(), 1, @rol_tecnico_id
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE username = 'tecnico2');

INSERT INTO `user` (`username`, `roles`, `nombre`, `apellido`, `email`, `dni`, `suspended`, `deleted`, `password`, `created_at`, `updated_at`, `is_tecnico`, `rol_actual_id`)
SELECT 'tecnico3', '["ROLE_TECNICO_SOPORTE"]', 'Martín', 'López', 'martin.lopez@soporte.local', '30333444', 0, 0, @pwd, NOW(), NOW(), 1, @rol_tecnico_id
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE username = 'tecnico3');

INSERT INTO `user` (`username`, `roles`, `nombre`, `apellido`, `email`, `dni`, `suspended`, `deleted`, `password`, `created_at`, `updated_at`, `is_tecnico`, `rol_actual_id`)
SELECT 'tecnico4', '["ROLE_TECNICO_SOPORTE"]', 'Ana', 'Ruiz', 'ana.ruiz@soporte.local', '30444555', 0, 0, @pwd, NOW(), NOW(), 1, @rol_tecnico_id
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE username = 'tecnico4');

-- Actualizar técnicos demo existentes al rol correcto (si ya estaban creados)
UPDATE `user` u
SET u.roles = '["ROLE_TECNICO_SOPORTE"]',
    u.rol_actual_id = @rol_tecnico_id,
    u.is_tecnico = 1
WHERE u.username IN ('tecnico1', 'tecnico2', 'tecnico3', 'tecnico4');

SELECT 'Técnicos de soporte listos. Usuarios: tecnico1..tecnico4 / password: demo123' AS resultado;
