<?php

namespace libs\Console;

use libs\Console\Console;
use libs\Middle\Singleton;

class Manager extends Singleton{

    private $commands=[];

    public function _command($line,$action){
        $command=(new Command($line,$action));
        $this->commands[]=$command;
        return $command;
    }

    public function _call($args){
        $array_text=explode(" ",implode(" ",$args));
        array_splice($array_text,0,1);
        $args=[];
        foreach($this->commands as $command){
            $array_command=explode(" ",$command->command);
            foreach($array_command as $key=>$text_command){
                if(($array_text[$key]??null)!=$text_command){
                    continue 2;
                }
            }
            foreach($command->arguments as $key=>$argument){
                $args[$argument['name']]=$array_text[$key]??null;
                if(!isset($array_text[$key]) && !$argument['optional']){
                    continue 2;
                }
            }
            $command->run(...$args);
            return;
        }
        Console::error("No existe la instrucción");
    }

}

?>