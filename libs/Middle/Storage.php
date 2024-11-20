<?php

namespace libs\Middle;

use Intervention\Image\ImageManagerStatic as Image;
use libs\Middle\Secure;
use libd\Middle\Singleton;
use libs\Config;
use libs\Middle\Models\UploaderFile;

class Storage extends Singleton{

    public $compress_image=false;
    public $compress_image_level=70;
    public $encrypt=null;
    public $download=false;

    public function _upload($file,$disk){
        $dir=Config::filesystem('storage.'.$disk)."/";
        if($file==null){
            return null;
        }
        $name=$file['name'];
        $type=$file['type'];
        $size=$file['size'];
        try{
            if($this->_isEncrypt()){
                $content=Secure::encryptNotBase64(file_get_contents($file['tmp_name']),$this->encrypt());
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
            return null;
        }
        return null;
    }

    public function _compressImage(string $path){
        $image=Image::make($path);
        $image->save($path,self::$compress_image_level);
    }

    public function _getByHash(string $text){
        $uploader_file=UploaderFile::where('hash',$text)->first();
        if($uploader_file==null){
            abort(404);
        }
        $this->get($uploader_file->name(),$uploader_file->disk,$uploader_file->download_name,$uploader_file->mime);
    }

    public function _get($file,$disk,$download_name=null,$mime=null){
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
    
    public function _delete($file,$disk){
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

    public function _download(){
        $this->$download=true;
        return $this;
    }

    public function _encrypt($key=null){
        $this->encrypt=$key??Config::app('key');
        return $this;
    }

    public function _isEncrypt(){
        return $this->encrypt!=null;
    }


}

?>
