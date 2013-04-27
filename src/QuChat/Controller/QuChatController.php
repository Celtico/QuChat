<?php
/**
 * @Author: Cel Ticó Petit
 * @Contact: cel@cenics.net
 * @Company: Cencis s.c.p.
 */

namespace QuChat\Controller;

use QuChat\Mapper\QuChatMapper;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

class QuChatController extends AbstractActionController
{

    protected $db;
    protected $id_user;
    protected $name;

    public function indexAction(){}
    public function messagesAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id',0);
        $qu_chat_model = $this->getServiceLocator()->get('qu_chat_mapper');
        $model = new ViewModel();
        $model->setVariables(array(
                'messages'=> $qu_chat_model->getAll(array('type'=>'onMessage')),
                'id_user'=>$id));
        $model->setTemplate('qu-chat/qu-chat/messages');
        return  $model->setTerminal(true);
    }



    public function listAction()
    {
        $post = $this->getEvent()->getRequest()->getPost();

        if(isset($post['id_user']))     $this->id_user = $post['id_user'];
        if(isset($post['name']))        $this->name    = $post['name'];


        if(isset($post['id_resource'])){
            $id_resource = $post['id_resource'];
        }else{
            $id_resource = '';
        }

        if(isset($post['clients'])){
            $clients = $post['clients'];
        }else{
            $clients = '';
        }

        $this->insertNewUser($id_resource);

        $model = new ViewModel();
        $model->setVariables(array(
            'listUsers'=> $this->saveListUsers($clients)
        ));

        $model->setTemplate('qu-chat/qu-chat/list');
        return  $model->setTerminal(true);

    }

    /**
     * @param $id_resource
     * @return mixed
     */
    protected function insertNewUser($id_resource){

        if($id_resource){

            $this->db = $this->getServiceLocator()->get('qu_chat_mapper');

            $data =  array(
                'id_user'     => $this->id_user,
                'name'        => $this->name ,
                'type'        => 'insertNewUser',
                'message'     => $_SERVER['REMOTE_ADDR'],
                'id_resource' => $id_resource,
                'date'        => date("Y-m-d H:i:s")
            );

            return $this->db->onInsert($data);

        }else{

            return false;

        }
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

        $this->db = $this->getServiceLocator()->get('qu_chat_mapper');

        if($clients){

            $users = array();
            $clients = explode(',',str_replace(',|','',$clients.'|'));
            foreach($clients as $client){

                $this->db->where(array('id_resource'=>$client));

                $this->db->Order('id_chat desc');

                $row = $this->db->row();

                if($row['id_resource'] == '')  $row['id_resource'] =  $client;
                if($row['id_user'] == '')      $row['id_user'] =      $client;
                if($row['name'] == '')         $row['name'] =         $client;

                    $users[] = array(
                        'id_user'       => $row['id_user'],
                        'id_resource'   => $row['id_resource'],
                        'type'          => 'listUsers',
                        'date'          => date("Y-m-d H:i:s"),
                        'name'          => $row['name'],
                        'message'       => $row['message'],
                    );

            }

            //$this->db->onRemove(array('type'=>'insertNewUser'));
            $this->db->onRemove(array('type'=>'listUsers'));

            foreach($users as $user){
                $this->db->onInsert($user);
            }

            return $this->db->getAll(array('type'=>'listUsers'));

        }else{

            return $this->db->getAll(array('type'=>'listUsers'));

        }

    }






    public function messageAction()
    {
        $post = $this->getEvent()->getRequest()->getPost();

        $this->id_user = $post['id_user'];
        $this->saveMessageUser($post['id_resource_parent'],$post['message']);

        $model = new ViewModel();
        return  $model->setTerminal(true);
    }

    /**
     * onMessage
     *
     * Save Message User
     *
     * @param $id_resource_parent
     * @param $message
     * @return bool
     */
    protected function saveMessageUser($id_resource_parent,$message) {

        $this->db = $this->getServiceLocator()->get('qu_chat_mapper');

        $user        = $this->db->getRow(array('id_user'=>$this->id_user,'type'=>'listUsers'));
        $user_parent = $this->db->getRow(array('id_resource'=>$id_resource_parent));

        $data =  array(


            'id_user'               => $user['id_user'],
            'name'                  => $user['name'] ,
            'id_resource'           => $user['id_resource'],


            'id_user_parent'        => $user_parent['id_user'],
            'id_resource_parent'    => $user_parent['id_resource'],
            'name_parent'           => $user_parent['name'],


            'type'                  => 'onMessage',

            'date'=>                date("Y-m-d H:i:s"),
            'message'               => $message,

        );

        $this->db->onInsert($data);

        return true;
    }
}