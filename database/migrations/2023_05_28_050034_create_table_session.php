<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('session',function($table){
			$table->add('id','varchar',40)->primary();
			$table->idForeign('id_user')->foreign('user','id')->nullable();
			$table->add('payload','text');
			$table->add('expire_at','timestamp');
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('session');
	}
	
};
		
?>