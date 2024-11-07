<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('direccion',function($table){
			$table->id();
			$table->idForeign('id_estado')->foreign('estado','id');
			$table->idForeign('id_municipio')->foreign('municipio','id');
			$table->string('calle')->nullable();
			$table->string('colina')->nullable();
			$table->int('cp')->nullable();
			$table->timestamps();
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('direccion');
	}
	
};

?>