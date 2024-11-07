<?php

namespace libs\Middle;

use Intervention\Image\ImageManagerStatic as Image;
use libs\Middle\Secure;
use libs\Config;
use libs\Middle\Models\UploaderFile;

class Storage{

    public static $compress_image=false;
    public static $compress_image_level=70;
    public static $encrypt=null;
    public static $download=false;

    public static function encrypt($key=null){
        self::$encrypt=$key??Config::app('key');
    }

    public static function upload($file,$disk){
        $dir=Config::filesystem('storage.'.$disk)."/";
        if($file==null){
            return null;
        }
        $name=$file['name'];
        $type=$file['type'];
        $size=$file['size'];
        try{
            if(self::$encrypt!=null){
                $content=Secure::encryptNotBase64(file_get_contents($file['tmp_name']),self::$encrypt);
                file_put_contents($file['tmp_name'],$content);
            }
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
                self::$encrypt=null;
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
                self::$encrypt=null;
                return null;
            }
        }catch(\Exception $ex){
            return null;
        }
        self::$encrypt=null;
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
        self::get($uploader_file->name(),$uploader_file->disk,$uploader_file->download_name,$uploader_file->mime);
    }

    public static function download(){
        self::$download=true;
    }

    public static function get($file,$disk,$download_name=null,$mime=null){
        $dir=Config::filesystem('storage.'.$disk)."/";
        $sha1=substr($file,0,2);
        $folder=$dir.$sha1;
        $path=$folder."/".$file;
        if(file_exists($path)){
            ob_clean();
            header("Content-type: ".(self::$download?"application/octet-stream":($mime??mime_content_type($path))));
            header("Content-Disposition: filename=\"".$download_name."\"");
            if(self::$encrypt!=null){
                $content=Secure::decryptNotBase64(file_get_contents($path),self::$encrypt);
            }else{
                $content=file_get_contents($path);
            }
            echo $content;
        }else{
            abort(404);
        }
        self::$encrypt=null;
        self::$download=false;
    }

    public static function deleteHash($text){
        $uploader_file=UploaderFile::where('hash',$text)->first();
        if($uploader_file==null){
            self::$encrypt=null;
            return false;
        }
        $uploader_file->delete();
        self::$encrypt=null;
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
            self::$encrypt=null;
            return true;
        }else{
            self::$encrypt=null;
            return false;
        }
    }

}

?>
