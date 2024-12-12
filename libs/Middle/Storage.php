<?php

namespace libs\Middle;

use Intervention\Image\ImageManagerStatic as Image;
use libs\Middle\Secure;
use libs\Middle\Singleton;
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
                $content=Secure::encryptNotBase64(file_get_contents($file['tmp_name']),$this->encrypt);
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
                if($this->compress_image && explode("/",$type)[0]=="image"){
                    $this->compressImage($folder);
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
        $image->save($path,$this->compress_image_level);
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
            if(ob_get_level()>0 && ob_get_length()>0){
                ob_clean();
            }
            header("Content-type: ".($this->download?"application/octet-stream":($mime??mime_content_type($path))));
            header("Content-Disposition: filename=\"".$download_name."\"");
            if($this->isEncrypt()){
                $content=Secure::decryptNotBase64(file_get_contents($path),$this->encrypt);
            }else{
                $content=file_get_contents($path);
            }
            echo $content;
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

    public function compressImage($active,$level=null){
        $this->compress_image=$active;
        $this->compress_image_level=$level??$this->compress_image_level;
        return $this;
    }

    public function _download(){
        $this->download=true;
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
