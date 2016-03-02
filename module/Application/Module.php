<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Locale;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

class Module {

    public function onBootstrap(MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        //Run Layouts
        $app = $e->getApplication()->getEventManager();
        $app->attach('dispatch', array($this, 'initLayout'), -100);
        
        //Run Locale Zone, Tranlate And Time
        $this->initEnviroment($e); 
    }
    
     protected function initEnviroment($e) {
        error_reporting(E_ALL | E_STRICT);
        ini_set("display_errors", TRUE);

      
        $timeZone = 'America/Caracas';
        date_default_timezone_set($timeZone);

        //Translator Formulario
        $serviceManager = $e->getApplication()->getServiceManager();
        $translator = $serviceManager->get('translator');

        $locale = 'es_ES';
        $translator->setLocale(Locale::acceptFromHttp($locale));
        $translator->addTranslationFile(
                'phpArray', __DIR__ . '/language/es/Zend_Validate.php', 'default', 'es_ES'
        );
        
        AbstractValidator::setDefaultTranslator($translator);
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function initLayout($e) {
        $routeMatchParams = array();

        $routeMatchParams = $e->getRouteMatch()->getParams();

        (!isset($routeMatchParams['__NAMESPACE__'])) ? $routeMatchParams['__NAMESPACE__'] = "Application\Controller" : NULL;
        $moduleName = substr($routeMatchParams['__NAMESPACE__'], 0, strpos($routeMatchParams['__NAMESPACE__'], '\\'));
        $controllerName = str_replace('\\Controller\\', '/', $routeMatchParams['controller']);
        $actionName = $routeMatchParams['action'];

        $config = $e->getApplication()->getServiceManager()->get('config');
        $controller = $e->getTarget();
        //print_r($config['controller_layouts']);die;
        
        if (isset($config['module_layouts'][$moduleName])) {
            $controller->layout($config['module_layouts'][$moduleName]);
        }
        if (isset($config['controller_layouts'][$controllerName])) {
            $controller->layout($config['controller_layouts'][$controllerName]);
        }
        if (isset($config['action_layouts'][$controllerName . '/' . $actionName])) {
            $controller->layout($config['action_layouts'][$controllerName . '/' . $actionName]);
        }
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Mailer' => function ($sm) {
                    $config = $sm->get('Config');
                    //print_r($config);die;
                    $transport = new Smtp();
                    $transport->setOptions(new SmtpOptions($config['mail']['transport']['options']));
                    return $transport;
                },
            )
        );
    }

}
