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
    <input type="search" wire:vista("search")>
    <component:vista :variable="{{base64_encode(serialize($user))}}"/>
</body>
</html>

<script src="{{urlPublic('js/fetch.js')}}"></script>
<script src="{{urlPublic('js/spa.js')}}"></script>