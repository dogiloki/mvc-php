
<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('rol',function($table){
			$table->id();
			$table->add('name','varchar',255);
			$table->add('description','varchar',255);
			$table->timestamps();
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
