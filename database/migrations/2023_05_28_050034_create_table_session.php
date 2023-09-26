<?php

use libs\DB\Migration;
use libs\DB\DB;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('session',function($table){
			$table->add('id','varchar',40)->primary();
			$table->idForeign('id_user')->foreign('user','id')->nullable();
			$table->add('ip_address','varchar',45);
			$table->add('user_agent','varchar',255);
			$table->add('payload','text');
			$table->add('last_activity','timestamp')->default(DB::flat('CURRENT_TIMESTAMP'));
			$table->add('expire_at','timestamp')->default(DB::flat('CURRENT_TIMESTAMP'));
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