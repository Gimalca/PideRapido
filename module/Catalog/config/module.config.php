<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Catalog\Controller\Index' => 'Catalog\Controller\IndexController',
            'Catalog\Controller\Branch' => 'Catalog\Controller\BranchController',
            'Catalog\Controller\Franchise' => 'Catalog\Controller\FranchiseController',
            'Catalog\Controller\Cart' => 'Catalog\Controller\CartController',
            'Catalog\Controller\Info' => 'Catalog\Controller\InfoController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'catalog' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/catalog[/:controller][/:action][/:id]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Catalog\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Catalog' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);