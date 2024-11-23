<?php

namespace libs\System;

class Process{

    public $command;
    public $process;
    public $output;
    public $return;

    public function __construct($command){
        $this->command=$command;
    }

    public function start(){
        $this->process=exec($this->command,$this->output,$this->return);
        return $this;
    }

    public function output(){
        return $this->output;
    }

    public function return(){
        return $this->return;
    }

}

?>