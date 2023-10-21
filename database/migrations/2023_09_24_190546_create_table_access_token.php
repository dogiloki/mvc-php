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
			$table->bigint('id_tokenable');
			$table->string('type_tokenable');
			$table->string('name');
			$table->string('token')->unique();
			$table->text('abilities');
			$table->string('ip_address');
			$table->string('user_agent');
			$table->timestamp('last_activity')->default(DB::flat('CURRENT_TIMESTAMP'));
			$table->timestamp('expire_at')->default(DB::flat('CURRENT_TIMESTAMP'));
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