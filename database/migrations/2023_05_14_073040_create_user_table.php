<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('user',function($table){
			$table->id();
			$table->add('name','varchar',255);
			$table->add('email','varchar',255);
			$table->add('verify_email_at','datetime')->nullable();
			$table->add('password','varchar',255);
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