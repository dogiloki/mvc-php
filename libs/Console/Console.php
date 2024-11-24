<?php

namespace libs\Console;

use libs\Middle\Singleton;

class Console extends Singleton{

    public function _ask($text){
        return readline($text);
    }

    public function _error($text){
        print("\033[31m".$text."\033[0m")."\n";
        return $this;
    }

    public function _warning($text){
        print("\033[33m".$text."\033[0m")."\n";
        return $this;
    }

    public function _success($text){
        print("\033[32m".$text."\033[0m")."\n";
        return $this;
    }

    public function _info($text){
        print($text."\n");
        return $this;
    }

    public function _dd($text){
        print_r($text."\n");
        return $this;
    }

    public function _exit($code=null){
        exit($code);
    }

}

?>