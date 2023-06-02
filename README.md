## NO CONFIGURADO CORRECTAMENTE PARA DEPLOY SERVIDOR CON SUBDIRECTORIO, EJEMPLOE http://127.0.0.1:8000/carpeta/
# mvc-php
Es un pequeño prototipo de un Framework que cuenta con enrutamiento, ORM (sencillo inspirado en Hibernate y Eloquent), Modelos, Controladores, Vistas y una configuración global en un archivo llamado "config.cfg".
Tiene librerías para almacenar archivos en servidor, hacer peticiones HTTP, Servidor de Socket (sencillo inspirado en Socket.io) y envío de correo electrónico.
Implementa un básico sistema de migración que consiste en ejecutar archivos ubicados en database/migrations

# Instalar librerías
- composer install

# Iniciar servidor
Utiliza el servidor web integrado de php para pruebas locales con:
- php manager serve

# Crear controlador y modelo
- php manager new controller [nombre] -> Crea un archivo en la carpeta controllers, con el código inicial.
- php manager new model [nombre] -> Crea un archivo en la carpeta models, con el código inicial.
- php manager new cm [nombre] -> Crea un archivo en la carpeta controllers y models, con el código inicial.
