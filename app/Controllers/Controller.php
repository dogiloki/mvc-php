<?php

namespace app\Controllers;

use libs\HTTP\Response;

class Controller{
    
    public static function statusText($code){
        if(is_numeric($code)){
            return Response::statusText($code);
        }else{
            return Response::statusText($code->code);
        }
    }

    private $code=200;
    private $status="success";
    private $data=null;
    private $message="";
    private $errors=[];
    private $meta=null;

    public function response($code=null){
        $this->code=$code??$this->code;
        return json([
            'status'=>$this->status,
            'code'=>$this->code,
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

    public function setStatus($status){ // success, error, warning, info
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