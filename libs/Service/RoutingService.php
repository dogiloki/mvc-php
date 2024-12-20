<?php

namespace libs\Service;

use libs\Service\Contract\ServiceImpl;
use libs\Router\Route;
use libs\Config;

class RoutingService implements ServiceImpl{

	public function handle(){
		// Cargar archivos de routers
		$directory=scandir(Config::filesystem("routes.path"));
		foreach($directory as $file){
			if($file=='.' || $file=='..'){
				continue;
			}
			$name_ext=explode(".",$file);
			$name=$name_ext[0];
			$ext=$name_ext[1];
			if($ext!="php"){
				continue;
			}
			$src=Config::filesystem("routes.path")."/".$file;
			Route::group($name)
			->group(function()use($src){
				require_once($src);
			});
		}
		// Ruta para los componentes en SPA
		/*
		Route::post('/component/{name}',function($request){
			ob_start();
			$name=ucfirst($request->input('name'));
			$instance=new ("\\".str_replace("/","\\",Config::filesystem("components.path"))."\\".$name)();
			$done=$instance->syncRequest($request);
			$instance->render();
			$html=ob_get_clean();
			$data=$instance->getData();
			return json([
				"html"=>$done?$html:"",
				"data"=>$data,
				"direct"=>$done?null:$instance->direct()
			]);
		})->middleware('session','csrf')->group('component');
		*/
		
		Route::post('/component/{name?}',function($request){
			$class_name="app\\Components\\".$request->name;
			$component=new $class_name();
			if(!$component->call($request)){
				return abort(404);
			}
			if($request->set_properties){
				$component->setProperties(json_decode($request->properties));
			}
			$component->view();
			$content=ob_get_clean();
			return json([
				'content'=>$content,
				'properties'=>$component->getProperties()
			]);
		})->name('component');
		
		Route::post('/component-data-table',function($request){
			$class_name="app\\Components\\".$request->name;
			$component=new $class_name();
			if(!$component->call($request)){
				return abort(404);
			}
			$columns=json_decode($request->columns);
			$paginate=json_decode($request->paginate);
			$component->selectColumns($columns->select);
			$component->withMethods($columns->methods);
			$component->loadPaginate($paginate);
			return json($component->dataTable());
		})->name('component-data-table');
	}

	public function terminate(){
		
	}

	public function report($ex){
		exception($ex);
	}
		
}

?>