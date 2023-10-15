<?php

namespace app\Models;

use libs\DB\Model;
use libs\Auth\HasApiTokens;
use libs\Permission\HasRoles;

/**
 * @Table(user)
**/
class User extends Model{

	use HasApiTokens, HasRoles;

	protected $hidden=["password"];

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
	 * @Column(verify_email_at)
	 */
	public $verify_email_at;

	/**
	 * @Column(password)
	 */
	public $password;

}

?>