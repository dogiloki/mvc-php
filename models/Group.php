<?php

namespace models;

use libs\DB\Model;

/**
 * @Table(group)
 */
class Group extends Model{

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

	/**
	 * @HasMany(User,id_group)
	 */
	public $users;

}

?>