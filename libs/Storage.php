<?php


namespace libs;

if(!extension_loaded("imagick")){
    \dl("php_imagick.".PHP_SHLIB_SUFFIX);
}

class Storage{

    public static $dir="storage/";

    public static function upload($file){
        if($file==null){
            return null;
        }
        $name=$file['name'];
        $type=$file['type'];
        $size=$file['size'];
        if($size<20971520) {
            $name_temp=$file['tmp_name'];
            $name_exp=explode('.',$name);
            $name_exp=$name_exp[sizeof($name_exp)-1];
            $file_name=uniqid().".".$name_exp;
            $carpeta=Storage::$dir.$file_name;
            if(self::compression($name_temp,$carpeta)){
                return ["path"=>$file_name,"type"=>$type];
            }else{
                return null;
            }
        }else{
            return null;
        }
    }

    public static function get($file){
        if($file==null){
            return null;
        }
        $type=$file['type'];
        header("Content-type: ".$type);
	    readfile(Storage::$dir.$file);
    }

    public static function compression($path_source,$path_destiny,$quality=25){
        $img=new \Imagick($path_source);
        if($img->setImageCompressionQuality(25)){
            return $img->writeImage($path_destiny,true);
        }
        return false;
    }
    
    public static function delete($name){
        if($name!=null){
            unlink(Storage::$dir.$name);
            return true;
        }else{
            return false;
        }
    }

}

?>
