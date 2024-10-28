@extends('layouts.header')
<link rel="stylesheet" href="{{scss('form.scss')}}">
<div class="container-form">
    <form action="{{route('api-login')}}" method="POST" id="form-login" enctype="multipart/form-data">
        <h1>Bitacora digital</h1>
        @csrf
        <label>
            <span>Usuario</span>
            <input type="text" name="user" required>
        </label>
        <label>
            <span>Contraseña</span>
            <input type="password" name="password" required>
        </label>
        <div class="container-btn">
            <input type="submit" class="btn-primary" value="Iniciar sesión">
        </div>
    </form>
</div>
@extends('layouts.footer')