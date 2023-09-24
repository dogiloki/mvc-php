<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('access_token',function($table){
			$table->id();
			$table->idForeign('id_user')->foreign('user','id');
			$table->add('token','varchar')->unique();
			$table->add('expire_at','timestamp');
			$table->timestamps();
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('access_token');
	}
	
};
		
?>