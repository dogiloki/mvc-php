@extends('layouts.header',["title"=>"Inicio"])
<h1>{{__('welcome',['name'=>'Julio'])}}</h1>
<form action="{{route('test-post')}}" method="POST">
    @csrf
    <input type="text" name="name">
    <input type="submit" value="Enviar">
</form>
@extends('layouts.footer')