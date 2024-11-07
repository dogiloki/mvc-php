<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('seccion',function($table){
			$table->id();
			$table->idForeign('id_direccion')->foreign('direccion','id');
			$table->int('numero');
			$table->string('nombre');
			$table->timestamps();
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('seccion');
	}
	
};

?>