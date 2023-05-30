@extends('layouts.header',["title"=>"Inicio"])
<h1>Hola mundo</h1>
<p>Bienvenido</p>
@php
    $user=\app\Models\User::all();
@endphp
<component:vista :variable="{{base64_encode(serialize($user))}}"/>
@extends('layouts.footer')
