<?php

namespace libs\Permission;

use libs\Permission\Models\Role;
use libs\Permission\Models\UserRole;
use libs\Permission\Models\Permission;
use libs\Permission\Models\UserPermission;

trait HasRoles{
	
	public function roles(){
		return $this->manyToMany(Role::class,UserRole::class,'id_user','id_role');
	}

	public function permissions(){
		return $this->manyToMany(Permission::class,UserPermission::class,'id_user','id_permission');
	}

    public function assignRole($names){
		$names=is_array($names)?$names:func_get_args();
		$rs=new Role();
		foreach($names as $index=>$name){
			if($index==0){
				$rs=Role::where('name',$name);
			}else{
				$rs->or();
				$rs->where('name',$name);
			}
		}
		$ids=array_column($rs->rows(),Role::getPrimaryKey());
		if(count($ids)<1){
			return false;
		}
		return $this->roles()->sync($ids);
    }

	public function assignPermission($names){
		$names=is_array($names)?$names:func_get_args();
		$rs=new Permission();
		foreach($names as $index=>$name){
			if($index==0){
				$rs=Permission::where('name',$name);
			}else{
				$rs->or();
				$rs->where('name',$name);
			}
		}
		$ids=array_column($rs->rows(),Permission::getPrimaryKey());
		if(count($ids)<1){
			return false;
		}
		return $this->permissions()->sync($ids);
    }

	public function is($name_role){
		$role=Role::where('name',$name_role)->row();
		if($role==null){
			return false;
		}
		return $this->roles()->exists($role[Role::getPrimaryKey()]);
	}

	public function can($name_permission){
		$permission=Permission::where('name',$name_permission)->row();
		if($permission==null){
			return false;
		}
		return $this->permissions()->exists($permission[Permission::getPrimaryKey()]);
	}

}

?>