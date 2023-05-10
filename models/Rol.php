<?php

namespace models;

use libs\DB\Model;

/**
 * @Table(rol)
 */
class Rol extends Model{

	/**
	 * @ID(id)
	 */
	public $id;

	/**
	 * @Column(name)
	 */
	public $name;

	/**
	 * @Column(description)
	 */
	public $description;

}

?>