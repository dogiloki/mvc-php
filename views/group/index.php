<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Grupos</title>
	<link rel="stylesheet" type="text/css" href="<?php echo urlPublic('header.css') ?>">
</head>
<body>

	<header>
        <nav>
            <ul>
                <li><a href="<?php echo url('/') ?>">Inicio</a></li>
                <li><a href="<?php echo url('/user') ?>">Usuarios</a></li>
                <li><a href="<?php echo url('/group') ?>">Grupos</a></li>
            </ul>
        </nav>
    </header>

	<form action="<?php echo isset($group_found)?route("group.update")."/".$group_found->id:route("group.store"); ?>" method="POST">
		<label>
			Nombre
			<input type="text" name="name" value="<?php echo isset($group_found)?$group_found->name:''; ?>">
		</label>
		<label>
			Descripción
			<input type="text" name="description" value="<?php echo isset($group_found)?$group_found->description:''; ?>">
		</label>
		<input type="submit" value="<?php echo isset($group_found)?"Guardar cambios":"Registrar" ?>">
	</form>

	<table border=1>
		<tr>
			<th>Nombre</th>
			<th>Descripción</th>
			<th>Acciones</th>
		</tr>
		<?php
			foreach($groups as $group){
				echo "<tr>";
				echo "<td>{$group->name}</td>";
				echo "<td>{$group->description}</td>";
				echo "<td><a href='".route('group.index',$group->id)."'>EDITAR</a></td>";
				echo "<td><a href='".route('group.delete',$group->id)."'>ELIMINAR</a></td>";
				echo "</tr>";
			}
		?>
	</table>

</body>
</html>