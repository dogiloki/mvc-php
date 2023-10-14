<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('user_permission',function($table){
			$table->id();
			$table->idForeign('id_user')->foreign('user','id');
			$table->idForeign('id_permission')->foreign('permission','id');
			$table->timestamps();
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('user_permission');
	}
	
};
		
?>