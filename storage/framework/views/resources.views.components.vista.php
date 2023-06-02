<div>
    <h2>Soy un componente:</h2>
    <?php if($search!=""){ ?>
        <p><?php echo $search; ?></p>
    <?php } ?>
    <input type="search" wire:model="search" placeholder="Escribe un nombre">
    <label>
        Busqueda en tiempo real
        <input type="checkbox" wire:model="live_search">
    </label>
    <button wire:click="buscar()">Buscar</button>
    <ul>
        <?php foreach($variable as $user){ ?>
            <li><?php echo $user->name; ?> - <?php echo $user->email; ?></li>
        <?php } ?>
    </ul>
</div>
