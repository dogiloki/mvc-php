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
    DB::DBcreate()->table('rol',function($table){
        $table->add('id','int')->autoIncrement()->primaryKey();
        $table->add('name','varchar',255);
        $table->add('description','varchar',255);
    });
    DB::DBcreate()->table('user_rol',function($table){
        $table->add('id','int')->autoIncrement()->primaryKey();
        $table->add('id_user','int')->foreignKey('user','id');
        $table->add('id_rol','int')->foreignKey('rol','id');
    });
}catch(\Exception $ex){
    echo $ex->getMessage();
}

?>