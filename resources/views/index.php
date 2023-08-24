@extends('layouts.header',["title"=>"Inicio"])
<h1>Hola mundo</h1>
<input type="search" placeholder="Buscar por nombre" id="box-search">
<button id="btn-search">Buscar</button>
<component-vista>
    <event-btn-search>
        <on-click on_id="box-search" emit="search">
    </event-btn-search>
    <event-box-search>
        <on-keyup on_id="box-search" emit="search">
    </event-box-search>
</component-vista>
@extends('layouts.footer')
@spa