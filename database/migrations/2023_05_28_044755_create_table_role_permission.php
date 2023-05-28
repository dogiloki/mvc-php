<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('role_permission',function($table){
			$table->id();
			$table->idForeign('id_role')->foreign('role','id');
			$table->idForeign('id_permission')->foreign('permission','id');
			$table->timestamps();
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('role_permission');
	}
	
};
		
?>