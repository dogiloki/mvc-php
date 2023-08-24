@extends('layouts.header',["title"=>"Inicio"])
<h1>Hola mundo</h1>
<label>
    Búsqueda en tiempo real
    <input type="checkbox" id="check-live-search">
</label>
<input type="search" placeholder="Buscar por nombre" id="box-search">
<button id="btn-search">Buscar</button>
<component:vista>
    <on:click>
        <id:btn-search search="box-search"/>
        <id:check-live-search live_search="check-live-search:checked"/>
    </on:click>
    <on:keyup>
        <id:box-search search="box-search"/>
    </on:keyup>
</component:vista>
@extends('layouts.footer')
@spa