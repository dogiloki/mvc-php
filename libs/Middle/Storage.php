<?php

namespace libs\Middle;

use Intervention\Image\ImageManagerStatic as Image;
use libs\Middle\Secure;
use libs\Config;
use libs\Middle\Models\UploaderFile;

class Storage{

    public static $compress_image=false;
    public static $compress_image_level=70;

    public static function upload($file,$disk){
        $dir=Config::filesystem('storage.'.$disk)."/";
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
            $sha1=Secure::hash();
            $folder=substr($sha1,0,2);
            if(!file_exists($dir)){
                mkdir($dir);
            }
            if(!file_exists($dir.$folder)){
                mkdir($dir.$folder);
            }
            $file_name=$sha1.".".$name_exp;
            $folder_save=$dir.$folder."/".$sha1;
            $folder=$dir.$folder."/".$file_name;
            if(move_uploaded_file($name_temp,$folder)){
                if(self::$compress_image && explode("/",$type)[0]=="image"){
                    self::compressImage($folder);
                }
                return UploaderFile::create([
                    "disk"=>$disk,
                    "folder"=>$folder_save,
                    "hash"=>$sha1,
                    "ext"=>$name_exp,
                    "mime"=>$type,
                    "original_name"=>$name,
                    "download_name"=>$name
                ]);
            }else{
                return null;
            }
        }catch(\Exception $ex){
            
        }
        return null;
    }

    public static function compressImage(string $path){
        $image=Image::make($path);
        $image->save($path,self::$compress_image_level);
    }

    public static function getHash($text){
        $uploader_file=UploaderFile::where('hash',$text)->first();
        if($uploader_file==null){
            abort(404);
        }
        self::get($uploader_file->name(),$uploader_file->disk,$uploader_file->download_name);
    }

    public static function get($file,$disk,$download_name=null){
        $dir=Config::filesystem('storage.'.$disk)."/";
        $sha1=substr($file,0,2);
        $folder=$dir.$sha1;
        $path=$folder."/".$file;
        if(file_exists($path)){
            ob_clean();
            header("Content-type: ".mime_content_type($path));
            header("Content-Disposition: filename=\"".$download_name."\"");
            readfile($path);
        }else{
            abort(404);
        }
    }

    public static function deleteHash($text){
        $uploader_file=UploaderFile::where('hash',$text)->first();
        if($uploader_file==null){
            return false;
        }
        $uploader_file->delete();
        return self::delete($uploader_file->name(),$uploader_file->disk);
    }
    
    public static function delete($file,$disk){
        $dir=Config::filesystem('storage.'.$disk)."/";
        $sha1=substr($file,0,2);
        $folder=$dir.$sha1;
        $path=$folder."/".$file;
        if($file!=null){
            if(file_exists($path)){
                unlink($path);
            }
            return true;
        }else{
            return false;
        }
    }

}

?>
