<?php
return array(
    'controllers' => array(
        'invokables' => array(         
            'Payment\Controller\Index' => 'Payment\Controller\IndexController',
            'Payment\Controller\Checkout' => 'Payment\Controller\CheckoutController',
       
          
        ),
    ),
    'router' => array(
        'routes' => array(
            'payment' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/payment[/:controller][/:action[/:id]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Payment\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Payment' => __DIR__ . '/../view',
        ),
    ),
  
);