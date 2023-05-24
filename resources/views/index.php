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
    @php
        $user=\app\Models\User::all();
    @endphp
    <component:vista :variable="{{base64_encode(serialize($user))}}">
    </component:vista>
</body>
</html>

<script src="{{urlPublic('js/fetch.js')}}"></script>
<script src="{{urlPublic('js/component/component.js')}}"></script>
<script src="{{urlPublic('js/component/wire.js')}}"></script>
<script src="{{urlPublic('js/component/app.js')}}"></script>
