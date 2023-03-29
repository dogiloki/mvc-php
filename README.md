# mvc-php
Usa el servidor de apache, con los .htaccess activados.

# Instalar librerías
- composer install

# Conversión de nombres en el código
- Variables: small_caps, Ejemplo. soy_una_variable
- Constantes: ALL_CAPS, Ejemplo. SOY_UNA_CONSTANTE
- Métodos: camelCase, Ejemplo. soyUnMetodo
- Clases: PascalCase, Ejemplo. SoyUnaClase

# Migrar base de datos
- php manager migration [nombre base de datos] -> En caso de no envíar base de datos, usará en nombre de la configuración src/config.php
<br>
Creará la base de datos y las tablas; en caso de ya existir las tablas no hará modificación.

# Crear controlador y modelo
- php manager new controller [nombre] -> Crea un archivo en la carpeta controllers, con el código inicial.
- php manager new model [nombre] -> Crea un archivo en la carpeta models, con el código inicial.
- php manager new cm [nombre] -> Crea un archivo en la carpeta controllers y models, con el código inicial.
