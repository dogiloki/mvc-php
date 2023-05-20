<?php

namespace app\Models;

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
	 * @Column(verify_email_at)
	 */
	public $verify_email_at;

	/**
	 * @Column(password)
	 */
	public $password;

}

?>