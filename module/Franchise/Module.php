<?php
namespace Franchise;
use Franchise\Form\LoginFranchiseForm;
use Franchise\Model\Dao\BranchDao;
use Franchise\Model\Dao\FranchiseDao;
use Franchise\Model\Entity\Branch;
use Franchise\Model\Entity\BranchContact;
use Franchise\Model\Entity\Franchise;
use Franchise\Model\Entity\FranchiseCategory;
use Franchise\Model\Entity\Franquimovil;
use Franchise\Model\Entity\Operator;
use Franchise\Model\Entity\ProductHasBranch;
use Franchise\Model\LoginFranchise;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\Hydrator\ObjectProperty;
class Module implements AutoloaderProviderInterface {
    public function onBootstrap(MvcEvent $e) {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $this->initLogin($e);
    }
    public function init(ModuleManager $moduleManager) {
        $moduleName = $moduleManager->getEvent()->getModuleName();
        if ($moduleName == 'Franchise') {
            $events = $moduleManager->getEventManager();
            $sharedEvents = $events->getSharedManager();
            $sharedEvents->attach(array(__NAMESPACE__, 'Franchise'), 'dispatch', array($this, 'initAuth'), 100);
        }
    }
    public function initAuth(MvcEvent $e) {
        $app = $e->getApplication();
        $routerMatch = $e->getRouteMatch();
        $module = $routerMatch->getMatchedRouteName();
        $controller = $routerMatch->getParam('controller');
        $action = $routerMatch->getParam('action');
        $sm = $app->getServiceManager();
        $auth = $sm->get('Franchise\Model\LoginFranchise');
        if ($controller != 'Franchise\Controller\Login' && !$auth->isLoggedIn()) {
            $controller = $e->getTarget();
//            var_dump($module);die;
            return $controller->redirect()->toRoute('franchise', array('controller' => 'login', 'action' => 'index'));
        }
        if ($auth->isLoggedIn()) {
            $viewModel = $e->getViewModel();
            $viewModel->userIdentity = $auth->getIdentity();
        }
    }
    protected function initLogin($e) {
        $viewModel = $e->getViewModel();
        $viewModel->loginForm = new LoginFranchiseForm();
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
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Franchise\Model\LoginFranchise' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new LoginFranchise($dbAdapter);
                },
                'FranchiseTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Franchise());
                    return new TableGateway('pr_franchise', $dbAdapter, null, $resultSetPrototype);
                },
                'FranchiseDao' => function($sm) {
                    $tableGateway = $sm->get('FranchiseTableGateway');
                    $table = new FranchiseDao($tableGateway);
                    return $table;
                },
                'FranchiseCategoryTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new FranchiseCategory());
                    return new TableGateway('pr_franchise_category', $dbAdapter, null, $resultSetPrototype);
                },
                'FranchiseCAtegoryDao' => function($sm) {
                    $tableGateway = $sm->get('FranchiseCategoryTableGateway');
                    $table = new FranchiseDao($tableGateway);
                    return $table;
                },
                'BranchTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Branch());
                    return new TableGateway('pr_branch', $dbAdapter, null, $resultSetPrototype);
                },
                'BranchHydratingTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new HydratingResultSet();
                    $resultSetPrototype->setHydrator(new ObjectProperty());
                    $resultSetPrototype->setObjectPrototype(new Branch());
                    return new TableGateway('pr_branch', $dbAdapter, null, $resultSetPrototype);
                },
                'OperatorTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Operator());
                    return new TableGateway('pr_operator', $dbAdapter, null, $resultSetPrototype);
                },
                'BranchDao' => function ($sm) {
                    $tableGateway = $sm->get('BranchTableGateway');
                    $table = new BranchDao($tableGateway);
                    return $table;
                },
                'FranquimovilTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Franquimovil());
                    return new TableGateway('pr_franquimovil', $dbAdapter, null, $resultSetPrototype);
                },
                'BranchContactTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new BranchContact());
                    return new TableGateway('pr_branch_contact', $dbAdapter, null, $resultSetPrototype);
                },
                'BranchHasProductTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ProductHasBranch());
                    return new TableGateway('pr_product_has_branch', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}