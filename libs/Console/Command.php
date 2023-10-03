<?php

namespace libs\Console;

use libs\Config;
use libs\Console\Console;

class Command{

    public $line;
    public $description;
    public $action;
    public $command;
    public $arguments=[];

    public function __construct($line,$action){
        $this->line=$line;
        $this->action=$action;
        $this->generateArguments();
    }

    private function generateArguments(){
        $path=explode(" ",$this->line);
        $this->command=[];
        foreach($path as $key=>$value){
            if(preg_match('/{.*?}/',$value,$param)){
                $name=str_replace(["{","}","?"],"",$param[0]);
                $this->arguments[$key]=[
                    'name'=>$name,
                    'optional'=>substr($param[0],-2,1)=="?"?true:false
                ];
            }else{
                $this->command[]=$value;
            }
        }
        $this->command=implode(" ",$this->command);
    }

    public function describe($text){
        $this->description=$text;
    }

    public function run(...$args){
        return ($this->action)(new Console(),...$args);
    }

}

?>