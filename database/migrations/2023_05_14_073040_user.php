
<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('user',function($table){
			$table->add('id','int')->autoIncrement()->primaryKey();
			$table->add('id_group','int')->foreignKey('group','id');
			$table->add('name','varchar',255);
			$table->add('email','varchar',255);
			$table->add('password','varchar',255);
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('user');
	}
	
};
		
?>		
