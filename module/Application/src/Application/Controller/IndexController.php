<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Catalog\Model\Dao\ProductDao;
use Franchise\Model\Dao\FranchiseDao;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        
        $this->layout()->fbUrl = $this->getService('FacebookUrl');
   
        $this->layout()->slider = 1;
        $this->layout()->parallax = 1;
        $this->layout()->featured = 1;
        $this->layout()->cssAuto = 1;
        $this->layout()->parallax_two = 0;
        
        $products = $this->params()->fromRoute('products',false);
        $cart = Json::encode($products);
//        var_dump($products);die;
        $this->layout()->productos = $cart;
        $request = $this->getRequest();

        $productDao = new ProductDao($this->getService('ProductHasBranchHydratingTableGateway'));
        $lastProduct = $productDao->getAll()
                ->whithProduct()
                ->whithFranchise(['status_franchise' => 'status'])                 
                ->whithBranch(['status_branch' => 'status'])
                ->where(array(
                    'pr_product.type' => 2,
                    'pr_product.status' => '1',
                    'f.status' => '1',
                    'b.status' => '1'
                ))
                ->whithLimit(8)
                //->getSqlString()
                ->fetchAll();
        $this->layout()->lastProduct = $lastProduct;
        //print_r($lastProduct->current());die;
        
        $franchiseTableGateway = $this->getService('FranchiseTableGateway');
        $franchiseDao = new FranchiseDao($franchiseTableGateway);
    
        $franchies = $franchiseDao->getLast();
        $view['franchieses'] = $franchies;
        $view['franchieses'] = $franchies;

        return new ViewModel($view);
        
    }
    
    
    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }
}
