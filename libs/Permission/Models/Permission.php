<?php

namespace libs\Permission\Models;

use libs\DB\Model;
use app\Models\User;
use libs\Permission\Models\UserPermission;

/**
 * @Table(permission)
**/
class Permission extends Model{

    protected $fillable=[
        "name",
        "description"
    ];

    public function users(){
        return $this->manyToMany(User::class,UserPermission::class,'id_permission','id_user');
    }

}

?>