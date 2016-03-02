<?php

namespace Admin\Controller;

use Admin\Form\BranchAdd;
use Admin\Form\ComboAdd;
use Admin\Form\ComboHasProductEdit;
use Admin\Form\ProductAdd;
use Admin\Form\Validator\ComboAddValidator;
use Catalog\Model\Dao\CategoryDao;
use Catalog\Model\Dao\ComboDao;
use Catalog\Model\Dao\ProductDao;
use Catalog\Model\Entity\Combo;
use Catalog\Model\Entity\ComboHasProduct;
use Catalog\Model\Entity\Product;
use Franchise\Model\Dao\BranchDao;
use Franchise\Model\Dao\FranchiseDao;
use Zend\File\Transfer\Adapter\Http as FileTransferAdapter;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ViewModel\JsonModel;

class ComboController extends AbstractActionController {

    public function indexAction() {
        return new ViewModel();
    }

    public function listFranchiseAction() {
        $request = $this->getRequest();

        $franchiseTableGateway = $this->getService('FranchiseTableGateway');
        $franchiseDao = new FranchiseDao($franchiseTableGateway);

        $franchies = $franchiseDao->getAll();
        $view['franchieses'] = $franchies;

        return new ViewModel($view);
    }

    public function listAction() {
        $request = $this->getRequest();

        $id = (int) $this->params()->fromRoute('id', 0);
        $comboTableGateway = $this->getService('ComboTableGateway');
        $comboDao = new ComboDao($comboTableGateway);

        $combo = $comboDao->getAll($id);
        $view['combo'] = $combo;
//        print_r($view);die;
        return new ViewModel($view);
    }

    public function addAction() {
        $request = $this->getRequest();
        $franchise = $this->getFranchiseSelect();
        $product = $this->getProductSelect();
        $comboAddForm = new ComboAdd();
        $comboAddForm->get('franchise_id')->setValueOptions($franchise);
//        $comboAddForm->get('productid')->setValueOptions($product);
        $comboAddForm->get('selectpro')->setValueOptions($product);
//        print_r($comboAddForm->getElements());die;
        if ($request->isPost()) {

            $postData = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
//            print_r($postData);die;
//            $franchiseAddForm = new FranchiseAdd();
            $comboAddForm->setInputFilter(New ComboAddValidator);
            $comboAddForm->setData($postData);
//            var_dump($postData);
            $validacionEdit = $this->params()->fromPost('validationEdit');
            $productsId = $this->params()->fromPost('product_id');
//            var_dump($productsId);die;
//            echo $validacionEdit;die;
//            var_dump($comboAddForm->isValid());
//            print_r($comboAddForm->getMessages());die;
//            die;

            if ($comboAddForm->isValid()) {

                $comboData = $comboAddForm->getData();
                $prepareProductData = $this->prepareProductData($comboData);

                $comboEntity = New Combo;
                $comboEntity->exchangeArray($prepareProductData);

//                var_dump($comboEntity);die;

                $comboTableGateway = $this->getService('ComboTableGateway');
                $comboDao = new ComboDao($comboTableGateway);

                $saved = $comboDao->saveCombo($comboEntity, $productsId);
                if ($validacionEdit == 1) {
                    if ($saved) {
                        $this->message($saved);
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'combo',
                                    'action' => 'listFranchise'
                        ));
                    } else {
                        $brachData->populateValues($dataform);
                        $this->message($saved);
                    }
                } else {
                    //var_dump($saved);die;
                    if ($saved) {
                        $this->message($saved);
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'combo',
                                    'action' => 'listFranchise'
                        ));
                    } else {
                        $this->message($saved);
                        $comboAddForm->populateValues($dataform);
                    }
                }
            } else {
                $messages = $comboAddForm->getMessages();
                print_r($messages);
                print_r($comboAddForm->getData());
                $comboAddForm->populateValues($comboAddForm->getData());
                $view['form'] = $comboAddForm;

                return new ViewModel($view);
            }
        }
        $view['form'] = $comboAddForm;

        return new ViewModel($view);
    }

    private function getFranchiseSelect() {
        $franchiseTable = $this->getService('FranchiseTableGateway');
        $franchiseDao = new FranchiseDao($franchiseTable);
        $results = $franchiseDao->getAll();

        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->franchise_id] = $bankR->name;
        }

        return $result;
    }

    private function getProductSelect() {
        $productTable = $this->getService('ProductTableGateway');
        $productDao = new ProductDao($productTable);
        $results = $productDao->getAllProduct();
        //var_dump($results);die;

        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $bankR->getArrayCopy();die;
            $result[$bankR->product_id] = $bankR->name;
        }

        return $result;
    }

    private function prepareProductData($data) {
        // print_r($data);die;
        if ($data['image']['tmp_name'] != '') {
            $explo = explode('img_', $data['image']['tmp_name']);
            $img = 'img_' . $explo[1];
        } else {
            $img = 'img_';
        }

        $data['image'] = ($img == "img_") ? 'no-logo.jpg' : $img;
        $data['date_added'] = date("Y-m-d H:i:s");
        $data['date_modified'] = date("Y-m-d H:i:s");
        $data['status'] = 1;

        return $data;
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin', array(
                        'controller' => 'combo',
                        'action' => 'list'
            ));
        }

        $comboTableGateway = $this->getService('ComboHydratingTableGateway');
        $comboDao = new ComboDao($comboTableGateway);

        $combo = $comboDao->getCombo($id);

        $comboForm = new ComboAdd();
        $franchise = $this->getFranchiseSelect();
        $comboForm->get('franchise_id')->setValueOptions($franchise);
        $product = $this->getProductSelect();
        $comboForm->get('selectpro')->setValueOptions($product);
        $comboForm->setData($combo->getArrayCopy());

        // Populate Products
        $comboHasProducstTableGateway = $this->getService('ComboHasProductHydratingTableGateway');
        $comboHasProductsDao = new ComboDao($comboHasProducstTableGateway);
        $products = $comboHasProductsDao->getComboHasProduct($id); 
        $products->buffer();    
//Calculo del precio
        $productP = $this->getProducPrice($products);
//        echo $productP;die;
     //   print_r($products->current());die;
        $view['product'] = $products;
        $view['productPrice'] = $productP;
        $view['image'] = $combo->image;
        $view['form'] = $comboForm;
        
//        var_dump($view['product']);die;
        return new ViewModel($view);
    }

    public function deleteAction() {

        $request = $this->getRequest();
        $response = $this->getResponse();

        $id = $request->getPost('id');
        $comboTableGateway = $this->getService('ComboTableGateway');
        $comboDao = new ComboDao($comboTableGateway);
        $delete = $comboDao->deleteCombo($id);

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
    public function deletepAction() {

        $request = $this->getRequest();
        $response = $this->getResponse();

        $id = (int) $this->params()->fromRoute('id', 0);
        $idc = (int) $this->params()->fromRoute('idd', 0);
//        echo $idc;die;
        $comboTableGateway = $this->getService('ComboHasProductTableGateway');
        $comboDao = new ComboDao($comboTableGateway);
        $delete = $comboDao->deleteComboProduct($id,$idc);

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

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

    public function statusAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $status = $request->getPost('status');
        $id = $request->getPost('id');
        $comboTableGateway = $this->getService('ComboTableGateway');
        $comboDao = new ComboDao($comboTableGateway);
        $update = $comboDao->updateComboStatus($status, $id);

        switch ($status) {
            case 1:
                $statusName = 'Activo';
                break;
            case 0:
                $statusName = 'Deshabilitado';
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

    private function message($message) {
        // echo $message;die;
        switch ($message) {
            case 1:
                $this->flashMessenger()->addMessage('Producto creado satisfactoriamente  ', 'success');

                break;
            case 0:
                $this->flashMessenger()->addMessage('Error con la base de datos', 'error');

                break;
        }
    }

    private function getProducPrice($products) {
        // echo $message;die;
        $precio = 0;
        foreach ($products as $prod) {

            $precioU = $prod->price;
            $precio = $precio + $precioU;
        }
        return $precio;
    }

}
