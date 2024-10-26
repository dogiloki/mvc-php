@extends('layouts.header',["title"=>"Inicio"])
<h1>{{__('messages.welcome',['name'=>'Julio'])}}</h1>
<form action="{{route('api-create-document')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <label>
        <span>No. Documento</span>
        <input type="text" name="no_document" required>
    </label>
    <label>
        <span>Referencia</span>
        <input type="text" name="reference" required>
    </label>
    <label>
        <span>Remitente</span>
        <input type="text" name="sender" required>
    </label>
    <label>
        <span>Archivo</span>
        <input type="file" name="name" required>
    </label>
    <input type="submit" value="Enviar">
</form>
@extends('layouts.footer')