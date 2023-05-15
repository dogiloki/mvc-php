
<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('user_rol',function($table){
			$table->id();
			$table->idForeign('id_user')->foreignKey('user','id');
			$table->idForeign('id_rol')->foreignKey('rol','id');
			$table->timestamps();
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('user_rol');
	}
	
};
		
?>		
