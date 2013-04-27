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
            'clients'        => $clients
        );

        $conn->send(json_encode($data));

    }
    public function onMessage(ConnectionInterface $from, $msg){

        list($typeMessage,$name,$id_resource_parent,$message) = explode('|',$msg);

        if($typeMessage == 'onOpen'){

           $this->sendNewUsers($this->clients);

        }elseif($typeMessage == 'onMessage'){

            $sendMessageOneUser = $this->sendMessageOneUser($this->clients,$from,$name,$message,$id_resource_parent);

            if(!$sendMessageOneUser){

               $this->sendMessageToUsers($this->clients,$from,$name,$message);
            }
        }
    }




    /*SEND*/




    /**
     * onOpen
     *
     * Send New Users
     *
     * @param $clients
     * @return bool
     */
    protected function sendNewUsers($clients){

        foreach($clients as $client){
            $data =  array(
                'type'=>'listUsers',
                'usersCount' => count($clients),
            );
            $client->send(json_encode($data));
        }
        return true;
    }
    /**
     * onMessage
     *
     * Send Message One User
     *
     * @param $clients
     * @param $from
     * @param $name
     * @param $message
     * @param $id_resource_parent
     * @return bool
     */
    protected function sendMessageOneUser($clients,$from,$name,$message,$id_resource_parent) {

        foreach($clients as $client){
            if($id_resource_parent == $client->resourceId){
                $data =  array(
                    'type'      => 'onMessage',
                    'date'      => $this->dateFormat(date("Y-m-d H:i:s")),
                    'name'      => $name,
                    'message'   => $message
                );
                if($from !== $client){
                    $client->send(json_encode($data));
                }
                return true;
            }
        }
        return false;
    }
    /**
     * onMessage
     *
     * Send Message To Users
     *
     * @param $clients
     * @param $from
     * @param $name
     * @param $message
     * @return bool
     */
    protected function sendMessageToUsers($clients,$from,$name,$message) {

        foreach($clients as $client){
            if($from !== $client){

                $data =  array(
                    'type'      => 'onMessage',
                    'date'      => $this->dateFormat(date("Y-m-d H:i:s")),
                    'name'      => $name,
                    'message'   => $message
                );
                $client->send(json_encode($data));
            }
        }
        return true;
    }



    protected function dateFormat($date){
        return strftime("%k:%M:%S", strtotime($date));
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }

}