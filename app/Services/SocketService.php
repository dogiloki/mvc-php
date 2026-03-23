<?php

namespace app\Services;

use app\Models\Issabel\AsteriskConfiguration;
use libs\Service\Contract\ServiceImpl;
use Workerman\Worker;
use Workerman\Connection\TcpConnection;
use Workerman\Timer;
use app\Controllers\Issabel\ExtensionController;

class SocketService implements ServiceImpl{

	private $host;
    private $port;

	public function handle(){
		$this->host=env('SOCKET_HOST');
        $this->port=env('SOCKET_PORT');
		// Servidor WebSocket
		$ws_worker=new Worker("websocket://".$this->host.":".$this->port);
		// Array global para almacenar los canales y clientes
		$channels=[];
		// Función para emitir mensajes
		$emit=function($message,$channel=null,$client=null)use(&$channels){
			$data=[
				'channel'=>$channel??'global',
				'message'=>$message
			];
			if($client){
				$client->send(json_encode($data));
			}else if($channel && isset($channels[$channel])){
				foreach($channels[$channel] as $chann){
					$chann->send(json_encode($data));
				}
			}else{
				foreach($channels as $chann_clients){
					foreach($chann_clients as $chann_client){
						$chann_client->send(json_encode($data));
					}
				}
			}
		};
		// Evento al iniciar el worker
		$ws_worker->onWorkerStart=function()use(&$channels,$emit){
			$issabel_config=AsteriskConfiguration::where('name','issabel')->first()??new AsteriskConfiguration();

			// Conectar y loguearse en AMI
			$connectAMI=function()use($issabel_config){
				try{
					// Conectar a AMI
					$socket=@fsockopen($issabel_config->url,5038,$errno,$errstr,30);
					if(!$socket){
						print("\n❌Error al conectar AMI: $errstr ($errno)\n");
						return null;
					}
					stream_set_blocking($socket,false);
					// Login
					fwrite($socket,"Action: Login\r\nUsername: ".$issabel_config->user."\r\nSecret: ".$issabel_config->password."\r\n\r\n");
					print("\n✅ Conectado AMI\n");
					return $socket;
				}catch(\Exception $ex){
					print("\n❌ Excepción AMI: {$ex->getMessage()}\n");
					return null;
				}
			};

			$parseEvent=function($lines){
				$event=[];
				$event['raw']=$lines??[];
				$event['timestamp']=date('Y-m-d H:i:s');
				// Parsear de líneas
				foreach($lines??[] as $line){
					if(strpos($line,":")!==false){
						list($key,$value)=explode(":",$line,2);
						$event[strtolower(trim($key))]=trim($value);
					}
				}
				return json_encode($event);
			};

			$issabel_ami=$connectAMI();
			$buffer=[];
        	
			// Leer eventos cada segundo
			Timer::add(0.5,function()use($parseEvent,$connectAMI,&$issabel_ami,&$buffer,$emit){
				// Si el socket esta caído, intentar reconectar
				if(!$issabel_ami || !is_resource($issabel_ami) || feof($issabel_ami)){
					print("\n⚠️ AMI desconectado, reintentando conectar...\n");
					$issabel_ami=$connectAMI();
					return;
				}
				try{
					// Leer datos disponibles
					while(($line=fgets($issabel_ami))!==false){
						$line=trim($line);
						if($line===''){
							// Procesar el evento almacenado
							if(!empty($buffer)){
								$emit($parseEvent($buffer),'issabel-ami');
								$buffer=[];
							}
						}else{
							$buffer[]=$line;
						}
					}
				}catch(\Exception $ex){
					print("\n⚠️ Error al leer datos del AMI: {$ex->getMessage()}\n");
					if(is_resource($issabel_ami) || fclose($issabel_ami));
					$issabel_ami=null;
				}
			});
		};
		// Evento cuando el cliente se conecta
		$ws_worker->onConnect=function(TcpConnection $connection)use($emit){
			$connection->channels=[];
		};
		// Evento cuando envía mensaje
		$ws_worker->onMessage=function(TcpConnection $connection,$data)use(&$channels,$emit){
			$data=json_decode($data,true);
			if(!$data) return;
			switch($data['action']??''){
				case 'join':{
					// Inerse al canal
					$channel=$data['channel'];
					$connection->channels[$channel]=true;
					if(!isset($channels[$channel])) $channels[$channel]=[];
					$channels[$channel][$connection->id]=$connection;
					$connection->send(json_encode([
						'status'=>'joined',
						'channel'=>$channel
					]));
					break;
				}
				case 'leave':{
					// Salir del canal
					$channel=$data['channel'];
					unset($connection->channels[$channel]);
					unset($channels[$channel][$connection->id]);
					$connection->send(json_encode([
						'status'=>'left',
						'channel'=>$channel
					]));
					break;
				}
				case 'message':{
					$channel=$data['channel'];
					$message=$data['message']??'';
					switch($channel){
						case 'issabel-extensions-status':{
							$controller=new ExtensionController();
							$emit($controller->status(),'issabel-extensions-status',$connection);
							break;
						}
					}
				}
			}
		};
		// Evento cuando el cliente se desconecta
		$ws_worker->onClose=function(TcpConnection $connection)use(&$channels){
			foreach($connection->channels as $channel=>$_){
				unset($channels[$channel][$connection->id]);
			}
		};
		// Correr el servidor
		Worker::runAll();
	}

	public function terminate(){
		
	}

	public function report($ex){
		exception($ex);
	}

}

?>