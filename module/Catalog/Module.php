<?php

namespace Catalog;

use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ObjectProperty;
use Zend\ModuleManager\ModuleManager;
use Catalog\Model\Entity\Category;
use Catalog\Model\Entity\Clasification;
use Catalog\Model\Entity\Combo;
use Catalog\Model\Entity\ComboHasProduct;
use Catalog\Model\Entity\Option;
use Catalog\Model\Entity\OptionValue;
use Catalog\Model\Entity\Product;
use Catalog\Model\Entity\ProductOption;
use Catalog\Model\Entity\ProductOptionValue;
use Catalog\Model\Entity\Type;
use Franchise\Model\Entity\Branch;
use Franchise\Model\Entity\BranchHasCombo;
use Franchise\Model\Entity\ProductHasBranch;
use Sale\Model\Entity\Customer;
use Sale\Model\Dao\CartDao;

class Module {

    public function onBootstrap(MvcEvent $e) {
        //    $this->initCart($e);
    }

    public function init(ModuleManager $moduleManager) {
        $moduleName = $moduleManager->getEvent()->getModuleName();
        $events = $moduleManager->getEventManager();
        $sharedEvents = $events->getSharedManager();
        $sharedEvents->attach(array(__NAMESPACE__, 'Application', 'Account', 'Catalog', 'Sale', 'Payment'), 'dispatch', array($this, 'initCart'), 100);
    }

    public function initCart(MvcEvent $e) {
        $app = $e->getApplication();
        $sm = $app->getServiceManager();
        $auth = $sm->get('Account\Model\LoginAccount');

        if ($auth->isLoggedIn()) {

            $productHasBranch = $sm->get('ProductHasBranchHydratingTableGateway');
            $cartDao = new CartDao($productHasBranch);
            $user = $auth->getIdentity();
            $viewModel = $e->getViewModel();
            $viewModel->shoppingCart = $cartDao->cart($user);
        } else {
            // Get Cart from Session Namespace
        }
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
                'ResultSet' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return function ($tableName, $prototype) use ($dbAdapter) {
                                $resultSetPrototype = new ResultSet();
                                $resultSetPrototype->setArrayObjectPrototype($prototype);
                                return new TableGateway($tableName, $dbAdapter, null, $resultSetPrototype);
                            };
                },
                'HydratingResultSet' => function ($sm) {
                    return function ($tableName, $prototype) use ($sm) {
                                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                                $resultSetPrototype = new HydratingResultSet();
                                $resultSetPrototype->setHydrator(new ObjectProperty());
                                $resultSetPrototype->setObjectPrototype($prototype);
                                return new TableGateway($tableName, $dbAdapter, null, $resultSetPrototype);
                            };
                },
                'ProductTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Product());
                    return new TableGateway('pr_product', $dbAdapter, null, $resultSetPrototype);
                },
                'ProductHydratingTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new HydratingResultSet();
                    $resultSetPrototype->setHydrator(new ObjectProperty());
                    $resultSetPrototype->setObjectPrototype(new Product());
                    return new TableGateway('pr_product', $dbAdapter, null, $resultSetPrototype);
                },
                'CategoryTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Category());
                    return new TableGateway('pr_product_category', $dbAdapter, null, $resultSetPrototype);
                },
                'ComboTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Combo());
                    return new TableGateway('pr_combo', $dbAdapter, null, $resultSetPrototype);
                },
                'ComboHydratingTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new HydratingResultSet();
                    $resultSetPrototype->setHydrator(new ObjectProperty());
                    $resultSetPrototype->setObjectPrototype(new Combo());
                    return new TableGateway('pr_combo', $dbAdapter, null, $resultSetPrototype);
                },
                'ComboHasProductHydratingTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new HydratingResultSet();
                    $resultSetPrototype->setHydrator(new ObjectProperty());
                    $resultSetPrototype->setObjectPrototype(new BranchHasCombo());
                    return new TableGateway('pr_combo_has_branch', $dbAdapter, null, $resultSetPrototype);
                },
                'ProductHasBranchHydratingTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new HydratingResultSet();
                    $resultSetPrototype->setHydrator(new ObjectProperty());
                    $resultSetPrototype->setObjectPrototype(new ProductHasBranch());
                    return new TableGateway('pr_product_has_branch', $dbAdapter, null, $resultSetPrototype);
                },
                'ProductHasBranchTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ProductHasBranch());
                    return new TableGateway('pr_product_has_branch', $dbAdapter, null, $resultSetPrototype);
                },
                'ProductOptionValueTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ProductOptionValue());
                    return new TableGateway('pr_product_option_value', $dbAdapter, null, $resultSetPrototype);
                },
                'ComboHasBranchTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new BranchHasCombo());
                    return new TableGateway('pr_combo_has_branch', $dbAdapter, null, $resultSetPrototype);
                },
                'ComboHasProductTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ComboHasProduct());
                    return new TableGateway('pr_product_has_combo', $dbAdapter, null, $resultSetPrototype);
                },
                'OptionTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Option());
                    return new TableGateway('pr_option', $dbAdapter, null, $resultSetPrototype);
                },
                'OptionHydratingTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new HydratingResultSet();
                    $resultSetPrototype->setHydrator(new ObjectProperty());
                    $resultSetPrototype->setObjectPrototype(new Option());
                    return new TableGateway('pr_option', $dbAdapter, null, $resultSetPrototype);
                },
                'OptionValueTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OptionValue());
                    return new TableGateway('pr_option_value', $dbAdapter, null, $resultSetPrototype);
                },
                'TypeTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Type());
                    return new TableGateway('pr_option_type', $dbAdapter, null, $resultSetPrototype);
                },
                'ClasificationTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Clasification());
                    return new TableGateway('pr_option_clasification', $dbAdapter, null, $resultSetPrototype);
                },
                'ProductOptionTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ProductOption());
                    return new TableGateway('pr_product_option', $dbAdapter, null, $resultSetPrototype);
                },
                'ProductOptionHydratingTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new HydratingResultSet();
                    $resultSetPrototype->setHydrator(new ObjectProperty());
                    $resultSetPrototype->setObjectPrototype(new ProductOption());
                    return new TableGateway('pr_product_option', $dbAdapter, null, $resultSetPrototype);
                },
                'UserTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Customer());
                    return new TableGateway('pr_customer', $dbAdapter, null, $resultSetPrototype);
                },
                'ContactTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    //$resultSetPrototype->setArrayObjectPrototype();
                    return new TableGateway('pr_contact', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

}
