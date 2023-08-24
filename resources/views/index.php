@extends('layouts.header',["title"=>"Inicio"])
<h1>Hola mundo</h1>
<label>
    Búsqueda en tiempo real
    <input type="checkbox" id="check-live-search">
</label>
<input type="search" placeholder="Buscar por nombre" id="box-search">
<button id="btn-search">Buscar</button>
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
                "delay":1000
            },
            {
                "id":"check-live-search",
                "event":"change"
            }
        ]    
    }
</component:vista>
@extends('layouts.footer')
@spa