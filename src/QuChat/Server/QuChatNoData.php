<?php

namespace QuChat\Server;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class QuChatNoData implements MessageComponentInterface {

    protected $clients;
    protected $db;

    public function __construct() {

        $this->clients = new \SplObjectStorage;
    }
    public function onOpen(ConnectionInterface $conn){

        $this->clients->attach($conn);

        $clients = array();
        foreach($this->clients as $client){
            $clients[] =  array( 'id_resource'=>$client->resourceId );
        }

        $data =  array(
            'type'           => 'onOpen',
            'id_resource'    => $conn->resourceId,
            'clients'        => $clients,
            'usersCount'    =>  count($this->clients)
        );

        $conn->send(json_encode($data));

    }
    public function onMessage(ConnectionInterface $from, $msg){

        list($typeMessage,$name,$id_resource_parent,$message) = explode('|',$msg);

        $send = false;

        if($typeMessage == 'onOpen'){

            $send = false;

            foreach($this->clients as $client){

                $data =  array(
                    'type'=>'listUsers',
                    'usersCount' => count($this->clients),
                );

                $client->send(json_encode($data));
                $send = true;
            }


        }elseif($typeMessage == 'onMessage'){

            $send = false;

            foreach($this->clients as $client){
                if($id_resource_parent == $client->resourceId){
                    $data =  array(
                        'type'      => 'onMessage',
                        'date'      => '',
                        'name'      => $name,
                        'message'   => $message
                    );
                    if($from !== $client){

                        $client->send(json_encode($data));
                        $send = true;
                    }
                }
            }

            if(!$send){

                $send = false;

                foreach($this->clients as $client){

                    if($from !== $client){
                        $data =  array(
                            'type'      => 'onMessage',
                            'date'      => '',
                            'name'      => $name,
                            'message'   => $message
                        );

                        $client->send(json_encode($data));
                        $send = true;
                    }
                }
            }
        }

        if(!$send){
            $data =  array(
                'type'      => 'onMessage',
                'date'      => '',
                'name'      =>  $from->resourceId,
                'message'   => ':)'
            );
            $from->send(json_encode($data));
        }


    }


    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }

}