<?php

namespace libs\Middle\Models;

use libs\DB\Model;

class UploaderFile extends Model{

    protected $table='uploader_file';
    protected $fillable=[
		"disk",
		"folder",
		"hash",
		"ext",
		"mime",
		"original_name",
		"download_name"
	];

	public function name(){
		return $this->hash.".".$this->ext;
	}
    
}

?>