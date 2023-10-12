@extends('layouts.header',["title"=>"Inicio"])
<h1>{{__('welcome',['name'=>'Julio'])}}</h1>
<form action="{{route('test-post')}}" method="POST">
    @csrf
    <input type="text" name="name">
    <input type="submit" value="Enviar">
</form>
<label>
    Búsqueda en tiempo real
    <input type="checkbox" wire:name="vista" wire:sync.change="live_search.checked">
</label>
<input type="search" placeholder="Buscar por nombre" wire:name="vista" wire:sync.keyup="search.value">
<input type="search" placeholder="Buscar por nombre" wire:name="vista" wire:sync="temp.value">
<button wire:name="vista" wire:click="search">Buscar</button>
@component("vista")
@extends('layouts.footer')
@scriptsSPA