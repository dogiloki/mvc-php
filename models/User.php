<?php

namespace models;

use libs\DB\Model;

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
	 * @HasOne(Group,id)
	 */
	public $group;

}

?>