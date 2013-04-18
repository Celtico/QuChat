<?php

namespace QuChat\Server;

use QuChat\Mapper\QuChatMapper;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\Wamp;

class QuChat implements MessageComponentInterface {

    protected $clients;
    protected $db;

    public function __construct() {

        $this->db      = new QuChatMapper();
        $this->clients = new \SplObjectStorage;
    }
    public function onOpen(ConnectionInterface $conn){

        $this->clients->attach($conn);

        $id_chat = $this->db->onInsert( array('id_resource'=>$conn->resourceId,'date'=>date("Y-m-d H:i:s")));
        $data =  array(
            'type'           => 'onOpen',
            'id_resource'    => $conn->resourceId,
            'id_chat'        => $id_chat,
            'usersCount'     => count($this->clients)
        );

        $conn->send(json_encode($data));
        $this->log(true,'onOpen Insert DATA SEND');

    }
    public function onMessage(ConnectionInterface $from, $msg){


        list($typeMessage,$name,$id_user,$id_resource,$id_parent,$name_parent,$id_resource_parent,$message) = explode('|',$msg);

        if($typeMessage == 'onOpen'){

            //data
            $updateNewUser = $this->updateNewUser($id_user,$name,$id_parent);
            $this->log($updateNewUser,'updateNewUser onOpen DATA');

            if($updateNewUser){

                //data
                $saveSendListUsers = $this->saveListUsers($this->clients);
                $this->log($saveSendListUsers,'saveSendListUsers onOpen DATA');

                if($saveSendListUsers){

                    //send
                    $sendNewUsers = $this->sendNewUsers($this->clients);
                    $this->log($sendNewUsers,'sendNewUsers onOpen SEND');
                }
            }

        }elseif($typeMessage == 'onMessage'){

            //data
            $sendMessageUser = $this->saveMessageUser($name,$id_user,$id_resource,$id_parent,$name_parent,$id_resource_parent,$message);
            $this->log($sendMessageUser,'saveMessageUser onMessage DATA');

            if($sendMessageUser){

                //send
                $sendMessageOneUser = $this->sendMessageOneUser($this->clients,$from,$name,$message,$id_resource_parent);
                $this->log($sendMessageOneUser,'sendMessageOneUser onMessage SEND');

                if(!$sendMessageOneUser){

                    //send
                    $sendMessageToUsers = $this->sendMessageToUsers($this->clients,$from,$name,$message);
                    $this->log($sendMessageToUsers ,'sendMessageToUsers onMessage SEND');

                }
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
    protected function sendNewUsers($clients) {
        foreach($clients as $client){
            $data =  array(
                'type'=>'listUsers',
                'usersCount' => count($clients),
            );
            $this->log(json_encode($data) ,'sendNewUsers DATA-SEND');
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
                    $this->log(json_encode($data) ,'sendMessageOneUser DATA-SEND');
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
                $this->log(json_encode($data) ,'sendMessageToUsers DATA-SEND');
                $client->send(json_encode($data));
            }
        }
        return true;
    }






/*DATA*/





    /**
     * onOpen
     *
     * Update New User
     *
     * @param $id_user
     * @param $name
     * @param $id_chat
     * @return bool
     */
    protected function updateNewUser($id_user,$name,$id_chat){

        $data =  array(
            'id_user'   => $id_user,
            'name'      => $name,
            'message'   => 'updateNewUser'
        );
        $this->db->onUpdate($data,array('id_chat'=>$id_chat));

        return true;
    }
    /**
     * onOpen
     *
     * Save List Users
     *
     * @param $clients
     * @return bool
     */
    protected  function saveListUsers($clients) {

        $users = array();
        foreach($clients as $client){

            $data =  array( 'id_resource'=>$client->resourceId );
            $this->db->where($data);
            $this->db->Order('id_chat desc');
            $row = $this->db->row();
            $users[] = array(
                'id_user'       =>$row['id_user'],
                'id_resource'   =>$row['id_resource'],
                'type'          =>'listUsers',
                'date'          =>date("Y-m-d H:i:s"),
                'name'          =>$row['name'],
                'message'       =>'listUsers',

            );

        }

        $this->db->onRemove(array('message'=>'updateNewUser'));
        $this->db->onRemove(array('type'=>'listUsers'));

        foreach($users as $user){
            $this->db->onInsert($user);
        }
        return true;
    }


    /**
     * onMessage
     *
     * Save Message User
     *
     * @param $name
     * @param $id_user
     * @param $id_resource
     * @param $id_parent
     * @param $name_parent
     * @param $id_resource_parent
     * @param $message
     * @return bool
     */
    protected function saveMessageUser($name,$id_user,$id_resource,$id_parent,$name_parent,$id_resource_parent,$message) {

        $data =  array(

            'id_user'               =>$id_user,
            'id_user_parent'        =>$id_parent,

            'id_resource'           =>$id_resource,
            'id_resource_parent'    =>$id_resource_parent,

            'name_parent'           =>$name_parent,
            'name'                  =>$name,

            'type'                  =>'onMessage',

            'date'=>                date("Y-m-d H:i:s"),
            'message'               =>$message,

        );

        $this->db->onInsert($data);

        return true;
    }


    protected function log($data,$position){
        //echo "\n".$position."\n";
        //var_dump($data);
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
