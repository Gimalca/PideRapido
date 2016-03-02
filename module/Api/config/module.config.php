<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Api\Controller\State' => 'Api\Controller\StateController',
            'Api\Controller\Municipality' => 'Api\Controller\MunicipalityController',
            'Api\Controller\City' => 'Api\Controller\CityController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'api' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api[/:controller][/:id]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Api\Controller',
                        'controller' => 'Index',
                        
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
        'template_path_stack' => array(
            'api' => __DIR__ . '/../view',
        ),
    ),
    'module_layouts' => array(
        'Api' => 'layout/empty',
    ),
);
