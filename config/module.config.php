<?php
/**
 * @Author: Cel Ticó Petit
 * @Contact: cel@cenics.net
 * @Company: Cencis s.c.p.
 */
return array(


    'controllers' => array(
        'invokables' => array(
            'qu_chat_controller' => 'QuChat\Controller\QuChatController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'router' => array(
        'routes' => array(
            'qu_chat_route' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '[/:lang]/chat[/:action][/:id][/:id_parent][/:message]',
                    'constraints' => array(
                        'lang'      => '[a-z]{2}(-[A-Z]{2}){0,1}',
                        'action'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'        => '[0-9]+',
                        'id_parent' => '[0-9]+',
                        'message'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'lang'          => 'es',
                        'controller'    => 'qu_chat_controller',
                        'action'        => 'index',
                    ),
                ),
            ),
        ),
    ),


    /**
     *
     *
     * QuAdmin Strategy
     *
     *
     */
    'QuAdminConfig' => array(
        // NAMESPACE Layout Module
        'QuLayout' => array(
            'QuChat' => 'qu-admin/layout/qu-admin-layout'
        ),

        // NAMESPACE Base Path Module
        'QuBasePath' => array(
            'QuChat' => '/qu-admin'
        ),

        // NAMESPACE Redirect login
        'QuRedirectLogin' => array(
            //'QuChat' => true
        ),
    ),

);