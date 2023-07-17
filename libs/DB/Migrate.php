<?php

namespace libs\DB;

use libs\DB\DB;
use libs\DB\Scheme;

class Migrate{

    private $name_table="migration";
    private $schema;

    public function init(){
        $this->schema=new Schema();
        $this->createTable();
    }

    public function getNameTable(){
        return $this->name_table;
    }

    private function createTable(){
        $this->schema->tableIfNotExists($this->getNameTable(),function($table){
			$table->id();
			$table->add('name','varchar')->unique();
			$table->add('batch','int');
			$table->add('applied_at','datetime');
		});
    }

    private function addMigration($name){
        DB::table($this->getNameTable())->insert([
            'name'=>$name,
            'batch'=>$this->getNextBatch(),
            'applied_at'=>date('Y-m-d H:i:s')
        ]);
    }

    private function removeMigration($name){
        DB::table($this->getNameTable())->where('name',$name)->delete();
    }

    private function getNextBatch(){
        $last_batch=DB::table($this->getNameTable())->select()->orderBy('batch','desc')->first()->get()->fetch();
        return ($last_batch['batch']??0)+1;
    }

    private function getMigrations($type){
        $migrations=DB::table($this->getNameTable())->select()->get()->fetchAll();
        if($type=='up'){
			$directory=scandir("database/migrations");
		}else
		if($type=='down'){
			$directory=array_reverse(scandir("database/migrations"));
		}
        $files=[];
        foreach($directory as $file){
            if($file!='.' && $file!='..'){
                $ext=explode(".",$file);
                if($ext[1]=='php'){
                    if(count($migrations)==0){
                        $files[]=$file;
                        continue;
                    }
                    foreach($migrations as $migration){
                        if($migration['name']==$ext[0]){
                            continue 2;
                        }
                    }
                    $files[]=$file;
                }
            }
        }
        return $files;
    }

    public function migrate($type){
        $directory=$this->getMigrations($type);
        foreach($directory as $file){
			if($file!='.' && $file!='..'){
				$ext=explode(".",$file);
				if($ext[1]=='php'){
					$migration=require_once("database/migrations/".$file);
					try{
						if($type=='up'){
							$migration->up();
                            $this->addMigration($ext[0]);
							echo "\n".$file." (up exitoso)\n";
						}else
						if($type=='down'){
							$migration->down();
                            $this->removeMigration($ext[0]);
							echo "\n".$file." (down exitoso)\n";
						}
					}catch(\Exception $ex){
						if($type=='up'){
							echo "\n".$file." (up fallido)\n";
						}else
						if($type=='down'){
							echo "\n".$file." (down fallido)\n";
						}
						echo "\n".$ex->getMessage()."\n";
						return;
					}
				}
			}
		}
    }

}

?>