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
		"size",
		"mime",
		"original_name",
		"download_name"
	];
	protected $with_methods=['path'];

	public function name(){
		return $this->hash.".".$this->ext;
	}

	public function path(){
		return $this->folder."/".$this->name();
	}
    
}

?>