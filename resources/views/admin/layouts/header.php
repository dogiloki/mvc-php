<link rel="stylesheet" href="{{scss('header.scss')}}">
@php
    $user=user();
@endphp
<header>
    <div class="container-logo">
        <img src="{{url('assets/bitacora.png')}}"/>
    </div>
    <div class="container-user">
        <div class="name">{{$user->fullName()}}</div>
        <div class="user">{{$user->registration}}</div>
    </div>
    <nav class="navigator">
        <a href="{{route('admin-modules','users')}}">Usuarios</a>
        <a href="{{route('admin-modules','documents')}}">Documentos</a>
        <a href="{{route('logout')}}">Cerrar sesión</a>
    </nav>
</header>