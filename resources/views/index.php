@extends('layouts.header',["title"=>"Inicio"])
<h1>{{__('welcome',['name'=>'Julio'])}}</h1>
<label>
    Búsqueda en tiempo real
    <input type="checkbox" id="check-live-search">
</label>
<input type="search" placeholder="Buscar por nombre" id="box-search">
<button id="btn-search">Buscar</button>
<form action="{{route('test')}}" method="POST">
    @csrf
    <input type="text" name="name">
    <input type="submit" value="Enviar">
</form>
<component:vista>
    {
        "wires":{
            "box-search.value":"search",
            "check-live-search.checked":"live_search"
        },
        "events":[
            {
                "id":"btn-search",
                "event":"click",
                "method":"search"
            },
            {
                "id":"box-search",
                "event":"keyup",
                "delay":500
            },
            {
                "id":"check-live-search",
                "event":"change"
            }
        ]    
    }
</component:vista>
@extends('layouts.footer')
@scriptsSPA