<?php

namespace libs\Permission\Models;

use libs\DB\Model;
use app\Models\User;
use libs\Permission\Models\Role;

/**
 * @Table(user_role)
 */
class UserRole extends Model{

    public function user(){
        return $this->hasOne(User::class,'id_user');
    }

    public function role(){
        return $this->hasOne(Role::class,'id_role');
    }

}

?>