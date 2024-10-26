<?php

namespace app\Models;

use libs\DB\Model;

class DocumentUploaderFile extends Model{
    
    protected $table="document_uploader_file";
    protected $fillable=[
        "message"
    ];

}

?>