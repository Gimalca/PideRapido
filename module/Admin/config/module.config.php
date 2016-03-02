<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Admin\Controller\Index' => 'Admin\Controller\IndexController',
            'Admin\Controller\Franchise' => 'Admin\Controller\FranchiseController',
            'Admin\Controller\Product' => 'Admin\Controller\ProductController',
            'Admin\Controller\Customer' => 'Admin\Controller\CustomerController',
            'Admin\Controller\Branch' => 'Admin\Controller\BranchController',
            'Admin\Controller\Prodcut' => 'Admin\Controller\ProdcutController',
            'Admin\Controller\Option' => 'Admin\Controller\OptionController',
            'Admin\Controller\Operator' => 'Admin\Controller\OperatorController',
            'Admin\Controller\Login' => 'Admin\Controller\LoginController',
            'Admin\Controller\Invoice' => 'Admin\Controller\InvoiceController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin[/:controller][/:action][/:id][/:idd]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'idd' => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Admin' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'module_layouts' => array(
        'Admin' => 'layout/admin'
    ),
    'controller_layouts' => array(
        'Admin/Login' => 'layout/admin_login'
    ),
    
);
