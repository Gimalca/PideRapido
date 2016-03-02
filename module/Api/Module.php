<?php
namespace Api;

use Api\Model\Dao\LocationDao;
use Api\Model\Entity\Country;
use Api\Model\Entity\Municipality;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
 

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
 
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'CountryTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Country());
                    return new TableGateway('pr_country', $dbAdapter, null, $resultSetPrototype);
                },
                'MunicipalityTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Municipality());
                    return new TableGateway('pr_municipality', $dbAdapter, null, $resultSetPrototype);
                },
                'CountryDao' => function($sm) {
                    $tableGateway = $sm->get('CountryTableGateway');
                    $table = new LocationDao($tableGateway);
                    return $table;
                },
            )
        );
    }
}