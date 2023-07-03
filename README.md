# NO CONFIGURADO CORRECTAMENTE PARA DEPLOY SERVIDOR CON SUBDIRECTORIO. EJEMPLO, http://127.0.0.1:8000/carpeta/
## mvc-php
Es un pequeño prototipo de un Framework que cuenta con enrutamiento, ORM (sencillo inspirado en Hibernate y Eloquent), Modelos, Controladores, Vistas y una configuración global en un archivo llamado "config.env".
Tiene librerías para almacenar archivos en servidor, hacer peticiones HTTP, Servidor de Socket (sencillo inspirado en Socket.io) y envío de correo electrónico.
Implementa un básico sistema de migración que consiste en ejecutar archivos ubicados en database/migrations

## Instalación
### Crear proyecto
```sh
composer create-project dogiloki/mvc-php mvc-php dev-main
``````

### Crear carpetas y archivo iniciales necesarios para el framework
```sh
php manager create
``````
## ¿Cómo inciar?
### Desplegar proyecto local
Utiliza el servidor web integrado de php para pruebas locales con:
```sh
php manager serve
``````
## Comando de creación de archivo

### Crear un controlador. Lo crea en una carpeta app/controllers con código incial
```sh
php manager new controller [nombre]
``````
### Crear un modelo. Lo crea en una carpeta app/models con código incial
```sh
php manager new model [nombre]
``````
