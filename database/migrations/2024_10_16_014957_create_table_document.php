<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('document',function($table){
			$table->id();
			$table->string('no_document');
			$table->string('reference');
			$table->string('sender');
			$table->timestamp('date_validated')->nullable();
			$table->timestamps();
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('document');
	}
	
};

?>