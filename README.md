# mvc-php
Es un pequeño prototipo de un Framework que cuenta con enrutamiento, ORM (sencillo inspirado en Hibernate y Eloquent), Modelos, Controladores, Vistas y una configuración global en un archivo llamado "config.cfg".
Tiene librerías para almacenar archivos en servidor, hacer peticiones HTTP, Servidor de Socket (sencillo inspirado en Socket.io) y envío de correo electrónico.
Implementa un básico sistema de migración que consiste en ejecutar setencias SQL de un archivo ubicado en "src/migration.php"

# Instalar librerías
- composer install

# Iniciar servidor
Utiliza el servidor web integrado de php para pruebas locales con:
- php manager serve

# Migrar base de datos
- php manager migration [nombre base de datos] -> En caso de no envíar base de datos, usará el nombre de la configuración src/config.php
<br>
Ejecutará las sentencias sql; en caso de ya existir las tablas no las volverá a crear.

# Crear controlador y modelo
- php manager new controller [nombre] -> Crea un archivo en la carpeta controllers, con el código inicial.
- php manager new model [nombre] -> Crea un archivo en la carpeta models, con el código inicial.
- php manager new cm [nombre] -> Crea un archivo en la carpeta controllers y models, con el código inicial.
