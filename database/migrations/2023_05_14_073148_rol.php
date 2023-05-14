
<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->create()->table('rol',function($table){
			$table->add('id','int')->autoIncrement()->primaryKey();
			$table->add('name','varchar',255);
			$table->add('description','varchar',255);
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('rol');
	}
	
};
		
?>		
