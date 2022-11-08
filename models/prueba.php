<?php

namespace models;

use libs\DB;
use util\Secure;

class Prueba{

	public function modelar(){
		return Secure::encodePassword("HOla");
	}

}

?>