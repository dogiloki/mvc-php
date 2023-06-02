<?php view('layouts.header',["title"=>"Inicio"]); ?>
<h1>Hola mundo</h1>
<p>Bienvenido</p>
<?php
    $user=\app\Models\User::all();
?>
<component:vista :variable="<?php echo base64_encode(serialize($user)); ?>"/>
<?php view('layouts.footer'); ?>
