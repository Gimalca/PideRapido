<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Account\Controller\Index' => 'Account\Controller\IndexController',
            'Account\Controller\Register' => 'Account\Controller\RegisterController',
            'Account\Controller\Information' => 'Account\Controller\InformationController',
            'Account\Controller\Login' => 'Account\Controller\LoginController',
            'Account\Controller\OrderHistory' => 'Account\Controller\OrderHistoryController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'account' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/account[/:controller][/:action[/:id]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Account\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Account' => __DIR__ . '/../view',
        ),
    ),

);
