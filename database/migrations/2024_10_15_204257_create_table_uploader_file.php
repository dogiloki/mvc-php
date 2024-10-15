<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('uploader_file',function($table){
			$table->id();
			$table->text('disk');
			$table->text('folder');
			$table->text('hash')->unique();
			$table->text('ext');
			$table->text('mime');
			$table->text('original_name');
			$table->text('download_name');
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