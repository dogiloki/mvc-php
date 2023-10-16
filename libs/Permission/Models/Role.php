<?php

namespace libs\Permission\Models;

use libs\DB\Model;
use app\Models\User;
use libs\Permission\Models\UserRole;

/**
 * @Table(role)
**/
class Role extends Model{
    
    /**
	 * @ID(id)
	 */
	public $id;

    /**
     * @Column(name)
     */
    public $name;

    /**
     * @Column(description)
     */
    public $description;

    public function users(){
        return $this->manyToMany(User::class,UserRole::class,'id_role','id_user');
    }

}

?>