<?php

namespace libs\Middle;

class File{

    public static function exists($path){
        return file_exists($path);
    }

    protected $path;
    protected $dir;
    protected $name;
    protected $ext;
    protected $mode;
    protected $file;
    protected $stats;

    public function __construct($path,$mode="r+"){
        $this->path=$path;
        $this->dir=dirname($path);
        $this->name=basename($path);
        $this->ext=pathinfo($path,PATHINFO_EXTENSION);
        $this->mode=$mode;
        $this->file=fopen($this->path,$this->mode);
        if(!$this->file){
            throw new \Exception("Error open file: ".$this->path);
        }
        $this->stats=fstat($this->file);
    }

    // Getters
    public function getPath(){
        return $this->path;
    }
    public function getDir(){
        return $this->dir;
    }
    public function getName(){
        return $this->name;
    }
    public function getExt(){
        return $this->ext;
    }
    public function getMode(){
        return $this->mode;
    }
    public function getFile(){
        return $this->file;
    }
    public function getStats(){
        return $this->stats;
    }

    public function read(){
        fseek($this->file,0);
        return fread($this->file,$this->stats['size']);
    }

    public function readLine(){
        return fgets($this->file);
    }

    public function write($data){
        ftruncate($this->file,0);
        fseek($this->file,0);
        return fwrite($this->file,$data);
    }

    public function append($data){
        fseek($this->file,0,SEEK_END);
        return fwrite($this->file,$data);
    }

    public function copy($path){
        return copy($this->path,$path);
    }

    public function close(){
        if($this->file){
            fclose($this->file);
        }
    }

}

?>