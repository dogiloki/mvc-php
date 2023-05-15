<?php

namespace libs;

class View{

    static $statementsWithParentheses=[
        "elseif",
        "if",
        "for",
        "foreach",
        "while",
        "switch",
        "case"
    ];

    public static function render($path){
        $handler=fopen($path,"r");
        $content=fread($handler,filesize($path));
        // Statements with parentheses
        foreach(View::$statementsWithParentheses as $statement){
            if($statement=="elseif"){
                $content=preg_replace("/@".$statement."\((.*?)\)\s/m","<?php }else if($1){ ?>",$content);
            }else{
                $content=preg_replace("/@".$statement."\((.*?)\)\s/m","<?php ".$statement."($1){ ?>",$content);
            }
            $content=preg_replace("/@end".$statement."/","<?php } ?>",$content);
        }
        // Tags php
        $content=preg_replace("/@php/","<?php",$content);
        $content=preg_replace("/@endphp/","?>",$content);
        // Variables to php
        $content=preg_replace("/{{(.*?)}}/","<?php echo $1; ?>",$content);
        // Params to php
        $content=preg_replace("/{-{(.*?)}}/","$1",$content);
        // If to php
        $content=preg_replace("/@else/","<?php }else{ ?>",$content);
        // Switch to php
        $content=preg_replace("/@default/","<?php default: ?>",$content);
        $content=preg_replace("/@break/","<?php break; ?>",$content);
        $content=preg_replace("/@continue/","<?php continue; ?>",$content);

        fclose($handler);

        $m=fopen("storage/logs/view.php","w");
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