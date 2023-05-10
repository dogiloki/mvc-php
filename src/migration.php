<?php

use libs\DB\DB;

// Sentencias SQL

try{
    DB::DBcreate()->table('group',function($table){
        $table->add('id','int')->autoIncrement()->primaryKey();
        $table->add('name','varchar',255);
        $table->add('description','varchar',255);
    });
    DB::DBcreate()->table('user',function($table){
        $table->add('id','int')->autoIncrement()->primaryKey();
        $table->add('id_group','int')->foreignKey('group','id');
        $table->add('name','varchar',255);
        $table->add('email','varchar',255);
        $table->add('password','varchar',255);
    });
}catch(\Exception $ex){
    echo $ex->getMessage();
}

?>