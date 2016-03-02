<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Franchise\Controller\Index' => 'Franchise\Controller\IndexController',
            'Franchise\Controller\Login' => 'Franchise\Controller\LoginController',
            'Franchise\Controller\Orders' => 'Franchise\Controller\OrdersController',
            'Franchise\Controller\Invoice' => 'Franchise\Controller\InvoiceController',
            'Franchise\Controller\Products' => 'Franchise\Controller\ProductsController',
            'Franchise\Controller\Operator' => 'Franchise\Controller\OperatorController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'franchise' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/franchise[/:controller][/:action][/:id]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Franchise\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Franchise' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'module_layouts' => array(
        'Franchise' => 'layout/franchise'
    ),
    'controller_layouts' => array(
        'Franchise/Login' => 'layout/franchise_login'
    ),
);
