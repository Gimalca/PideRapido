<?php

namespace Admin;

use Admin\Form\LoginAdminForm;
use Admin\Model\LoginAdmin;
use Admin\Model\Entity\User;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;

class Module {

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e) {
        $this->initLogin($e);
    }

    public function init(ModuleManager $moduleManager) {
        $moduleName = $moduleManager->getEvent()->getModuleName();
        if ($moduleName == 'Admin') {
            $events = $moduleManager->getEventManager();
            $sharedEvents = $events->getSharedManager();
            $sharedEvents->attach(array(__NAMESPACE__, 'Admin'), 'dispatch', array($this, 'initAuth'), 100);
        }
    }

    public function initAuth(MvcEvent $e) {

        $app = $e->getApplication();
        $routerMatch = $e->getRouteMatch();
        $module = $routerMatch->getMatchedRouteName();
        $controller = $routerMatch->getParam('controller');
        $action = $routerMatch->getParam('action');

        $sm = $app->getServiceManager();
        $auth = $sm->get('Admin\Model\LoginAdmin');


        if ($controller != 'Admin\Controller\Login'  && !$auth->isLoggedIn()) {
            $controller = $e->getTarget();
//            var_dump($module);die;
            return $controller->redirect()->toRoute('admin',array('controller'=>'login','action' => 'index'));
        }


        if ($auth->isLoggedIn()) {

            $viewModel = $e->getViewModel();
            $viewModel->userIdentity = $auth->getIdentity();
        }
    }

    protected function initLogin($e) {
        $viewModel = $e->getViewModel();
        $viewModel->loginForm = new LoginAdminForm();
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Admin\Model\LoginAdmin' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new LoginAdmin($dbAdapter);
                },
                'UserAdminTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('pr_user', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
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
