<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('role',function($table){
			$table->id();
			$table->add('name','varchar')->unique();
			$table->add('description','text');
			$table->timestamps();
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('role');
	}
	
};
		
?>