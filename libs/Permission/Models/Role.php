<?php

namespace libs\Permission\Models;

use libs\DB\Model;
use app\Models\User;
use libs\Permission\Models\UserRole;

/**
 * @Table(role)
**/
class Role extends Model{

    protected $fillable=[
        "name",
        "description"
    ];

    public function users(){
        return $this->manyToMany(User::class,UserRole::class,'id_role','id_user');
    }

}

?>