<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Usuarios</title>
	<link rel="stylesheet" type="text/css" href="<?php echo urlPublic('header.css') ?>">
</head>
<body>

<form action="<?php route('user.store'); ?>" method="POST">
	<label>
		Nombre
		<input type="text" name="name" value="<?php echo isset($user_found)?$user_found->name:''; ?>">
	</label>
	<label>
		Email
		<input type="email" name="email" value="<?php echo isset($user_found)?$user_found->name:''; ?>">
	</label>
	<label>
		Contraseña
		<input type="password" name="password" value="<?php echo isset($user_found)?$user_found->name:''; ?>">
	</label>
	<select name="id_group">
		<?php
			foreach($groups as $group){
				echo "<option value='{$group->id}' title='{$group->description}'>{$group->name}</option>";
			}
		?>
	</select>
	<input type="submit">
</form>

	<table border=1>
		<tr>
			<th rowspan=2>Nombre</th>
			<th rowspan=2>Email</th>
			<th colspan=2>Grupo</th>
			<th colspan=2 rowspan=2>Acciones</th>
		</tr>
		<tr>
			<th>Nombre</th>
			<th>Descripción</th>
		</tr>
		<?php
			foreach($users as $user){
				echo "<tr>";
				echo "<td>{$user->name}</td>";
				echo "<td>{$user->email}</td>";
				echo "<td>{$user->group->name}</td>";
				echo "<td>{$user->group->description}</td>";
				echo "<td><a href='{$user->id}'>EDITAR</a></td>";
				echo "<td><a href='".route('user.delete',['id'=>$user->id])."'>ELIMINAR</a></td>";
				echo "</tr>";
			}
		?>
	</table>

</body>
</html>