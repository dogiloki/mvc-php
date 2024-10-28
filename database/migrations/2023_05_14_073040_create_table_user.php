<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('user',function($table){
			$table->id();
			$table->string('name');
			$table->string('surname1');
			$table->string('surname2');
			$table->string('registration')->uniqued();
			$table->string('password');
			$table->timestamps();
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('user');
	}
	
};
		
?>