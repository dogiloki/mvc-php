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
		"email",
		"verified_email_at",
		"password"
	];

	public function full(){
		return "Hola, ".$this->name;
	}

}

?>