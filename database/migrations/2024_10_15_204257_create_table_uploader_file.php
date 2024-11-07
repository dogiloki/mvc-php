<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('uploader_file',function($table){
			$table->id();
			$table->string('disk');
			$table->string('folder');
			$table->string('hash')->unique();
			$table->string('ext');
			$table->string('mime');
			$table->string('original_name');
			$table->string('download_name');
			$table->timestamps();
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('uploader_file');
	}
	
};

?>