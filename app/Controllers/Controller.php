<?php

namespace app\Controllers;

class Controller{
    
    private $code=200;
    private $status="success";
    private $data=null;
    private $message="";
    private $errors=[];
    private $meta=null;

    public function response(){
        return json([
            'status'=>$this->status,
            'data'=>$this->data,
            'message'=>$this->message,
            'errors'=>$this->errors,
            'meta'=>$this->meta
        ],$this->code);
    }

    public function setCode($code){
        $this->code=$code;
        return $this;
    }

    public function setStatus($status){
        $this->status=$status;
        return $this;
    }

    public function setData($data){
        $this->data=$data;
        return $this;
    }

    public function setMessage($message){
        $this->message=$message;
        return $this;
    }

    public function setErrors($errors){
        $this->errors=$errors;
        return $this;
    }

    public function setMeta($meta){
        $this->meta=$meta;
        return $this;
    }

}

?>