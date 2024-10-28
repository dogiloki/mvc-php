<?php

namespace app\Models;

use libs\DB\Model;
use libs\Auth\HasApiTokens;
use libs\Permission\HasRoles;

class User extends Model{
	use HasApiTokens, HasRoles;

	protected $table="user";
	protected $hidden=["password"];
	protected $fillable=[
		"name",
		"surname1",
		"surname2",
		"registration",
		"password"
	];

	public function fullName(){
		return $this->name." ".$this->surname1." ".$this->surname2;
	}

}

?>