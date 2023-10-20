<?php

namespace libs\Auth\Models;

use libs\DB\Model;

/**
 * @Table(access_token)
 * 
**/
class AccessToken extends Model{

    public function tokenable(){
        $class=$this->type_tokenable;
        return $class::find($this->id_tokenable);
    }

    public function hasExpired(){
        return strtotime($this->expire_at)<time();
    }

    public function can($ability){
        $abilities=json_decode($this->abilities);
        if($abilities){
            foreach($abilities as $a){
                if($a=="*"){
                    return true;
                }
                if($a==$ability){
                    return true;
                }
            }
        }
        return false;
    }

}

?>