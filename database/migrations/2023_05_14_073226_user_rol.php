
<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->create()->table('user_rol',function($table){
			$table->add('id','int')->autoIncrement()->primaryKey();
			$table->add('id_user','int')->foreignKey('user','id');
			$table->add('id_rol','int')->foreignKey('rol','id');
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
