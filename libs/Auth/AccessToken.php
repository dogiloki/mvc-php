<?php

namespace libs\Auth;

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
     * @Column(last_activity)
     */
    public $last_activity;

    /**
     * @Column(expire_at)
     */
    public $expire_at;

}

?>