<?php

namespace libs\View;

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
        "/@extends\((.*?)\)\s/m"=>"<?php
            \$_=[
                'value'=><<<'EOD'
                    $1
                EOD
            ];
            \$_['array']=explode(',',\$_['value'],2);
            \$_=[
                'path'=>(string)\$_['array'][0]??'',
                'params'=>\$_['array'][1]??'[]'
            ];
            \$_['params']=json_decode(str_replace(['[',']','=>'],['{','}',':'],\$_['params']),true)??[];
            view(\$_['path'],array_merge(\$_['params'],get_defined_vars()['params']??[])); unset(\$_); ?>",
        "/@include\((.*?)\)\s/m"=>"<?php view($1); ?>",
        "/@component\((.*?)\)\s/m"=>'
            <div wire:render=$1>
                <?php
                (new ("\\\\\\\\".str_replace("/","\\\\\\\\",config()->filesystem("components.path"))."\\\\\\\\".ucfirst($1))())->render();
                ?>
            </div>
        ',
        "/@componentNotRender\((.*?)\)\s/m"=>'
            <?php
            (new ("\\\\\\\\".str_replace("/","\\\\\\\\",config()->filesystem("components.path"))."\\\\\\\\".ucfirst($1))())->render();
            ?>
        ',
        "/@scriptsSPA/"=>"
            <script>var _token=\"<?php echo csrfToken(); ?>\"</script>
            <script src=\"<?php echo url('js/Fetch.js') ?>\"></script>
            <script src=\"<?php echo url('js/components/Wire.js') ?>\"></script>
            <script src=\"<?php echo url('js/components/Component.js') ?>\"></script>
            <script src=\"<?php echo url('js/components/SPA.js') ?>\"></script>
        ",
        "/@csrf/"=>"
            <input type=\"hidden\" name=\"_token\" value=\"<?php echo csrfToken(); ?>\">
        ",
        "/@(.*?)\)\s/m"=>"<?php if($1)){ ?>",
        "/@(.*?)\s/m"=>"<?php if($1()){ ?>"
    ];

    public $ext_views=["html","php"];

	public function make($path,$params=[],$once=false){
		if(!is_bool($once)){
			$once=false;
		}
		$path=str_replace(".","/",$path);
		$path=str_replace(['"',"'"," "],"",$path);
		foreach($this->ext_views as $value){
			$require_path=Config::filesystem('views.path')."/".$path.".".$value;
			if(file_exists($require_path)){
				foreach($params as $key=>$param){
					$$key=$param;
				}
				/*eval("?>".View::render($require_path)."<?php");*/
				if($once){
					require_once($this->render($require_path));
				}else{
					require($this->render($require_path));
				}
				return;
			}
		}
	}

	public function component($name){
		$name=str_replace(".","/",$name);
		foreach($this->ext_views as $value){
			$class=Config::filesystem('components.path')."/".$name;
			$class=str_replace("/","\\",$class);
			$component=new $class();
			return $component;
		}
	}

    public function render($path){
        $name=str_replace(["/","\\"],".",$path);
        $path_cache=Config::filesystem('views.cache')."/".$name;
        if(file_exists($path_cache) && filemtime($path_cache)>filemtime($path)){
            return $path_cache;
        }
        $handler=fopen($path,"r");
        $content=fread($handler,filesize($path));
        $content.=PHP_EOL;
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
        $m=fopen($path_cache,"w");
        fwrite($m,$content);
        fclose($m);
        return $path_cache;
    }

    private function getParentheses($text,$content){
        preg_match("/@".$text."\((.*?)\)\s/m",$content,$matches);
        return $matches[1]??"";
    }
    
}

?>