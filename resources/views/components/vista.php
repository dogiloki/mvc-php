<h2>Soy un componente:</h2>
<input type="search" wire:model="search" placeholder="Escribe un nombre">
<input type="search" wire:model="search" placeholder="Escribe un nombre">
<p>{{$search}}</p>
<ul>
    @foreach($variable as $user)
        <li>{{$user->name}} - {{$user->email}}</li>
    @endforeach
</ul>
