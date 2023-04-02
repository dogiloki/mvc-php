<?php

require_once 'libs/Config.php';

use libs\Config;

class SocketServer{

    public $sockets;
    public $host;
    public $port;
    public $server;

    public function __construct(){
        Config::singleton("src/config.cfg");
        $this->host=Config::get('SOCKET_HOST');
        $this->port=Config::get('SOCKET_PORT');
        $this->server=socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
        socket_set_option($this->server,SOL_SOCKET,SO_REUSEADDR,1);
        socket_bind($this->server,0,$this->port);
        socket_listen($this->server);
    }

    public function on($key,$action){
        $this->sockets[$key]=[
            "action"=>$action,
            "clients"=>[$this->server]
        ];
    }

    public function emit($key,$message,$client=null){
        $json=json_decode($message);
        $json->key=$key;
        $this->sockets[$key]['message']=json_encode($json);
        $this->send($key,$client);
    }

    public function listen(){
        echo "Servidor a activo ".$this->host.":".$this->port."\n";
        while(true){
            foreach($this->sockets as $key=>$socket){
                $new_clients=$socket['clients'];
                socket_select($new_clients,$null,$null,0,10);
                if(in_array($this->server,$new_clients)){
                    $new_socket=socket_accept($this->server);
                    $this->sockets[$key]['clients'][]=$new_socket;
                    $header=socket_read($new_socket,1024);
                    $this->handshake($header,$new_socket);
                    $index=array_search($this->server,$new_clients);
                    unset($new_clients[$index]);
                }
                foreach($new_clients as $new_client_resource){
                    while(@socket_recv($new_client_resource,$socket_data,1024,0)>=1){
                        if($socket_data){
                            $data=$this->unseal($socket_data);
                            $json=json_decode($data);
                            if($json!=null){
                                $action=$this->sockets[$json->key]['action']??null;
                                if($action!=null){
                                    $action($data,$new_client_resource);
                                }
                            }
                            break 2;
                        }
                    }
                    $socket_data=@socket_read($new_client_resource,1024,PHP_NORMAL_READ);
                    if($socket===false){
                        $index=array_search($new_client_resource,$this->sockets[$key]['clients']);
                        unset($socket['clients'][$index]);
                    }
                }
            }
        }
        socket_close($this->server);
    }

    public function send($key,$client_sender=null){
        $message=$this->seal($this->sockets[$key]['message']);
        foreach($this->sockets[$key]['clients'] as $client){
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

    private function handshake($header,$socket){
        $headers=[];
        $lines=preg_split("/\r\n/",$header);
        foreach($lines as $line){
            $line=chop($line);
            if(preg_match("/\A(\S+): (.*)\z/",$line,$matches)){
                $headers[$matches[1]]=$matches[2];
            }
        }
        $key=$headers['Sec-WebSocket-Key'];
        $accept=base64_encode(pack("H*",sha1($key."258EAFA5-E914-47DA-95CA-C5AB0DC85B11")));
        $buffer="HTTP/1.1 101 Web Socket Protocol Handshake\r\n";
        $buffer.="Upgrade: websocket\r\n";
        $buffer.="Connection: Upgrade\r\n";
        $buffer.="WebSocket-Origin: ".$this->host."\r\n";
        $buffer.="WebSocket-Location: ws://$this->host:$this->port\r\n";
        $buffer.="Sec-WebSocket-Accept: $accept\r\n\r\n";
        @socket_write($socket,$buffer,strlen($buffer));
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