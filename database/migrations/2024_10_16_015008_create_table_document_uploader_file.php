<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('document_uploader_file',function($table){
			$table->id();
			$table->idForeign('id_document')->foreign('document','id');
			$table->idForeign('id_uploader_file')->foreign('uploader_file','id');
			$table->integer('message');
			$table->timestamps();
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('document_uploader_file');
	}
	
};

?>