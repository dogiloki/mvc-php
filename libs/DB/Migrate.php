<?php

namespace libs\DB;

use libs\DB\DB;
use libs\Console\Console;
use libs\Config;

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

    private function addMigration($name,$batch){
        DB::table($this->getNameTable())->insert([
            'name'=>$name,
            'batch'=>$batch,
            'applied_at'=>date('Y-m-d H:i:s')
        ]);
    }

    private function removeMigration($name,$batch){
        DB::table($this->getNameTable())->where('name',$name)->and()->where('batch',$batch)->delete()->execute();
    }

    private function getNextBatch(){
        $last_batch=DB::table($this->getNameTable())->select()->orderBy('batch','desc')->limit(1)->execute()->fetch();
        return ($last_batch['batch']??0)+1;
    }

    private function getMigrations($type,$batch){
        $directory=scandir(Config::filesystem('database.migrations'));
		if($type=='up'){
            $migrations=DB::table($this->getNameTable())->select()->execute()->fetchAll();
		}else
        if($type=='down'){
            $migrations=DB::table($this->getNameTable())->select()->where('batch',$batch)->execute()->fetchAll();
			$directory=array_reverse($directory);
        }
        $files=[];
        foreach($directory as $file){
            if($file!='.' && $file!='..'){
                $ext=explode(".",$file);
                if($ext[1]=='php'){
                    if($type=='up'){
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
                    }else
                    if($type=='down'){
                        foreach($migrations as $migration){
                            if($migration['name']==$ext[0]){
                                $files[]=$file;
                            }
                        }
                    }
                }
            }
        }
        return $files;
    }

    public function migrate($type){
        $console=new Console();
        $batch=$this->getNextBatch();
        if($type=='down'){
            $batch--;
        }
        $directory=$this->getMigrations($type,$batch);
        foreach($directory as $file){
			if($file!='.' && $file!='..'){
				$ext=explode(".",$file);
				if($ext[1]=='php'){
					$migration=require_once(Config::filesystem('database.migrations')."/".$file);
					try{
						if($type=='up'){
							$migration->up();
                            $this->addMigration($ext[0],$batch);
							$console->success($file." (up exitoso)");
						}else
						if($type=='down'){
							$migration->down();
                            $this->removeMigration($ext[0],$batch);
							$console->success($file." (down exitoso)");
						}
					}catch(\Exception $ex){
						if($type=='up'){
							$console->error($file." (up fallido)");
						}else
						if($type=='down'){
							$console->error($file." (down fallido)");
						}
						$console->error($ex->getMessage());
						return;
					}
				}
			}
		}
    }

}

?>