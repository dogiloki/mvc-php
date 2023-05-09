<?php

namespace models;

use libs\Model;

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
	 * @OneToMany(User,id)
	 */
	public $users;

}

?>