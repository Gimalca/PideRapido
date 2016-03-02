<?php

namespace Franchise\Controller;

use Catalog\Model\Dao\CategoryDao;
use Catalog\Model\Dao\OptionDao;
use Catalog\Model\Dao\ProductDao;
use Catalog\Model\Entity\Product;
use Catalog\Model\Entity\ProductOptionValue;
use Franchise\Form\PriceForm;
use Franchise\Form\ProductAdd;
use Franchise\Form\ProductList;
use Franchise\Form\Validator\PriceValidator;
use Franchise\Form\Validator\ProductAddValidator;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProductsController extends AbstractActionController {

    private $_user;

    public function addAction() {
        $request = $this->getRequest();
        $category = $this->getCategorySelect();
        $productAddForm = new ProductAdd();
        $productAddForm->get('category_id')->setValueOptions($category);


        $optionSelectData = $this->getOptionSelect();
        $optionSelectData['0'] = 'Ninguno';
        $productAddForm->get('option')->setValueOptions($optionSelectData);
        $productAddForm->get('option')->setValue('0');

        if ($request->isPost()) {

            $postData = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
            //print_r($postData);die;
            $productAddForm->setInputFilter(New ProductAddValidator);
            $productAddForm->setData($postData);
            //            var_dump($productAddForm);die;
            $validacionEdit = $this->params()->fromPost('validationEdit');

            if ($productAddForm->isValid()) {

                $productData = $productAddForm->getData();
                $prepareProductData = $this->prepareProductData($productData);

                $productEntity = New Product;
                $productEntity->exchangeArray($prepareProductData);

                // print_r($productOptionEntity);                print_r($productEntity);die;
                $productTableGateway = $this->getService('ProductTableGateway');
                $productDao = new ProductDao($productTableGateway);

                $saved = $productDao->saveProduct($productEntity);

                if ($validacionEdit == 1) {
                    if ($saved) {
                        $this->message($saved);
                        return $this->redirect()->toRoute('franchise', array(
                                    'controller' => 'products',
                                    'action' => 'edit',
                                    'id' => $productEntity->product_id,
                        ));
                    } else {
                        $brachData->populateValues($postData);
                        $this->message($saved);
                    }
                } else {
                    // var_dump($saved);die;
                    if ($saved) {
                        $this->message(1, 'Producto salvado');
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'product',
                        ));
                    } else {
                        $this->message($saved);
                        $productAddForm->populateValues($postData);
                    }
                }
            } else {
                $messages = $productAddForm->getMessages();
//                print_r($messages);
//                die;
//                print_r($productAddForm->getData());
                $productAddForm->populateValues($productAddForm->getData());
            }
        }
        $view['form'] = $productAddForm;

        return new ViewModel($view);
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
        $data['status'] = 1;

        return $data;
    }

    public function productsAction() {
        $auth = $this->getService('Franchise\Model\LoginFranchise');
        if ($auth->isLoggedIn()) {
            $request = $this->getRequest();
            $productListSelect = new ProductList();
            $this->_user = $auth->getIdentity();
            $branch_id = $this->_user->branch_id;

            if ($request->isPost()) {
                $postData = array_merge_recursive(
                        $request->getPost()->toArray(), $request->getFiles()->toArray()
                );
                $product_id = $postData['product_id'];
//                var_dump($product_id);die;
                $productList = $this->getProductSelect($branch_id);
                $productListSelect->get('product_id')->setValueOptions($productList);
                
                $productTableGateway = $this->getService('ProductTableGateway');
                $productDao = new ProductDao($productTableGateway);
                $product = $productDao->getProductsBranchSearch($product_id);
//                var_dump($product);die;
                $view['product'] = $product;
                $view['product_list'] = $productListSelect;
                return new ViewModel($view);
            } else {
                
//            var_dump($branch_id);die;
                $productList = $this->getProductSelect($branch_id);
                $productListSelect->get('product_id')->setValueOptions($productList);
                
                $productTableGateway = $this->getService('ProductTableGateway');
                $productDao = new ProductDao($productTableGateway);
                $product = $productDao->getProductsBranch($branch_id);
//            var_dump($product);die;
                $product->buffer();

                $view['product'] = $product;
                $view['product_list'] = $productListSelect;
                return new ViewModel($view);
            }
        }
    }

    private function getProductSelect($branch_id) {
        $productTableGateway = $this->getService('ProductTableGateway');
        $productDao = new ProductDao($productTableGateway);
        $product = $productDao->getProductsBranch($branch_id);
        $result = array();
        foreach ($product as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->product_id] = $bankR->name;
        }
        return $result;
    }

    public function statusAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $status = $request->getPost('status');
        $id = $request->getPost('id');
//        echo $id;die;
        $productTableGateway = $this->getService('ProductTableGateway');
        $productDao = new ProductDao($productTableGateway);
        $update = $productDao->updateProductStatus($status, $id);
//        echo $status;die;
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

    public function statusOptionAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $status = $request->getPost('status');
        $id = $request->getPost('id');
//        echo $id;
        $productTableGateway = $this->getService('ProductTableGateway');
        $productDao = new ProductDao($productTableGateway);
        $update = $productDao->updateOptionStatus($status, $id);
//        echo $update;die;
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

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin', array(
                        'controller' => 'product',
                        'action' => 'list'
            ));
        }

        $productTableGateway = $this->getService('ProductHydratingTableGateway');
        $productDao = new ProductDao($productTableGateway);
        $product = $productDao->getProduct($id);

        $productForm = new ProductAdd();
        $category = $this->getCategorySelect();
        $productForm->get('category_id')->setValueOptions($category);
        $productForm->setData($product->getArrayCopy());


        $options = $productDao->getAll(array('product_id'))
                ->whithOptionsValue()
                ->where(array('pr_product.product_id' => $id))
                ->fetchAll();
        $options->buffer();
//                var_dump($options);die;

        $view['options'] = $options;
        $view['image'] = $product->image;
        $view['form'] = $productForm;

        $price = new PriceForm();
        $view['formPrice'] = $price;
        //var_dump($options);die;
        return new ViewModel($view);
    }

    public function priceAction() {
        $request = $this->getRequest();
        $productAddForm = new PriceForm();

        if ($request->isPost()) {

            $postData = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
//            var_dump($postData);die;
            $productAddForm->setInputFilter(New PriceValidator);
            $productAddForm->setData($postData);
//                    var_dump($productAddForm->isValid());die;

            if ($productAddForm->isValid()) {

                $productData = $productAddForm->getData();
//                var_dump($productData);die;

                $productEntity = New ProductOptionValue();
                $productEntity->exchangeArray($productData);

                $productTableGateway = $this->getService('ProductOptionValueTableGateway');
                $productDao = new ProductDao($productTableGateway);

                $saved = $productDao->updatePrice($productEntity);
                if ($saved) {
                    $this->message($saved);
                    return $this->redirect()->toRoute('franchise', array(
                                'controller' => 'products',
                                'action' => 'edit',
                                'id' => $postData['product_id'],
                    ));
                } else {
                    $this->message($saved);
                    return $this->redirect()->toRoute('franchise', array(
                                'controller' => 'products',
                                'action' => 'edit',
                                'id' => $postData['product_id'],
                    ));
                }
            } else {

                return $this->redirect()->toRoute('franchise', array(
                            'controller' => 'products',
                            'action' => 'products'
                ));
            }
        }
    }

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

    private function message($message) {
        // echo $message;die;
        switch ($message) {
            case 1:
                $this->flashMessenger()->addMessage('Cambio guardado satisfactoriamente  ', 'success');
                break;
            case 0:
                $this->flashMessenger()->addMessage('Error con la base de datos', 'error');
                break;
        }
    }

    private function getCategorySelect() {
        $categoryTable = $this->getService('CategoryTableGateway');
        $categoryDao = new CategoryDao($categoryTable);
        $results = $categoryDao->getAll();
        //var_dump($results);die;

        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $bankR->getArrayCopy();die;
            $result[$bankR->category_id] = $bankR->name;
        }

        return $result;
    }

    private function getOptionSelect() {
        $optionTable = $this->getService('OptionTableGateway');
        $optionDao = new OptionDao($optionTable);
        $results = $optionDao->getAllOption();
        //var_dump($results);die;

        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $bankR->getArrayCopy();die;
            $result[$bankR->option_id] = $bankR->name;
        }

        return $result;
    }

    private function getOptionValueSelect($productOptionId) {
        $optionValueTable = $this->getService('ProductOptionHydratingTableGateway');
        $optionDao = new OptionDao($optionValueTable);
        $results = $optionDao->getOptionValue($productOptionId);
        //print_r($results->toArray());die;

        $result = array();
        foreach ($results as $optionValue) {
            //$result[] = $bankR->getArrayCopy();die;
            $result[$optionValue->option_value_id] = $optionValue->name;
        }
        //print_r($result);die;
        return $result;
    }

}
