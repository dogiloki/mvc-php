<h2>Soy un componente:</h2>
<input type="search" wire:keyup="search" value="{{$search}}">
<ul>
    @foreach($variable as $user)
        <li>{{$user->name}}: {{$user->email}}</li>
    @endforeach
</ul>
