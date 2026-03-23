<?php

namespace app\Services;

use libs\Service\Contract\ServiceImpl;
use Workerman\Worker;
use Workerman\Timer;
use Workerman\Protocols\Http\Response;
use Workerman\Protocols\Http\Request;
use Workerman\Connection\TcpConnection;
use App\Controllers\Issabel\ExtensionController;
use App\Models\Issabel\AsteriskConfiguration;

class SseService implements ServiceImpl{

	private $host;
	private $port;
	private $channels=[];

	public function handle(){
		$this->host=env('SSE_HOST');
		$this->port=env('SSE_PORT');

		// Servidor SSE
		$sse_worker=new Worker("http://".$this->host.":".$this->port);

		// Evento al iniciar
		$sse_worker->onWorkerStart=function(){
			$this->initAMI();
		};

		// Eventos para recibir conexiones SSE
		$sse_worker->onMessage=function(TcpConnection $connection, Request $request){
			// Obtener canal de la query string
			$channel=$request->get('channel','global');

			// Configurar cabeceras SSE
			$headers=[
				'Content-Type'=>'text/event-stream',
				'Cache-Control'=>'no-cache',
				'Connection'=>'keep-alive',
				'Access-Control-Allow-Origin'=>'*'
			];
			$connection->send(new Response(200,$headers));

			// Almacenar cliente en el canal
			if(!isset($this->channels[$channel])){
				$this->channels[$channel]=[];
			}
			$this->channels[$channel][$connection->id]=$connection;
			print("\n✅ Cliente conectado al canal '{$channel}' (Total: ".count($this->channels[$channel]).")\n");

			// Evento al cerrar la conexión
			$connection->onClose=function()use($connection,$channel){
				if(isset($this->channels[$channel][$connection->id])){
					unset($this->channels[$channel][$connection->id]);
					print("\n❌ Cliente desconectado del canal '{$channel}' (Total: ".count($this->channels[$channel]).")\n");
					if(empty($this->channels[$channel])){
						unset($this->channels[$channel]);
					}
				}
			};
		};
		// Iniciar el worker
		Worker::runAll();
	}

	// Función para emitir mensajes SSE
	private function emit($message,$channel='global'){
		if(!isset($this->channels[$channel])) return;
		$json=json_encode($message,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$lines=explode("\n",$json);
		$payload="event: {$channel}\n";
		foreach($lines as $line){
			$payload.="data: {$line}\n";
		}
		$payload.="\n";
		foreach($this->channels[$channel] as $client){
			if($client->getStatus()===TcpConnection::STATUS_ESTABLISHED){
				$client->send(new Response(200,[
					'Content-Type'=>'text/event-stream'
				],$payload));
			}
		}
	}

	// Iniciar conexión con AMI y leer los eventos
	private function initAMI(){
		$issabel_config=AsteriskConfiguration::where('name','issabel')->first()??new AsteriskConfiguration();
		$connectAMI=function()use($issabel_config){
			try{
				// Conectar a AMI
				$socket=@fsockopen($issabel_config->ip,5038,$errno,$errstr,30);
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
			return $event;
		};
		$ami_socket=$connectAMI();
		// Timer para leer eventos
		$buffer=[];
		Timer::add(0.1,function()use($parseEvent,$connectAMI,&$ami_socket,&$buffer){
			// Si el socket esta caído, intentar reconectar
			if(!$ami_socket || !is_resource($ami_socket) || feof($ami_socket)){
				print("\n⚠️ AMI desconectado, reintentando conectar...\n");
				$ami_socket=$connectAMI();
				return;
			}
			try{
				// Leer datos disponibles
				while(($line=fgets($ami_socket))!==false){
					$line=trim($line);
					if($line===''){
						// Procesar el evento almacenado
						if(!empty($buffer)){
							$this->emit($parseEvent($buffer),'issabel-ami');
							$buffer=[];
						}
					}else{
						$buffer[]=$line;
					}
				}
			}catch(\Exception $ex){
				print("\n⚠️ Error al leer datos del AMI: {$ex->getMessage()}\n");
				if(is_resource($ami_socket) || fclose($ami_socket));
				$ami_socket=null;
			}
		});
		// Leer estado de extensiones
		Timer::add(1,function(){
			$controller=new ExtensionController();
			$this->emit($controller->status(),'issabel-extensions-status');
		});
	}

	public function terminate(){
		
	}

	public function report($ex){
		exception($ex);
	}

}

?>