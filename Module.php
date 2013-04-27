<?php
/**
 * @Author: Cel Ticó Petit
 * @Contact: cel@cenics.net
 * @Company: Cencis s.c.p.
 */

namespace QuChat;

use QuChat\Db\Adapter\DbAdapterAwareInterface;
use Zend\Db\Adapter\Adapter;

class Module
{

    public function getServiceConfig()
    {
        return array(
            'initializers' => array(
                function($instance, $sm){
                    if($instance instanceof DbAdapterAwareInterface){



                                                $instance->setDbAdapter(new Adapter(array(
                                                    'driver'         => 'Pdo_Mysql',
                                                    'hostname'       => 'localhost',
                                                    'dbname'         => 'qumodules',
                                                    'username'       => 'root',
                                                    'password'       => 'estacio8'
                                                )));



                    }
                },
            ),
            'factories' => array(
                'qu_chat_mapper' =>  function($sm) {
                    return  new \QuChat\Mapper\QuChatMapper();
                },
            )
        );
    }

    public function getConfig()
    {
        $config = array();
        $configFiles = array(
            __DIR__ . '/config/module.config.php',
        );
        foreach($configFiles as $configFile) {
            $config = \Zend\Stdlib\ArrayUtils::merge($config, include $configFile);
        }
        return $config;
    }
    public function getAutoloaderConfig()
    {  
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
