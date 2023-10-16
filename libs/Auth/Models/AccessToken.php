<?php

namespace libs\Auth\Models;

use libs\DB\Model;

/**
 * @Table(access_token)
 * 
**/
class AccessToken extends Model{

    /**
     * @ID(id)
     */
    public $id;

    /**
     * @Column(id_tokenable)
     */
    public $id_tokenable;

    /**
     * @Column(type_tokenable)
     */
    public $type_tokenable;

    /**
     * @Column(name)
     */
    public $name;

    /**
     * @Column(token)
     */
    public $token;

    /**
     * @Column(abilities)
     */
    public $abilities;

    /**
     * @Column(ip_address)
     */
    public $ip_address;

    /**
     * @Column(user_agent)
     */
    public $user_agent;

    /**
     * @Column(last_activity)
     */
    public $last_activity;

    /**
     * @Column(expire_at)
     */
    public $expire_at;

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