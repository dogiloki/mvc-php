<h2>Soy un componente: {{$search}}</h2>
{{$live_search?"Búsqueda en tiempo real activada":"Búsqueda en tiempo real desactivada"}}
@foreach($users as $index=>$user)
    <p>{{$index}}: {{$user->name}}</p>
@endforeach