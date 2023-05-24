<div>
    <h2>Soy un componente:</h2>
    <input type="search" wire:model="search" placeholder="Escribe un nombre">
    @if($search!="")
        {{$search}}
    @endif
    <label>
        Busqueda en tiempo real
        <input type="checkbox" wire:model="live_search">
    </label>
    <button wire:click="buscar()">Buscar</button>
    <ul>
        @foreach($variable as $user)
            <li>{{$user->name}} - {{$user->email}}</li>
        @endforeach
    </ul>
</div>
