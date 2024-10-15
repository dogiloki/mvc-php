@extends('layouts.header',["title"=>"Inicio"])
<h1>{{__('messages.welcome',['name'=>'Julio'])}}</h1>
<form action="{{route('test-post')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="name">
    <input type="submit" value="Enviar">
</form>
@extends('layouts.footer')