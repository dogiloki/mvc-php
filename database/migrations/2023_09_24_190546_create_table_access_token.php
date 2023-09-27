<?php

use libs\DB\Migration;
use libs\DB\DB;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('access_token',function($table){
			$table->id();
			$table->add('id_tokenable','bigint');
			$table->add('type_tokenable','varchar');
			$table->add('name','varchar');
			$table->add('token','varchar')->unique();
			$table->add('abilities','text');
			$table->add('last_activity','timestamp')->default(DB::flat('CURRENT_TIMESTAMP'));
			$table->add('expire_at','timestamp')->default(DB::flat('CURRENT_TIMESTAMP'));
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