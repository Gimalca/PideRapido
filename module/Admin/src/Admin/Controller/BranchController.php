<?php

namespace Admin\Controller;

use Admin\Form\BranchAdd;
use Admin\Form\BranchAddProduct;
use Admin\Form\BranchList;
use Admin\Form\FranchiseList;
use Admin\Form\Validator\BranchAddProductValidator;
use Admin\Form\Validator\BranchAddValidator;
use Catalog\Model\Dao\ProductDao;
use Franchise\Model\Dao\BranchDao;
use Franchise\Model\Dao\FranchiseDao;
use Franchise\Model\Entity\Branch;
use Franchise\Model\Entity\BranchContact;
use Franchise\Model\Entity\ProductHasBranch;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class BranchController extends AbstractActionController {

    public function indexAction() {
        return new ViewModel();
    }

    public function listFranchiseAction() {
        $request = $this->getRequest();
        $franchiseListSelect = new FranchiseList();
        
         $postData = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
            @$franchise_id = $postData['franchise_id'];

        if ($request->isPost() && $franchise_id != '0') {
           
//                var_dump($product_id);die;
            $franchiseList = $this->getFranchiseSelect();
            $franchiseList['0'] = 'Todas';
            $franchiseListSelect->get('franchise_id')->setValueOptions($franchiseList);
            $franchiseListSelect->get('franchise_id')->setValue('0');

            $franchiseTableGateway = $this->getService('FranchiseTableGateway');
            $franchiseDao = new FranchiseDao($franchiseTableGateway);

            $franchies = $franchiseDao->getFranchiseSearch($franchise_id);
//            var_dump($franchies);die;
            $view['franchieses'] = $franchies;
            $view['franchies_list'] = $franchiseListSelect;
            return new ViewModel($view);
        } else {

            $franchiseTableGateway = $this->getService('FranchiseTableGateway');
            $franchiseDao = new FranchiseDao($franchiseTableGateway);

            $franchies = $franchiseDao->getAll()
                    ->fetchAll();
            $franchiseList = $this->getFranchiseSelect();
             $franchiseList['0'] = 'Todas';
            $franchiseListSelect->get('franchise_id')->setValueOptions($franchiseList);
            $franchiseListSelect->get('franchise_id')->setValue('0');
            $view['franchieses'] = $franchies;
            $view['franchies_list'] = $franchiseListSelect;
            //print_r($view);die;
            return new ViewModel($view);
        }
    }

    public function listAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id', 0);
        $branchListSelect = new BranchList();
        
         $postData = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
            @$branch_id = $postData['branch_id'];

        if ($request->isPost() && $branch_id != '0') {
           
//                var_dump($product_id);die;
            $branchList = $this->getBranchSelect($id);
            
            $branchList['0'] = 'Todas';
            $branchListSelect->get('branch_id')->setValueOptions($branchList);
            $branchListSelect->get('branch_id')->setValue('0');

            $branchTableGateway = $this->getService('BranchTableGateway');
            $branchDao = new BranchDao($branchTableGateway);
            $branch = $branchDao->getBranchSelect($branch_id);
//            var_dump($branch);die;
            
            $view['branch'] = $branch;
            $view['branch_list'] = $branchListSelect;
            $view['frnachise_id'] = $id;
            return new ViewModel($view);
        } else {
            
            $branchList = $this->getBranchSelect($id);
            
            $branchList['0'] = 'Todas';
            $branchListSelect->get('branch_id')->setValueOptions($branchList);
            $branchListSelect->get('branch_id')->setValue('0');
            
            $branchTableGateway = $this->getService('BranchTableGateway');
            $branchDao = new BranchDao($branchTableGateway);
            $branch = $branchDao->getAll($id);
            
            $view['branch'] = $branch;
            $view['branch_list'] = $branchListSelect;
            $view['franchise_id'] = $id;
//            var_dump($view['frnachis_id']);die;
            //print_r($branch->getArrayCopy());die;
            return new ViewModel($view);
        }
    }

    private function getBranchSelect($id) {
        
        $branchTableGateway = $this->getService('BranchTableGateway');
        $branchDao = new BranchDao($branchTableGateway);
        $branch = $branchDao->getAll($id);
        $result = array();
        foreach ($branch as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->branch_id] = $bankR->name;
        }
        return $result;
    }

    public function addAction() {
        $request = $this->getRequest();
        $franchise = $this->getFranchiseSelect();
        $country = $this->getCountrySelect();
        $branchAddForm = new BranchAdd();
        $branchAddForm->get('franchise_id')->setValueOptions($franchise);
//        $branchAddForm->get('city_id')->setValueOptions($city);
        array_unshift($country, NULL);
        $branchAddForm->get('country_id')->setValueOptions($country);
//        print_r($branchAddForm->getElements());die;
        //print_r($city);die;
        if ($request->isPost()) {
//
            $postData = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
//            var_dump($postData);die;
//            $franchiseAddForm = new FranchiseAdd();
            $branchAddForm->setInputFilter(New BranchAddValidator);
            $branchAddForm->setData($postData);
            //print_r($branchAddForm);die;
            $validacionEdit = $this->params()->fromPost('validationEdit');
//            $franchiseAddForm->setInputFilter(New FranchiseAddValidator);
//            $franchiseAddForm->setData($postData);
//            //echo $validacionEdit;die;
//            var_dump($branchAddForm->isValid());
//            print_r($branchAddForm->getMessages());
//            die;
            if ($branchAddForm->isValid()) {

                $branchData = $branchAddForm->getData();
                $prepareBranchData = $this->prepareBranchData($branchData);
//                var_dump($prepareBranchData);die;
                $branchEntity = New Branch;
                $branchEntity->exchangeArray($prepareBranchData);
//                 var_dump($brachData);die;
                $branchContactEntity = new BranchContact;
                $branchContactEntity->exchangeArray($prepareBranchData);
//                var_dump($branchContactEntity);die;
                //print_r($franquimovilEntity);die;
                $branchTableGateway = $this->getService('BranchTableGateway');
                $branchDao = new BranchDao($branchTableGateway);
                $saved = $branchDao->saveBranch($branchEntity, $branchContactEntity);
                if ($validacionEdit == 1) {
                    if ($saved) {
                        $this->messageAdd($saved);
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'branch',
                                    'action' => 'listFranchise'
                        ));
                    } else {
                        $brachData->populateValues($dataform);
                        $this->messageAdd($saved);
                    }
                } else {
                    //var_dump($saved);die;
                    if ($saved) {
                        $this->messageAdd($saved);
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'branch',
                                    'action' => 'listFranchise'
                        ));
                    } else {
                        $this->messageAdd($saved);
                        $branchAddForm->populateValues($prepareBranchData);
                    }
                }
            } else {
                $messages = $branchAddForm->getMessages();
                print_r($messages);
                print_r($branchAddForm->getData());
                $branchAddForm->populateValues($branchAddForm->getData());
                return $this->forward()->dispatch('Admin\Controller\Branch', array(
                            'action' => 'list',
                            'form' => $branchAddForm
                ));
            }
        }
        $view['form'] = $branchAddForm;
        return new ViewModel($view);
    }

    public function addProductAction() {
        $request = $this->getRequest();
        $productAddForm = new BranchAddProduct();
        if ($request->isPost()) {
//
            $postData = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
//            var_dump($postData);
//            die;
            $productAddForm->setInputFilter(New BranchAddProductValidator);
            $productAddForm->setData($postData);
            if ($productAddForm->isValid()) {
                $productData = $productAddForm->getData();

                $branchEntity = New ProductHasBranch();
                $branchEntity->exchangeArray($productData);
                $branchTableGateway = $this->getService('BranchHasProductTableGateway');
                $branchDao = new BranchDao($branchTableGateway);
                $saved = $branchDao->saveProductHasBranch($branchEntity);
                if ($saved) {
                    $this->messageAdd($saved);
                    return $this->redirect()->toRoute('admin', array(
                                'controller' => 'branch',
                                'action' => 'branchProduct',
                                'id' => $postData['branch_id']
                    ));
                } else {
                    return $this->forward()->dispatch('Admin\Controller\Branch', array(
                                'action' => 'listFranchise'
                    ));
                }
            } else {
//            $messages = $productAddForm->getMessages();
////            print_r($messages);
////            print_r($productAddForm->getData());
//            $productAddForm->populateValues($productAddForm->getData());
                return $this->forward()->dispatch('Admin\Controller\Branch', array(
                            'action' => 'listFranchise'
                ));
            }
        }
    }

    public function branchProductAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $product = $this->getProductSelect($id);
        $productAddForm = new BranchAddProduct();
        $productAddForm->get('product')->setValueOptions($product);

        $branchTableGateway = $this->getService('BranchTableGateway');
        $branchDao = new BranchDao($branchTableGateway);
        $branch = $branchDao->getbyId($id);
        $productAddForm->bind($branch);

        $branchTableGateway = $this->getService('BranchTableGateway');
        $branchDao = new BranchDao($branchTableGateway);
        $branch = $branchDao->getById($id);
        $view['branch'] = $branch;

        $branchProductTableGateway = $this->getService('ProductHasBranchHydratingTableGateway');
        $branchProductDao = new BranchDao($branchProductTableGateway);
        $products = $branchProductDao->getBranchProducts($id);
        $view['product'] = $products;

        $view['form'] = $productAddForm;
        return new ViewModel($view);
        //var_dump($view);die;
    }

    private function prepareBranchData($data) {
//         var_dump($data);die;
        if ($data['logo']['tmp_name'] != '') {
            $explo = explode('img_', $data['logo']['tmp_name']);
            $img = 'img_' . $explo[1];
        } else {
            $img = 'img_';
        }
        if ($data['banner']['tmp_name'] != '') {
            $explo = explode('img_', $data['banner']['tmp_name']);
//            var_dump($explo);die;
            $imgB = 'img_' . $explo[1];
        } else {
            $imgB = 'img_';
        }
        
        $data['banner'] = ($imgB == "img_") ? 'no-logo.jpg' : $imgB;
        $data['logo'] = ($img == "img_") ? 'no-logo.jpg' : $img;
        $data['date_added'] = date("Y-m-d H:i:s");
        $data['status'] = 1;
        $data['salt'] = time();
        $data['password'] = md5($data['password'] . $data['salt']);
        return $data;
    }

    private function getFranchiseSelect() {
        $franchiseDao = $this->getService('FranchiseDao');
        $results = $franchiseDao->getAll()->fetchAll();
        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->franchise_id] = $bankR->name;
        }
        return $result;
    }

    private function getProductSelect($id) {
        $franchiseTableGateway = $this->getService('BranchTableGateway');
        $franchiseDao = new BranchDao($franchiseTableGateway);
        $franchise = $franchiseDao->getFranchise($id);
//        var_dump($franchise);die;
        $id = $franchise->franchise_id;

        $productTableGateway = $this->getService('ProductTableGateway');
        $productDao = new ProductDao($productTableGateway);
        $results = $productDao->getAll();
        $results->where(array(
            'franchise_id' => $id
        ));
        $data = $results->fetchAll();
//        var_dump($data);die;
        $result = array();
        foreach ($data as $bankR) {
//            $result[] = $row->getArrayCopy();
            $result[$bankR->product_id] = $bankR->name;
        }
//        var_dump($result);die;
        return $result;
    }

    private function getCitySelect($id) {
        $cityDao = $this->getService('CityDao');
        $results = $cityDao->getAllCity($id);
        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->city_id] = $bankR->name;
        }
        return $result;
    }
    private function getStateSelect() {
        $cityDao = $this->getService('CityDao');
        $results = $cityDao->getAllState();
        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->state_id] = $bankR->name;
        }
        return $result;
    }
    private function getMunicipalitySelect() {
        $cityDao = $this->getService('CityDao');
        $results = $cityDao->getAllMunicipality();
        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->municipality_id] = $bankR->name;
        }
        return $result;
    }
    
     

    private function getCountrySelect() {
        $cityDao = $this->getService('CountryDao');
        $results = $cityDao->getAllCountry();
        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->country_id] = $bankR->name;
        }
        return $result;
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin', array(
                        'controller' => 'branch',
                        'action' => 'list'
            ));
        }
        $branchTableGateway = $this->getService('BranchTableGateway');
        $branchDao = new BranchDao($branchTableGateway);
        $branch = $branchDao->getBranch($id);
        
        //print_r($branch); die;
        $branchContactTableGateway = $this->getService('BranchContactTableGateway');
        $branchDao = new BranchDao($branchContactTableGateway);
        $branchContact = $branchDao->getBranchContact($id);

        $branchForm = new BranchAdd();
        $franchise = $this->getFranchiseSelect();
        $branchForm->get('franchise_id')->setValueOptions($franchise);
        
        $country = $this->getCountrySelect();
        array_unshift($country, NULL);
        $branchForm->get('country_id')->setValueOptions($country);   
      
        $state = $this->getStateSelect();     
        $branchForm->get('state_id')->setValueOptions($state);
        $branchForm->get('state_id')->setValue($branch->state_id);
        
        $state = $this->getMunicipalitySelect();     
        $branchForm->get('municipality_id')->setValueOptions($state);
        $branchForm->get('municipality_id')->setValue($branch->municipality_id);
        
        $city = $this->getCitySelect(null);     
        $branchForm->get('city_id')->setValueOptions($city);
        $branchForm->get('city_id')->setValue($branch->city_id);
        
        
        $branchForm->setData($branch->getArrayCopy());
        $branchForm->setData($branchContact->getArrayCopy());
        $view['logo'] = $branch->logo;
        $view['banner'] = $branch->banner;
        $view['form'] = $branchForm;
        //var_dump($view);die;
        return new ViewModel($view);
    }

    public function deleteAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $id = (int) $this->params()->fromRoute('id', 0);
        $branchTableGateway = $this->getService('BranchTableGateway');
        $branchDao = new BranchDao($branchTableGateway);
//        echo $id;die;
        $delete = $branchDao->deleteBranch($id);
//        $delete->fetchAll();
        var_dump($delete);
        die;
        if ($delete) {
            if ($request->isXmlHttpRequest()) {
                $response->setStatusCode(200);
                $response->setContent(Json::encode(array('response' => $delete)));
            }
        } else {
            $response->setStatusCode(400);
            $response->setContent(Json::encode(array('response' => $delete)));
        }
        return $response;
    }

    public function deleteProductAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id', 0);
        $branchTableGateway = $this->getService('ProductHasBranchTableGateway');
        $branchDao = new BranchDao($branchTableGateway);
        $delete = $branchDao->deleteBranchProduct($id);

        $branchTableGateway = $this->getService('BranchTableGateway');
        $branchDao = new BranchDao($branchTableGateway);
        $branchId = $branchDao->getBranchByProduct($id);
//        var_dump($branchId);die;
        $branch_id = $branchId->branch_id;
        if ($delete) {
            $this->messageDelete($delete);
            return $this->redirect()->toRoute('admin', array(
                        'controller' => 'branch',
                        'action' => 'branchProduct',
                        'id' => $branch_id
            ));
        }
    }

    public function statusAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $status = $request->getPost('status');
        $id = $request->getPost('id');
        $branchTableGateway = $this->getService('BranchTableGateway');
        $branchDao = new BranchDao($branchTableGateway);
        $update = $branchDao->updateProductStatus($status, $id);
        switch ($status) {
            case 1:
                $statusName = 'Activo';
                break;
            case 0:
                $statusName = 'Deshabilitado';
                break;
            case 2:
                $statusName = 'Archivado';
                break;
        }
        if ($update) {
            $response->setStatusCode(200);
            $response->setContent(Json::encode(array(
                        'response' => $update,
                        'status' => $status,
                        'statusName' => $statusName
                            )
            ));
        } else {
            $response->setStatusCode(400);
            $response->setContent(Json::encode(array('response' => $update)));
        }
        return $response;
    }
    public function statusProductAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $status = $request->getPost('status');
        $id = $request->getPost('id');
        $productTableGateway = $this->getService('ProductTableGateway');
        $productDao = new ProductDao($productTableGateway);
        $update = $productDao->updateProductHasBranchStatus($status, $id);
        switch ($status) {
            case 1:
                $statusName = 'Activo';
                break;
            case 0:
                $statusName = 'Deshabilitado';
                break;
            case 2:
                $statusName = 'Archivado';
                break;
        }
        if ($update) {
            $response->setStatusCode(200);
            $response->setContent(Json::encode(array(
                        'response' => $update,
                        'status' => $status,
                        'statusName' => $statusName
                            )
            ));
        } else {
            $response->setStatusCode(400);
            $response->setContent(Json::encode(array('response' => $update)));
        }
        return $response;
    }

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);
        return $service;
    }

    private function messageAdd($message) {
        // echo $message;die;
        switch ($message) {
            case 1:
                $this->flashMessenger()->addMessage('Elemento agregado satisfactoriamente  ', 'success');
                break;
            case 0:
                $this->flashMessenger()->addMessage('Error con la base de datos', 'error');
                break;
        }
    }

    private function messageDelete($message) {
        // echo $message;die;
        switch ($message) {
            case 1:
                $this->flashMessenger()->addMessage('Elemento eliminado satisfactoriamente  ', 'success');
                break;
            case 0:
                $this->flashMessenger()->addMessage('Error con la base de datos', 'error');
                break;
        }
    }

}
