<?php

namespace libs;

class View{

    public static function render($path){
        $handler=fopen($path,"r");
        $content=fread($handler,filesize($path));
        // Tags php
        $content=preg_replace("/@php/","<?php",$content);
        $content=preg_replace("/@endphp/","?>",$content);
        // Variables to php
        $content=preg_replace("/{{(.*?)}}/","<?php echo $1; ?>",$content);
        // Params to php
        $content=preg_replace("/{-{(.*?)}}/","$1",$content);
        // If to php
        $content=View::processNestings("if",$content);
        $content=preg_replace("/@elseif\((.*?)\)/","<?php }else if($1){ ?>",$content);
        $content=preg_replace("/@else/","<?php }else{ ?>",$content);
        $content=preg_replace("/@endif/","<?php } ?>",$content);
        // For to php
        $content=preg_replace("/@for\((.*?)\)/","<?php for($1){ ?>",$content);
        $content=preg_replace("/@endfor/","<?php } ?>",$content);
        // Foreach to php
        $content=preg_replace("/@foreach\((.*?)\)/","<?php foreach($1){ ?>",$content);
        $content=preg_replace("/@endforeach/","<?php } ?>",$content);
        // While to php
        $content=preg_replace("/@while\((.*?)\)/","<?php while($1){ ?>",$content);
        $content=preg_replace("/@endwhile/","<?php } ?>",$content);
        // Switch to php
        $content=preg_replace("/@switch\((.*?)\)/","<?php switch($1){ ?>",$content);
        $content=preg_replace("/@case\((.*?)\)/","<?php case $1: ?>",$content);
        $content=preg_replace("/@default/","<?php default: ?>",$content);
        $content=preg_replace("/@break/","<?php break; ?>",$content);
        $content=preg_replace("/@endswitch/","<?php } ?>",$content);

        fclose($handler);

        $m=fopen("storage/logs/view.php","w");
        fwrite($m,$content);
        fclose($m);

        return $content;
    }

    static function processNestings($text, $content){
        while(preg_match("/@".$text."\((.*?)\)/",$content,$matches)){
            $nesting=$matches[0];
            $condition=$matches[1];
            $tag="<?php if($condition){ ?>";
            $content=str_replace($nesting,$tag,$content);
            //$content=View::processNestings($text,$content);
        }
        return $content;
    }
    
}

?>