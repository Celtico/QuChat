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

    public function listAction()
    {
        $qu_chat_model = $this->getServiceLocator()->get('qu_chat_mapper');
        $model = new ViewModel();
        $model->setVariables(array(
                'listUsers'=> $qu_chat_model->getAll(array('type'=>'listUsers'))));
        $model->setTemplate('qu-chat/qu-chat/list');
        return  $model->setTerminal(true);
    }
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



    public function openAction()
    {
        $post = $this->getEvent()->getRequest()->getPost();
        $this->getUser($post['id_resource']);
        $this->insertNewUser($post['id_resource']);
        $this->saveListUsers($post['clients']);
        $model = new ViewModel();
        return  $model->setTerminal(true);
    }

    public function messageAction()
    {
        $post = $this->getEvent()->getRequest()->getPost();

        $this->getUser($post['id_user']);
        $this->saveMessageUser($post['id_resource_parent'],$post['message']);

        $model = new ViewModel();
        return  $model->setTerminal(true);
    }


    /**
     * @param $id_resource
     * @return mixed
     */
    protected function insertNewUser($id_resource){

        $this->db = $this->getServiceLocator()->get('qu_chat_mapper');

        $data =  array(
            'id_user'    =>$this->id_user,
            'name'       =>$this->name ,
            'message'    => 'updateNewUser',
            'id_resource'=> $id_resource,
            'date'=>date("Y-m-d H:i:s")
        );

        return $this->db->onInsert($data);
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

        $users = array();
        $clients = explode(',',str_replace(',|','',$clients.'|'));
        foreach($clients as $client){

            $data =  array( 'id_resource'=>$client );
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
     * @param $id_resource_parent
     * @param $message
     * @return bool
     */
    protected function saveMessageUser($id_resource_parent,$message) {

        $this->db = $this->getServiceLocator()->get('qu_chat_mapper');

        $user        = $this->db->getRow(array('id_user'=>$this->id_user));
        $user_parent = $this->db->getRow(array('id_resource'=>$id_resource_parent));

        $data =  array(


            'id_user'               =>$this->id_user,
            'name'                  =>$this->name ,
            'id_resource'           =>$user['id_resource'],


            'id_user_parent'        =>$user_parent['id_user'],
            'id_resource_parent'    =>$user_parent['id_resource'],
            'name_parent'           =>$user_parent['name'],


            'type'                  =>'onMessage',

            'date'=>                date("Y-m-d H:i:s"),
            'message'               =>$message,

        );

        $this->db->onInsert($data);

        return true;
    }

    public function getUser($id_resource)
    {
        $zfcUser =  $this->getServiceLocator()->get('zfcuser_auth_service')->getIdentity();

        if(method_exists($zfcUser,'getId'))
        {
            $this->id_user = $zfcUser->getId();
            $this->name    = $zfcUser->getDisplayName();

        }else{

            $this->id_user = $id_resource;
            $this->name    = $id_resource;
        }
        return $this;
    }

}