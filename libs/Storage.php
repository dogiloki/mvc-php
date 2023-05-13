<?php

namespace libs;

require 'vendor/autoload.php';

use Intervention\Image\ImageManagerStatic as Image;
use libs\Middle\Secure;

class Storage{

    public static $compress_image=false;
    public static $compress_image_level=70;

    public static function upload($file){
        $dir=Config::singleton()->get('APP_STORAGE')."/";
        if($file==null){
            return null;
        }
        $name=$file['name'];
        $type=$file['type'];
        $size=$file['size'];
        try{       
            $name_temp=$file['tmp_name'];
            $name_exp=explode('.',$name);
            $name_exp=$name_exp[sizeof($name_exp)-1];
            $sha1=Secure::hash(uniqid(uniqid()));
            $folder=substr($sha1,0,2);
            if(!file_exists($dir)){
                mkdir($dir);
            }
            if(!file_exists($dir.$folder)){
                mkdir($dir.$folder);
            }
            $file_name=$sha1.".".$name_exp;
            $folder=$dir.$folder."/".$file_name;
            if(move_uploaded_file($name_temp,$folder)){
                if(self::$compress_image && explode("/",$type)[0]=="image"){
                    self::compressImage($folder);
                }
                return ["path"=>$file_name,"mime"=>$type];
            }else{
                return null;
            }
        }catch(\Exception $ex){
            echo $ex->getMessage();
        }
        return null;
    }

    public static function compressImage(string $path){
        $image=Image::make($path);
        $image->save($path,self::$compress_image_level);
    }

    public static function get($file){
        $dir=Config::singleton()->get('APP_STORAGE')."/";
        $sha1=substr($file,0,2);
        $folder=$dir.$sha1;
        $path=$folder."/".$file;
        if(file_exists($path)){
            header("Content-type: ".mime_content_type($path));
            readfile($path);
        }
    }
    
    public static function delete($file){
        $dir=Config::singleton()->get('APP_STORAGE')."/";
        $sha1=substr($file,0,2);
        $folder=$dir.$sha1;
        $path=$folder."/".$file;
        if($file!=null){
            unlink($path);
            return true;
        }else{
            return false;
        }
    }

}

?>
