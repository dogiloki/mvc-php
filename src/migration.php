<?php

use libs\DB;

// Sentencias SQL

try{
    DB::DBcreate()->table('user',function($table){
        $table->add('id','int')->autoIncrement()->primaryKey();
        $table->add('name','varchar',255);
        $table->add('email','varchar',255);
        $table->add('password','varchar',255);
    });
}catch(\Exception $e){
    echo $ex->getMessage();
}

?>