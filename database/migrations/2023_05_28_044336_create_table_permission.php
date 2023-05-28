<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('permission',function($table){
			$table->id();
			$table->add('code','varchar')->unique();
			$table->add('name','varchar');
			$table->timestamps();
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('permission');
	}
	
};
		
?>