<?php

namespace Libs\Middle;

use Intervention\Image\ImageManagerStatic as Image;
use libs\Middle\Secure;
use libs\Middle\Singleton;
use libs\Config;
use libs\Middle\Models\UploaderFile;
use libs\Middle\Log;

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
            $name_ext=pathinfo($name,PATHINFO_EXTENSION);
            $sha1=Secure::hash();
            $folder=substr($sha1,0,2);

            if(!file_exists($dir)){
                mkdir($dir,0700,true);
            }
            if(!file_exists($dir.$folder)){
                mkdir($dir.$folder,0700,true);
            }
            
            $file_name=$sha1.".".$name_ext;
            $folder_save=$dir.$folder."/".$sha1;
            $file_path=$dir.$folder."/".$file_name;

            if($this->_isEncrypt()){
                // Crear archivo temporal para encriptar
                $temp_encrypt=tempnam(sys_get_temp_dir(),'encrypt_');
                // Encriptar el archivo a partir del archivo temporal
                $res=Secure::encryptFileStream($file['tmp_name'], $temp_encrypt, $this->encrypt);
                
                if(!$res){
                    unlink($temp_encrypt);
                    return null;
                }

                // Mover el archivo encriptado temporal a la carpeta de destino
                if(!rename($temp_encrypt,$file_path)){
                    unlink($temp_encrypt);
                    return null;
                }

                // Borrar el archivo temporal original
                unlink($file['tmp_name']);
                Log::info("File uploaded con encriptación: ".$file_path);
            }else{
                // Mover el archivo si no se encripta
                if(!move_uploaded_file($file['tmp_name'], $file_path)){
                    return null;
                }
                if($this->compress_image && explode("/",$type)[0]=="image"){
                    $this->compressImage($file_path);
                }
                Log::info("File uploaded sin encriptar: ".$file_path);
            }

            return UploaderFile::create([
                "disk"=>$disk,
                "folder"=>$folder_save,
                "hash"=>$sha1,
                "ext"=>$name_ext,
                "mime"=>$type,
                "size"=>$size,
                "original_name"=>$name,
                "download_name"=>$name
            ]);
        }catch(\Exception $ex){
            return null;
        }
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
            header("Content-Disposition: attachment; filename=\"".$download_name."\"");
            
            if($this->isEncrypt()){
                Secure::decryptFileStream($path,'php://output',$this->encrypt);
                Log::info("File downloaded con encriptación: ".$path);
            }else{
                readfile($path);
                Log::info("File downloaded sin encriptación: ".$path);
            }
            exit;
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
