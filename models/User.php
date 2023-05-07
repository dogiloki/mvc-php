<?php

namespace models;

use libs\Model;

/**
 * @Table(user)
**/
class User extends Model{

	/**
	 * @ID(id)
	 */
	public $id;
	/**
	 * @Column(name)
	 */
	public $name;
	/**
	 * @Column(email)
	 */
	public $email;
	/**
	 * @Column(password)
	 */
	public $password;
	/**
	 * @Column(id_group)
	 * @Reference(Group,id)
	 */
	public $group;

}

?>