<h2>Soy un componente:</h2>
<ul>
    @foreach($variable as $user)
        <li>{{$user->name}}: {{$user->email}}</li>
    @endforeach
</ul>
