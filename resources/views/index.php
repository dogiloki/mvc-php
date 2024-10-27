@extends('layouts.header',["title"=>"Inicio"])
<h1>{{__('messages.welcome',['name'=>'Julio'])}}</h1>
<form action="{{route('test-post')}}" method="POST" enctype="multipart/form-data" id="form">
    @csrf
    <progress id="progress" value="0" max="100"></progress>
    <label>
        <span>Nombre</span>
        <input type="text" name="name" required>
    </label>
    <input type="submit" value="Enviar">
</form>
@extends('layouts.footer')