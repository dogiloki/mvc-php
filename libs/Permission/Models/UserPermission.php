<?php

namespace libs\Permission\Models;

use libs\DB\Model;
use app\Models\User;
use libs\Permission\Models\Permission;

/**
 * @Table(user_permission)
**/
class UserPermission extends Model{
    
    /**
	 * @ID(id)
	 */
	public $id;

    /**
     * @Column(id_user)
     */
    public $id_user;

    /**
     * @Column(id_permission)
     */
    public $id_permission;

    public function user(){
        return $this->hasOne(User::class,'id_user');
    }

    public function permission(){
        return $this->hasOne(Permission::class,'id_permission');
    }

}

?>