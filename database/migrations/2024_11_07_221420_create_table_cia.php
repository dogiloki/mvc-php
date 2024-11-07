<?php

use libs\DB\Migration;
		
return new class extends Migration{
		
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		$this->table('cia',function($table){
			$table->id();
			$table->idForeign('id_direccion')->foreign('direccion','id');
			$table->idForeign('id_batallon')->foreign('batallon','id');
			$table->int('numero');
			$table->timestamps();
		});
	}
		
	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		$this->dropIfExists('cia');
	}
	
};

?>