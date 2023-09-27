<?php

namespace libs\Auth;

use libs\Middle\Secure;

trait HasApiTokens{
    
    public function createToken($name){
        $token=new AccessToken();
        $token->id_tokenable=$this->id;
        $token->type_tokenable=$this->classType();
        $token->name=$name;
        $token->token=$this->generateToken();
        $token->abilities=json_encode(["*"]);
        $token->last_activity=date('Y-m-d H:i:s');
        $token->expire_at=date('Y-m-d H:i:s',strtotime('+1 year'));
        $token->save();
        return $token;
    }

    public function generateToken(){
        return Secure::hash();
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

    public function tokenable(){
        $class=$this->type_tokenable;
        return $class::find($this->id_tokenable);
    }

    public function tokens(){
        return AccessToken::find(function($find){
            $find->where('id_tokenable',$this->id)->and();
            $find->where('type_tokenable',$this->classType());
        },[]);
    }

    public function token($name){
        return AccessToken::find(function($find)use($name){
            $find->where('id_tokenable',$this->id)->and();
            $find->where('type_tokenable',$this->classType())->and();
            $find->where('name',$name);
        });
    }
    
    public function deleteToken($name){
        $token=$this->token($name);
        if($token){
            $token->delete();
        }
    }

    public function deleteTokens(){
        $tokens=$this->tokens();
        foreach($tokens as $token){
            $token->delete();
        }
    }

    public function hasToken($name){
        return $this->token($name)?true:false;
    }

    public function hasTokens(){
        return $this->tokens()?true:false;
    }

    public function tokenCan($name,$ability){
        $token=$this->token($name);
        if($token){
            $abilities=json_decode($token->abilities);
            if(in_array($ability,$abilities)){
                return true;
            }
        }
        return false;
    }

    public function tokenCant($name,$ability){
        return !$this->tokenCan($name,$ability);
    }

    public function tokenAbilities($name){
        $token=$this->token($name);
        if($token){
            return json_decode($token->abilities);
        }
        return [];
    }

}

?>