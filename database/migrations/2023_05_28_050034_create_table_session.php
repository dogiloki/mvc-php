<?php

use libs\DB\Migration;
use libs\DB\DB;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('session',function($table){
			$table->string('id',40)->primary();
			$table->idForeign('id_user')->foreign('user','id')->nullable();
			$table->string('ip_address');
			$table->string('user_agent');
			$table->text('payload');
			$table->timestamp('last_activity')->default(DB::flat('CURRENT_TIMESTAMP'));
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