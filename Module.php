<?php
/**
 * @Author: Cel Ticó Petit
 * @Contact: cel@cenics.net
 * @Company: Cencis s.c.p.
 */

namespace QuChat;

use QuChat\Db\Adapter\DbAdapterAwareInterface;

class Module
{

    public function getServiceConfig()
    {
        return array(
            'initializers' => array(
                function($instance, $sm){
                    if($instance instanceof DbAdapterAwareInterface){
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $instance->setDbAdapter($dbAdapter);
                    }
                },
            ),
            'factories' => array(
                'qu_chat_model' => function ($sm) {
                    $config = $sm->get('Config');
                    return new \QuAdmin\Options\QuAdminModelOptions($config['qu_chat_model']);
                },
                'qu_chat_mapper' =>  function($sm) {
                    $QuWebDemoMapper =  new \QuChat\Model\ChatMapper();
                    $QuWebDemoMapper->setQuAdminModelOptions($sm->get('qu_chat_model'));
                    return $QuWebDemoMapper;
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
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}