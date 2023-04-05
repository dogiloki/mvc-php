# mvc-php
Es un pequeño framework que cuenta con enrutamiento ORM (sencillo)

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
