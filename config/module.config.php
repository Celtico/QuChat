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
                    'route' => '[/:lang]/chat[/:action][/:id][/:id_parent]',
                    'constraints' => array(
                        'lang'      => '[a-z]{2}(-[A-Z]{2}){0,1}',
                        'action'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'        => '[0-9]+',
                        'id_parent' => '[0-9]+',
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
     * QuAdmin Navigation
     *
     *
     */
    'navigation' => array(
        'qu_admin_navigation' => array(
            'system' => array(
                'pages' => array(
                    'qu_chat' => array(
                        'order' =>3,
                        'icon'   =>'&#xe1f0',
                        'label' => 'Chat',
                        'route' => 'qu_chat_route',
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
            'QuChat' => true
        ),
    ),


    /**
     *
     *
     * Configuration QuAdmin model and forms managing fast :P !!!!!!!!!!!!
     *
     *
     */
    'qu_chat_model'=>array(
        /**
         *
         *
         * MODEL
         *
         *
         */

        'tableName'            => 'qu-chat',
        'tableLabel'           => 'QuAdminDemo',
        'entity'               => 'QuChat\Entity\QuChat',
        'tableKeyFields'       => array(

            'key_id'            => 'id_chat',
            'key_date'          => 'date',
            'key_title'         => 'title',
            'key_name'          => 0,
            'key_id_author'     => 0,
            'key_status'        => 0,
            'key_order'         => 0,

            'key_id_parent'     => 0,
            'key_id_lang'       => 0,
            'key_lang'          => 0,

            'key_modified'      => 0,
            'key_level'         => 0,
            'key_path'          => 0,
            'key_icon'          => 0,
        ),

        'tableFieldsCleanData'=> array(
            'id_chat',
            'id_user',
            'id_resource',
            'type',
            'date',
            'name',
            'message',
        ),

        'linkerModels'      => 0,
        'linkerParent'      => 0,
        'optionsPaginator'  => array('n'=>10,'p'=>1),
        'defaultLanguage'   => 0,
        'optionsOrder'      => 0,

        'documents'         => 0,
        /**
         *
         *
         * FORM
         *
         */
        'optionsForm' => 0,
    )

);