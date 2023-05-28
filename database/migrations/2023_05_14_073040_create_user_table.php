<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('user',function($table){
			$table->id();
			$table->add('name','varchar');
			$table->add('email','varchar');
			$table->add('verify_email_at','datetime')->nullable();
			$table->add('password','varchar');
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