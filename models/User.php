<?php

namespace models;

use libs\DB;
use libs\Model;

/**
 * @Table(user)
**/
class User extends Model{

	public $table="user";
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

}

?>