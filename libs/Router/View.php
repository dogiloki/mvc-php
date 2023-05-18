<?php

namespace libs\Router;

use libs\Config;

class View{

    static $statementsWithParentheses=[
        "elseif",
        "if",
        "for",
        "foreach",
        "while",
        "switch",
        "case",
        "try",
        "catch",
        "finally",
        "function"
    ];

    static $patterns=[
        "/@php/"=>"<?php",
        "/@endphp/"=>"?>",
        "/{{(.*?)}}/"=>"<?php echo $1; ?>",
        "/@else/"=>"<?php }else{ ?>",
        "/@default/"=>"<?php default: ?>",
        "/@break/"=>"<?php break; ?>",
        "/@continue/"=>"<?php continue; ?>",
        "/@end(.*?)\s/m"=>"<?php } ?>",
        "/@(.*?)\)\s/m"=>"<?php if($1)){ ?>",
        "/@(.*?)\s/m"=>"<?php if($1()){ ?>"
    ];

    public static function render($path){
        $handler=fopen($path,"r");
        $content=fread($handler,filesize($path));
        $patterns=[];
        // Constructs statements with parentheses
        foreach(View::$statementsWithParentheses as $statement){
            if($statement=="elseif"){
                $patterns["/@".$statement."\((.*?)\)\s/m"]="<?php }else if($1){ ?>";
            }else{
                $patterns["/@".$statement."\((.*?)\)\s/m"]="<?php ".$statement."($1){ ?>";
            }
            $patterns["/@end".$statement."\s/m"]="<?php } ?>";
        }
        // Statements with parentheses
        $content=preg_replace(array_keys($patterns),array_values($patterns),$content);
        // Statements without parentheses
        $content=preg_replace(array_keys(View::$patterns),array_values(View::$patterns),$content);

        fclose($handler);

        $name=str_replace(["/","\\"],".",$path);
        $m=fopen(Config::filesystem('views.cache')."/".$name,"w");
        fwrite($m,$content);
        fclose($m);

        return $content;
    }

    static function getParentheses($text, $content){
        preg_match("/@".$text."\((.*?)\)\s/m",$content,$matches);
        return $matches[1]??"";
    }
    
}

?>