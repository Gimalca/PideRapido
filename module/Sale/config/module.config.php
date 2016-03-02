<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Sale\Controller\Checkout' => 'Sale\Controller\CheckoutController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'sale' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/sale[/:controller][/:action][/:id]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Sale\Controller',
                        'controller' => 'checkout',
                        'action' => 'cartDetail',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Sale' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);