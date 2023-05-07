<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Usuarios</title>
	<link rel="stylesheet" type="text/css" href="<?php urlPublic('header.css') ?>">
</head>
<body>

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
		<tr>
			<?php
				foreach($users as $user){
					echo "<td>{$user->name}</td>";
					echo "<td>{$user->email}</td>";
					echo "<td>{$user->group->name}</td>";
					echo "<td>{$user->group->description}</td>";
				}
			?>
			<td><a href="">EDITAR</a></td>
			<td><a href="">ELIMINAR</a></td>
		</tr>
	</table>

</body>
</html>