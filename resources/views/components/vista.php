<h2>Soy un componente</h2>
@foreach($users as $index=>$user)
    <p>{{$index}}: {{$user->name}}</p>
@endforeach