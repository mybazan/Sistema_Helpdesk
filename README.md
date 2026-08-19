# SISTEMA DE SOPORTE

## REQUISITOS DEL SISTEMA
- Node.js v12.13.0 o superior (node --version).
- PHP v7.4 o superior (php -v)
- Gulp (npm install -g gulp)

## FUNCIÓN BÁSICA
- El funcionamiento del sistema está pensado para llevarlo por roles.
- Al seguir las instrucciones, se crea un usuario con el rol ROLE_SUPERUSER.
- Ningún otro usuario podrá modificar, ver o eliminar datos de la cuenta con el rol ROLE_SUPERUSER.
- No es posible crear un nuevo rol que contenga el string "ROLE_SUPERUSER".

## INSTRUCCIONES
1. Ubicarse en la dirección public/base (cd public/base)
2. Instalar dependencia de node (npm install)
3. Ejecutar gulp (gulp)
4. Procesar archivos de estilo (gulp css)
5. Crear la base de datos local, basarse en los datos .env.
6. Crear y adecuar el archivo .env
7. Ubicarse en la raíz del proyecto (cd ../../)
8. Instalar depedencias de composer (composer install)
9. Crear el esquema en la base de datos (php bin/console doctrine:schema:create)
10. Generar el usuario base (php bin/console doctrine:fixtures:load)
11. Inciar el servidor local (symfony server:start)
12. Abrir navegador en la dirección establecida por el servidor (localhost:8000)
13. Loguear con las credenciales (usuario: maxi contraseña:maxi)
14. En el sistema, ir al menú lateral izquierdo: Administrar -> Roles -> ROLE_SUPERUSER y asignarle todos los permisos necesarios.

Una vez modificado los archivos, es necesario realizar uno pasos para que se apliquen los cambios:
1. Ubicarse en la dirección public/base (cd public/base)
2. Procesar archivos de estilo (gulp css)
