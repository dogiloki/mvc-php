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

}

?>