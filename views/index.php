<!DOCTYPE html>
<html>
<head>
    <title>Inicio</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="<?php echo urlPublic('css/normalize.css') ?>">
</head>
<body>
    <h1>Hola mundo</h1>
    <p>Bienvenido</p>
    <var-variable>Texto en vista</var-variable>
    <input type="button" onclick="renderVar('variable')" value="Obtener variable"><br>
    <input type="button" onclick="renderComponent('vista')" value="Obtener componente"><br>
    Componente:
    <component-vista></component-vista>
</body>
</html>

<script src="<?php echo urlPublic('js/fetch.js') ?>"></script>
<script src="<?php echo urlPublic('js/spa.js') ?>"></script>