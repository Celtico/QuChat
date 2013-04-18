<?php
/**
 * @Author: Cel Ticó Petit
 * @Contact: cel@cenics.net
 * @Company: Cencis s.c.p.
 */

namespace QuChat\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

class QuChatController extends AbstractActionController
{
    public function indexAction()
    {
    }
    public function listAction()
    {
        $qu_chat_model = $this->getServiceLocator()->get('qu_chat_mapper');
        $model = new ViewModel();
        $model->setVariables(array(
                'listUsers'=> $qu_chat_model->getAll(array('type'=>'listUsers'))
            )
        );
        $model->setTemplate('qu-chat/qu-chat/list');
        return  $model->setTerminal(true);
    }
    public function messagesAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id',0);
        $qu_chat_model = $this->getServiceLocator()->get('qu_chat_mapper');
        $model = new ViewModel();
        $model->setVariables(array(
                /* where = 0 general*/
                'messages'=> $qu_chat_model->getAll(array('type'=>'onMessage')),
                'id_user'=>$id
            )
        );
        $model->setTemplate('qu-chat/qu-chat/messages');
        return  $model->setTerminal(true);
    }
}