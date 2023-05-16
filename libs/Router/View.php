<?php

namespace libs\Router;

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

    static $patterns=[
        "/@php/"=>"<?php",
        "/@endphp/"=>"?>",
        "/{{(.*?)}}/"=>"<?php echo $1; ?>",
        "/{-{(.*?)}}/"=>"$1",
        "/@else/"=>"<?php }else{ ?>",
        "/@default/"=>"<?php default: ?>",
        "/@break/"=>"<?php break; ?>",
        "/@continue/"=>"<?php continue; ?>"
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
            $patterns["/@end".$statement."/"]="<?php } ?>";
        }
        // Statements with parentheses
        $content=preg_replace(array_keys($patterns),array_values($patterns),$content);
        // Statements without parentheses
        $content=preg_replace(array_keys(View::$patterns),array_values(View::$patterns),$content);

        fclose($handler);

        $m=fopen("storage/framework/views/".str_replace(["/","\\"],".",$path),"w");
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