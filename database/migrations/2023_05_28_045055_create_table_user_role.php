<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('user_role',function($table){
			$table->id();
			$table->idForeign('id_user')->foreign('user','id');
			$table->idForeign('id_role')->foreign('role','id');
			$table->timestamps();
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('user_role');
	}
	
};
		
?>