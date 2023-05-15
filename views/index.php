<!DOCTYPE html>
<html>
<head>
    <title>Inicio</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{urlPublic('css/normalize.css')}}">
</head>
<body>
    <h1>Hola mundo</h1>
    <p>Bienvenido</p>
    <var-variable>Texto en vista</var-variable>
    <input type="button" onclick="SPA.renderVar('variable')" value="Cargar variable"><br>
    <input type="text" id="box-text" value="Texto para componente" placeholder="Escribe algo..."><br>
    <input type="button" onclick="SPA.renderComponent('vista',{
        variable:document.getElementById('box-text').value
    })" value="Cargar componente"><br>
    Componente:
    <component-vista></component-vista>
    @if(isset($variable1))
        <p>{{$variable1}}</p>
    @elseif(isset($variable2))
        <p>{{$variabl2}}</p>
    @else
        <p>{{$variable3??""}}</p>
    @endif
</body>
</html>

<script src="{{urlPublic('js/fetch.js')}}"></script>
<script src="{{urlPublic('js/spa.js')}}"></script>