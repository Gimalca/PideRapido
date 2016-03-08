<?php
namespace Account;

use Account\Form\LoginForm;
use Account\Form\RegisterForm;
use Account\Model\LoginAccount;
use Account\Model\LoginFacebook;
use Api\Model\Dao\LocationDao;
use Api\Model\Entity\City;
use Api\Model\Entity\Country;
use Api\Model\Entity\Municipality;
use Api\Model\Entity\State;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;


class Module {
    public function onBootstrap(MvcEvent $e) {
  
        $this->initRegister($e);
        $this->initLogin($e);
        
    }
    public function init(ModuleManager $moduleManager) {
        $moduleName = $moduleManager->getEvent()->getModuleName();
        if ($moduleName == 'Account') {
            $events = $moduleManager->getEventManager();
            $sharedEvents = $events->getSharedManager();
            $sharedEvents->attach(array(__NAMESPACE__, 'Application', 'Account', 'Catalog', 'Sale', 'Payment'), 'dispatch', array($this, 'initAuth'), 100);
        }
    }
    public function initAuth(MvcEvent $e) {
        $app = $e->getApplication();
        $routerMatch = $e->getRouteMatch();
        $module = $routerMatch->getParam('__NAMESPACE__');
        
        $controller = $routerMatch->getParam('controller');
        $action = $routerMatch->getParam('action');
        $sm = $app->getServiceManager();
        $auth = $sm->get('Account\Model\LoginAccount');
        
        if ( ($controller === 'Account\Controller\Index' || $controller === 'Account\Controller\OrderHistory') && !$auth->isLoggedIn()) {
            $controller = $e->getTarget();
            
            $app->getServiceManager();
            $flash = $sm->get('ControllerPluginManager')->get('flashMessenger');
            $flash->addMessage("Acceso denegado, debe iniciar sesión", 'error');
            
            return $controller->redirect()->toRoute('account', array('controller' => 'register', 'action' => 'add'));
        }
        if ($auth->isLoggedIn()) {
            $viewModel = $e->getViewModel();
            $viewModel->userIdentity = $auth->getIdentity();
        }
        
        if($module !== 'Account\Controller' || $controller === 'Account\Controller\Register' ){
           
            $viewModel = $e->getViewModel();
            $viewModel->fbUrl = $sm->get('FacebookUrl');
        }
    }
    protected function initRegister($e) {
        $viewModel = $e->getViewModel();
        $viewModel->registerForm = new RegisterForm();
    }
    protected function initLogin($e) {
        $viewModel = $e->getViewModel();
        $viewModel->loginForm = new LoginForm();
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Account\Model\LoginAccount' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new LoginAccount($dbAdapter);
                },      
                'FacebookUrl' => function ($sm) {
                    $globalConfig = $sm->get('config');
                    $fb = new LoginFacebook($globalConfig['fbapi']);
                    $fbUrl = $fb->loginUrl($globalConfig['fbapi']['fbcallback']);
                    return $fbUrl;
                },      
                'CountryTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Country());
                    return new TableGateway('pr_country', $dbAdapter, null, $resultSetPrototype);
                },
                'CountryDao' => function($sm) {
                    $tableGateway = $sm->get('CountryTableGateway');
                    $table = new LocationDao($tableGateway);
                    return $table;
                },
                'StateTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new State());
                    return new TableGateway('pr_state', $dbAdapter, null, $resultSetPrototype);
                },
                'StateDao' => function($sm) {
                    $tableGateway = $sm->get('StateTableGateway');
                    $table = new LocationDao($tableGateway);
                    return $table;
                },
                'MunicipalityTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Municipality());
                    return new TableGateway('pr_municipality', $dbAdapter, null, $resultSetPrototype);
                },
                'MunicipalityDao' => function($sm) {
                    $tableGateway = $sm->get('MunicipalityTableGateway');
                    $table = new LocationDao($tableGateway);
                    return $table;
                },
                'CityTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new City());
                    return new TableGateway('pr_city', $dbAdapter, null, $resultSetPrototype);
                },
                'CityDao' => function($sm) {
                    $tableGateway = $sm->get('CityTableGateway');
                    $table = new LocationDao($tableGateway);
                    return $table;
                },
            ),
        );
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
}