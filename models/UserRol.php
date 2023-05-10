<?php

namespace models;

use libs\DB\Model;

/**
 * @Table(user_rol)
 */
class UserRol extends Model{

	/**
	 * @ID(id)
	 */
	public $id;

	/**
	 * @Column(id_user)
	 */
	public $id_user;

	/**
	 * @Column(id_rol)
	 */
	public $id_rol;

}

?>