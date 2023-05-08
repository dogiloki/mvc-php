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
	 * @OneToOne(Group,id)
	 */
	public $group;

}

?>