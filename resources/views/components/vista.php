<h2>Soy un componente:</h2>
<input type="search" wire:keyup="search">
<p>{{$search}}</p>
<ul>
    @foreach($variable as $user)
        <li>{{$user->name}} - {{$user->email}}</li>
    @endforeach
</ul>
