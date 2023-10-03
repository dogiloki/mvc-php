<?php

namespace libs\Console;

use libs\Console\Console;

class Manager{

    private static $instance=null;

    public static function singleton(){
        if(self::$instance==null){
            self::$instance=new Manager();
        }
        return self::$instance;
    }

    public static function command($line,$action){
        $manager=self::singleton();
        $command=(new Command($line,$action));
        $manager->commands[]=$command;
        return $command;
    }

    public static function call($args){
        $manager=self::singleton();
        $array_text=explode(" ",implode(" ",$args));
        array_splice($array_text,0,1);
        $args=[];
        foreach($manager->commands as $command){
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
        (new Console())->error("No existe la instrucción");
    }

    private $commands=[];

    private function __construct(){
        
    }

}

?>