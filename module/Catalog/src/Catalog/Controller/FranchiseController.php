<?php

namespace Catalog\Controller;

use Catalog\Form\FranchiseSearch;
use Franchise\Model\Dao\FranchiseDao;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FranchiseController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function listAction()
    {
        $request = $this->getRequest();

        $franchiseTableGateway = $this->getService('FranchiseTableGateway');
        $franchiseDao = new FranchiseDao($franchiseTableGateway);


        $category = $this->getFranchiseCategorySelect();
        $category[0] = 'Todas';
        $franchiseAddForm = new FranchiseSearch();
        $franchiseAddForm->get('category_id')
                ->setValueOptions($category)
                ->setValue(0);


        $franchies = $franchiseDao->getAll();


        if ($request->isPost()) {

            $category_id = $request->getPost('category_id');
            $name = $request->getPost('name');


            if ($category_id > 0) {
                $franchies->where(array('category_id' => $category_id));
                $franchiseAddForm->get('category_id')->setValue($category_id);
            }elseif($name){
                $franchies->like('name', $name);
            }

        }
            $franchiesResult = $franchies->fetchAll();
            //$franchies->count();die;
            $view['form'] = $franchiseAddForm;
            $view['franchieses'] = $franchiesResult;
            //print_r($franchiesResult);die;
            return new ViewModel($view);
       
    }

    private function getFranchiseCategorySelect()
    {
        $franchiseCategoryDao = $this->getService('FranchiseCategoryDao');
        $results = $franchiseCategoryDao->getAllCategory();

        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->category_id] = $bankR->name;
        }
        return $result;
    }

    public function getService($serviceName)
    {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

}
