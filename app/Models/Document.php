<?php

namespace app\Models;

use libs\DB\Model;
use app\Models\DocumentUploaderFile;
use libs\Middle\Models\UploaderFile;

class Document extends Model{

    protected $table="document";
    protected $fillable=[
        "no_document",
        "reference",
        "sender",
        "date_validated"
    ];

    public function uploaderFile(){
        return $this->manyToMany(UploaderFile::class,DocumentUploaderFile::class,'id_document','id_uploader_file');
    }

}

?>