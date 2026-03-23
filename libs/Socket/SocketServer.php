<?php

namespace libs\Socket;

use app\Models\Issabel\Configuration;

class SocketServer{

    public $sockets;
    public $host;
    public $port;
    public $server;
    public $clients;

    private $issabel_config;
    private $issabel_ami;

    public function __construct(){
        $this->host=env('SOCKET_HOST');
        $this->port=env('SOCKET_PORT');
        print("Iniciando Socket ".$this->host.":".$this->port."\n");
        $this->server=socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
        if(!socket_set_option($this->server,SOL_SOCKET,SO_REUSEADDR,1)){
            print("Error al establecer opción SO_REUSEADDR ".socket_strerror(socket_last_error()));
        }
        if(!socket_bind($this->server,$this->host,$this->port)){
            print("Error al vincular socket ".socket_strerror(socket_last_error()));
        }
        if(!socket_listen($this->server)){
            print("Error al escuchar ".socket_strerror(socket_last_error()));
        }
        $this->sockets=[];

        $this->issabel_config=Configuration::where('name','issabel')->first()??new Configuration();

        // Conectar a AMI
        $this->issabel_ami=fsockopen($this->issabel_config->url,5038,$errno,$errstr,30);

        // Login
        fwrite($this->issabel_ami,"Action: Login\r\nUsername: ".$this->issabel_config->user."\r\nSecret: ".$this->issabel_config->password."\r\n\r\n");
    }

    public function on($key,$action){
        $this->sockets[$key]=[
            "action"=>$action,
            "clients"=>[]
        ];
    }

    public function emit($key,$message,$client=null){
        $decoded['key']=$key;
        $decoded['msg']=$message;
        $this->sockets[$key]['message']=json_encode($decoded);
        $this->send($key,$client);
    }

    public function listen() {
    echo "Servidor Activo {$this->host}:{$this->port}\n";

    $buffer=[];

    while (true) {
        // Construir array de sockets a leer (servidor + clientes)
        $read_sockets = [$this->server];
        foreach ($this->sockets as $channel) {
            if (!empty($channel['clients']) && is_array($channel['clients'])) {
                $read_sockets = array_merge($read_sockets, $channel['clients']);
            }
        }
        $read_sockets = array_filter($read_sockets, fn($s) => $s !== null); // filtrar nulls

        $write = $except = null;

        if (@socket_select($read_sockets, $write, $except, 0, 10) > 0) {
            foreach ($read_sockets as $socket) {
                if ($socket === $this->server) {
                    // Nuevo cliente
                    $new_client = @socket_accept($this->server);
                    if ($new_client) {
                        // Agregar cliente a todos los canales
                        foreach ($this->sockets as $key => $channel) {
                            if (!isset($this->sockets[$key]['clients']) || !is_array($this->sockets[$key]['clients'])) {
                                $this->sockets[$key]['clients'] = [];
                            }
                            $this->sockets[$key]['clients'][] = $new_client;
                        }

                        $header = @socket_read($new_client, 1024);
                        $this->handshake($header, $new_client);
                    }
                } else {
                    // Cliente existente
                    $data = @socket_read($socket, 2048, PHP_BINARY_READ);
                    if ($data === false || $data === "") {
                        // Buscar y eliminar cliente de todos los canales
                        foreach ($this->sockets as $key => $channel) {
                            if (!empty($channel['clients']) && is_array($channel['clients'])) {
                                $index = array_search($socket, $this->sockets[$key]['clients']);
                                if ($index !== false) {
                                    unset($this->sockets[$key]['clients'][$index]);
                                    $this->sockets[$key]['clients'] = array_values($this->sockets[$key]['clients']); // reindex
                                }
                            }
                        }
                        @socket_close($socket);
                        continue;
                    }

                    $decoded = $this->unseal($data);
                    $json = json_decode($decoded);
                    if ($json && isset($this->sockets[$json->key]['action'])) {
                        $action = $this->sockets[$json->key]['action'];
                        $action($decoded, $socket);
                    }
                }
            }
        }

        // Leer eventos de AMI
        if ($this->issabel_ami && !feof($this->issabel_ami)) {
            $line = fgets($this->issabel_ami);
            if ($line !== false) {
                $line = trim($line);
                if($line===''){
                    // Procesar el evento almacenado
                    $this->emit('issabel-ami',$this->parseEvent($buffer));
                    $buffer=[];
                    continue;
                }
                $buffer[]=$line;
            }
        }
    }

    socket_close($this->server);
}

    private function parseEvent($lines){
        $event=[];
        $event['raw']=$lines;
        foreach($lines as $line){
            if(strpos($line,":")!==false){
                list($key,$value)=explode(":",$line,2);
                $event[strtolower(trim($key))]=trim($value);
            }
        }
        return json_encode($event);
    }


    public function send($key,$client_sender=null){
        $message=$this->seal($this->sockets[$key]['message']);
        foreach($this->sockets[$key]['clients']??[] as $client){
            if($client_sender==null){
                @socket_write($client,$message,strlen($message));
            }else{
                if($client!=$client_sender){
                    @socket_write($client,$message,strlen($message));
                }
            }
        }
    }

    public function private(){
        $this->clients=[$this->server];
    }

    private function handshake($request,$socket){
        $headers=$this->parseHeaders($request);
        if(!isset($headers['sec-websocket-key'])){
            echo "Error no se recibió sec-websocket-key";
        }
        $key=$headers['sec-websocket-key'];
        $accept=base64_encode(pack("H*",sha1($key."258EAFA5-E914-47DA-95CA-C5AB0DC85B11")));
        $upgrade="HTTP/1.1 101 Switching Protocols\r\n".
                "Upgrade: websocket\r\n".
                "Connection: Upgrade\r\n".
                "Sec-WebSocket-Accept: $accept\r\n\r\n";
        socket_write($socket,$upgrade,strlen($upgrade));
    }

    private function parseHeaders($request){
        $headers=[];
        $lines=preg_split("/\r\n/",$request);
        foreach($lines as $line){
            if(strpos($line,":")!==false){
                list($key,$value)=explode(":",$line,2);
                $headers[strtolower(trim($key))]=trim($value);
            }
        }
        return $headers;
    }

    private function seal($socket){
        $b=0x80|(0x1&0x0f);
        $length=strlen($socket);
        if($length<=125){
            $header=pack("CC",$b,$length);
        }else
        if($length>125 && $length<65536){
            $header=pack("CCn",$b,126,$length);
        }else
        if($length>=65536){
            $header=pack("CCNN",$b,127,$length);
        }
        return $header.$socket;
    }

    private function unseal($socket){
        $length=ord($socket[1]) & 127;
        if($length==126){
            $masks=substr($socket,4,4);
            $data=substr($socket,8);
        }else
        if($length==127){
            $masks=substr($socket,10,4);
            $data=substr($socket,14);
        }else{
            $masks=substr($socket,2,4);
            $data=substr($socket,6);
        }
        $socket="";
        for($a=0; $a<strlen($data); $a++){
            $socket.=$data[$a]^$masks[$a%4];
        }
        return $socket;
    }

}

?>