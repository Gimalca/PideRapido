<?php

namespace Admin\Controller;

use Admin\Form\ProductAdd;
use Admin\Form\ProductOptionAdd;
use Admin\Form\ProductOptionValueAdd;
use Admin\Form\Validator\ProductAddValidator;
use Admin\Form\Validator\ProductOptionAddValidator;
use Catalog\Model\Dao\CategoryDao;
use Catalog\Model\Dao\OptionDao;
use Catalog\Model\Dao\OptionsDao;
use Catalog\Model\Dao\ProductDao;
use Catalog\Model\Entity\Product;
use Catalog\Model\Entity\ProductOption;
use Catalog\Model\Entity\ProductOptionValue;
use Franchise\Form\ProductList;
use Franchise\Model\Dao\FranchiseDao;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProductController extends AbstractActionController {

    public function indexAction() {
        $productsDao = new ProductDao($this->getService('ProductHydratingTableGateway'));
        $products = $productsDao->getAll()
                ->whithFranchise(array(
                    'name_franchise' => 'name',))
                ->fetchAll();
        //print_r($products->toArray()); die;
        return new ViewModel(
                array('products' => $products)
        );
    }

    public function listFranchiseAction() {
        $request = $this->getRequest();

        $franchiseTableGateway = $this->getService('FranchiseTableGateway');
        $franchiseDao = new FranchiseDao($franchiseTableGateway);

        $franchies = $franchiseDao->getAll()
                ->fetchAll();
        $view['franchieses'] = $franchies;
//                var_dump($franchies);die;
        return new ViewModel($view);
    }

    public function listFranchiseProductAction() {
        $request = $this->getRequest();

        $franchiseTableGateway = $this->getService('FranchiseTableGateway');
        $franchiseDao = new FranchiseDao($franchiseTableGateway);

        $franchies = $franchiseDao->getAll();
        $view['franchieses'] = $franchies;

        return new ViewModel($view);
    }

    public function listProductAction() {
        $request = $this->getRequest();

        $id = (int) $this->params()->fromRoute('id', 0);
        $productTableGateway = $this->getService('ProductTableGateway');
        $productDao = new ProductDao($productTableGateway);

        $product = $productDao->getAll($id);
        $view['product'] = $product;
        //print_r($branch->getArrayCopy());die;
        return new ViewModel($view);
    }

    public function listAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id', 0);
        $productListSelect = new ProductList();
        
        if ($request->isPost()) {
            $postData = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
            $product_id = $postData['product_id'];
//                var_dump($product_id);die;
            $productList = $this->getBranchSelect($id);
            $productListSelect->get('product_id')->setValueOptions($productList);

            $productTableGateway = $this->getService('ProductTableGateway');
            $productDao = new ProductDao($productTableGateway);

            $product = $productDao->getAll()
                    ->where(array('pr_product.product_id' => $product_id))
                    ->fetchAll();
//            var_dump($branch);die;

            $view['product'] = $product;
            $view['product_list'] = $productListSelect;
            $view['franchise_id'] = $id;
            return new ViewModel($view);
        } else {
            $productList = $this->getBranchSelect($id);
            $productListSelect->get('product_id')->setValueOptions($productList);
            $productTableGateway = $this->getService('ProductTableGateway');
            $productDao = new ProductDao($productTableGateway);

            $product = $productDao->getAll()
                    ->where(array('franchise_id' => $id))
                    ->fetchAll();
            $view['product'] = $product;
            $view['product_list'] = $productListSelect;
            $view['franchise_id'] = $id;
            //print_r($branch->getArrayCopy());die;
            return new ViewModel($view);
        }
    }
    private function getBranchSelect($id) {
        
        $productTableGateway = $this->getService('ProductTableGateway');
            $productDao = new ProductDao($productTableGateway);

            $product = $productDao->getAll()
                    ->where(array('franchise_id' => $id))
                    ->fetchAll();
        $result = array();
        foreach ($product as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->product_id] = $bankR->name;
        }
        return $result;
    }

    public function addAction() {
        $request = $this->getRequest();
        $franchise = $this->getFranchiseSelect();
        $category = $this->getCategorySelect();
        $productAddForm = new ProductAdd();
        $productAddForm->get('franchise_id')->setValueOptions($franchise);
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
                //print_r($productEntity);die;
                $productOptionEntity = New ProductOption();
                $productOptionEntity->exchangeArray($productData);

                // print_r($productOptionEntity);                print_r($productEntity);die;
                $productTableGateway = $this->getService('ProductTableGateway');
                $productDao = new ProductDao($productTableGateway);

                $saved = $productDao->saveProduct($productEntity, $productOptionEntity);

                if ($validacionEdit == 1) {
                    if ($saved) {
                        $this->message($saved);
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'product',
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
                print_r($productAddForm->getData());
                $productAddForm->populateValues($productAddForm->getData());
            }
        }
        $view['form'] = $productAddForm;

        return new ViewModel($view);
    }

    public function addOptionAction() {
        $request = $this->getRequest();
        $option = $this->getOptionSelect();
        $productAddForm = new ProductOptionAdd();
        $productAddForm->get('option')->setValueOptions($option);
        $id = (int) $this->params()->fromRoute('id', 0);

        if ($request->isPost()) {

            $postData = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
            //            print_r($postData);die;
            //            $franchiseAddForm = new FranchiseAdd();
            $productAddForm->setInputFilter(New ProductOptionAddValidator);
            $productAddForm->setData($postData);
            //            var_dump($productAddForm);die;
            //            //echo $validacionEdit;die;
            //            var_dump($productAddForm->isValid());
            //            print_r($productAddForm->getMessages());die;
            //            die;

            if ($productAddForm->isValid()) {

                $productData = $productAddForm->getData();


                $productEntity = New ProductOption();
                $productEntity->exchangeArray($productData);

                $productOptionValueEntity = new ProductOptionValue();
                $productOptionValueEntity->exchangeArray($productData);

                $productTableGateway = $this->getService('ProductOptionTableGateway');
                $productDao = new ProductDao($productTableGateway);

                $saved = $productDao->saveProductOption($productEntity, $productOptionValueEntity, $id);

                if ($validacionEdit == 1) {
                    if ($saved) {
                        $this->message($saved);
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'product',
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
                                    'controller' => 'product',
                        ));
                    } else {
                        $this->message($saved);
                        $productAddForm->populateValues($dataform);
                    }
                }
            } else {
                $messages = $productAddForm->getMessages();
                print_r($messages);
                print_r($productAddForm->getData());
                $productAddForm->populateValues($productAddForm->getData());
                $view['form'] = $productAddForm;

                return new ViewModel($view);
            }
        }
        $view['form'] = $productAddForm;

        return new ViewModel($view);
    }

    private function getFranchiseSelect() {
        $franchiseTable = $this->getService('FranchiseTableGateway');
        $franchiseDao = new FranchiseDao($franchiseTable);
        $results = $franchiseDao->getAll()
                ->fetchAll();

        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->franchise_id] = $bankR->name;
        }

        return $result;
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
        $franchise = $this->getFranchiseSelect();
        $productForm->get('franchise_id')->setValueOptions($franchise);
        $category = $this->getCategorySelect();
        $productForm->get('category_id')->setValueOptions($category);
        $productForm->setData($product->getArrayCopy());

        $optionSelectData = $this->getOptionSelect();
        $optionSelectData['0'] = 'Ninguno';
        $productForm->get('option')->setValueOptions($optionSelectData);
        $productForm->get('option')->setValue('0');


        $options = $productDao->getAll(array('product_id'))
                ->whithOptions()
                ->where(array('pr_product.product_id' => $id))
                ->fetchAll();

        $view['options'] = $options;
        $view['image'] = $product->image;
        $view['form'] = $productForm;
        //var_dump($options);die;
        return new ViewModel($view);
    }

    public function optionValueAction() {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('admin', array(
                        'controller' => 'product',
                        'action' => 'list'
            ));
        }

        $optionValueTable = $this->getService('ProductOptionHydratingTableGateway');
        $optionDao = new OptionDao($optionValueTable);
        $results = $optionDao->getOptionValue($id);

        //print_r($results);die;
        $optionValueCurrent = $results->current();
        $productForm = new ProductOptionValueAdd();

        $optionValueSelectData = $this->getOptionValueSelect($id);
        $optionValueSelectData['0'] = 'Ninguno';

        $productForm->get('product_option_id')->setValue($id);
        $productForm->get('product_id')->setValue($optionValueCurrent->product_id);
        $productForm->get('option_id')->setValue($optionValueCurrent->option_id);

        $productForm->get('option_value')->setValueOptions($optionValueSelectData);
        $productForm->get('option_value')->setValue('0');
        // echo $id; die;
        // Traer los registros
        $productTableGateway = $this->getService('ProductTableGateway');
        $productDao = new ProductDao($productTableGateway);
        $product = $productDao->getById($optionValueCurrent->product_id);
        $view['product'] = $product;

        $franchiseTable = $this->getService('FranchiseTableGateway');
        $franchiseDao = new FranchiseDao($franchiseTable);
        $franchise = $franchiseDao->getFranchise($product->franchise_id);
        $view['franchise'] = $franchise;

        $optionTableGateway = $this->getService('OptionTableGateway');
        $optionDao = new OptionDao($optionTableGateway);
        $option = $optionDao->getById($optionValueCurrent->option_id);
        $view['option'] = $option;

        $productOptionValueDao = $this->getOptionsDao(new ProductOptionValue);
        $productOptionValues = $productOptionValueDao->all()
                ->optionValues()
                ->productOptions()
                ->where([
                    'pr_product_option_value.product_option_id' => $id,
                ])
                ->fetch();

        //var_dump($productOptionValues);die;
        $view['productOptionValues'] = $productOptionValues;

        //var_dump($optionValue->toArray());die;
        //print_r($optionValue->toArray());die;
//        $view['optionValue'] = $optionValue;
        $view['form'] = $productForm;

        return new ViewModel($view);
    }

    public function optionValueAddAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = $request->getPost()->toArray();

            $productTableGateway = $this->getService('ProductTableGateway');
            $productDao = new ProductDao($productTableGateway);

            $saved = $productDao->saveProductOptionValue($data);

            if ($saved) {
                $this->message($saved);
                return $this->redirect()->toRoute('admin', array(
                            'controller' => 'product',
                            'action' => 'optionValue',
                            'id' => $data['product_option_id'],
                ));
            }
        }
    }

    public function deleteProductOptionValueAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $id = (int) $this->params()->fromRoute('id', 0);

            if ($id != 0) {

                $productOptionValueDao = $this->getOptionsDao(new ProductOptionValue);

                $productOptionValue = $productOptionValueDao->all()
                        ->byId($id)
                        ->optionValues()
                        ->fetch()
                        ->current();

                $delete = $productOptionValueDao->delete($id);
                $message = '';

                if ($delete) {
                    $message = 'Se eliminó Exitosamente la opción ' . $productOptionValue->name;
                    $this->flashMessenger()->addMessage($message, 'success');
                } else {
                    $message = 'No se pudo Eliminar la opción Seleccionada';
                    $this->flashMessenger()->addMessage($message, 'error');
                }
            }
        }

        return $this->redirect()->toRoute('admin', array(
                    'controller' => 'product',
                    'action' => 'optionValue',
                    'id' => $productOptionValue->product_option_id,
        ));
    }

    public function deleteAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $id = $request->getPost('id');
        $productTableGateway = $this->getService('ProductTableGateway');
        $productDao = new ProductDao($productTableGateway);
        $delete = $productDao->deleteProduct($id);

        if ($delete) {
            if ($request->isXmlHttpRequest()) {
                $response->setStatusCode(200);
                $response->setContent(Json::encode(array('response' => $id)));
            }
        } else {
            $response->setStatusCode(400);
            $response->setContent(Json::encode(array('response' => $id)));
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
        $productTableGateway = $this->getService('ProductTableGateway');
        $productDao = new ProductDao($productTableGateway);
        $update = $productDao->updateProductStatus($status, $id);

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

    private function message($message) {
        // echo $message;die;
        switch ($message) {
            case 1:
                $this->flashMessenger()->addMessage('Registro Exitoso! ', 'success');
                break;
            case 0:
                $this->flashMessenger()->addMessage('Error con la base de datos', 'error');
                break;
        }
    }

    private function getOptionsDao($entity) {
        $resultSet = $this->getService('HydratingResultSet');
        return new OptionsDao($entity->tableGateway($resultSet));
    }

}
