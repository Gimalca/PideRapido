<?php

namespace Admin\Controller;

use Admin\Form\ClasificationAdd;
use Admin\Form\OptionAdd;
use Admin\Form\OptionValueAdd;
use Admin\Form\TypeAdd;
use Admin\Form\Validator\OptionAddValidator;
use Catalog\Model\Dao\OptionDao;
use Catalog\Model\Dao\ProductDao;
use Catalog\Model\Entity\Clasification;
use Catalog\Model\Entity\Option;
use Catalog\Model\Entity\OptionValue;
use Catalog\Model\Entity\Type;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class OptionController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function listAction()
    {
        $request = $this->getRequest();

        $optionDao = new OptionDao($this->getService('OptionHydratingTableGateway'));
        
        $optionAddForm = new OptionAdd();
        $type = $this->getTypeSelect();
        $optionAddForm->get('type')->setValueOptions($type);
        $clasification = $this->getClasificationSelect();
        //print_r($clasification);die;
        $optionAddForm->get('clasification')->setValueOptions($clasification);
        $view['form'] = $optionAddForm;
        
        $options = $optionDao->getAllOption();
        $view['options'] = $options;
        //print_r($options->toArray());die;
        return new ViewModel($view);
    }

    public function addOptionAction()
    {

        $request = $this->getRequest();
        $optionAddForm = new OptionAdd();
        $type = $this->getTypeSelect();
        $optionAddForm->get('type')->setValueOptions($type);
        $clasification = $this->getClasificationSelect();
        //print_r($clasification);die;
        $optionAddForm->get('clasification')->setValueOptions($clasification);

        if ($request->isPost()) {

            $postData = array_merge_recursive(
                    $request->getPost()->toArray()
            );
//            print_r($postData);die;
            $validacionEdit = $this->params()->fromPost('validationEdit');
            $optionAddForm->setInputFilter(New OptionAddValidator);
            $optionAddForm->setData($postData);
            //echo $validacionEdit;die;
//            print_r($optionAddForm);die;
            if ($optionAddForm->isValid()) {

                $optionData = $optionAddForm->getData();
              
            //    print_r($optionData);
                $optionEntity = New Option;
                $optionEntity->exchangeArray($optionData);
                
//                var_dump($optionData);die;

                $optionTableGateway = $this->getService('OptionTableGateway');
                $optionDao = new OptionDao($optionTableGateway);
             //   print_r($optionEntity);die;

                $saved = $optionDao->saveOption($optionEntity);

                if ($validacionEdit == 1) {
                    if ($saved) {
                        $this->message($saved);
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'option',
                                    'action' => 'list'
                        ));
                    } else {
                        $franchiseAddForm->populateValues($dataform);
                        $this->message($saved);
                    }
                } else {
                    if ($saved) {
                        $this->message($saved);
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'option',
                                    'action' => 'list'
                        ));
                    } else {
                        $this->message($saved);
                        $franchiseAddForm->populateValues($dataform);
                    }
                }
            } else {
                $messages = $optionAddForm->getMessages();
                print_r($messages);
                die;
                $franchiseAddForm->populateValues($dataform);
                return $this->forward()->dispatch('Admin\Controller\Category', array(
                            'action' => 'list',
                            'form' => $categoryForm
                ));
            }
        }
        $view['form'] = $optionAddForm;

        return new ViewModel($view);
    }
    public function statusOptionAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $status = $request->getPost('status');
        $id = $request->getPost('id');
//        echo $id;
        $productTableGateway = $this->getService('ProductTableGateway');
        $productDao = new ProductDao($productTableGateway);
        $update = $productDao->updateOptionGeneralStatus($status, $id);
//        echo $update;die;
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

    public function addTypeAction()
    {
        $request = $this->getRequest();
        $typeAddForm = new TypeAdd();

        if ($request->isPost()) {

            $postData = array_merge_recursive(
                    $request->getPost()->toArray()
            );
//            print_r($postData);die;
            $validacionEdit = $this->params()->fromPost('validationEdit');
            $typeAddForm->setInputFilter(New OptionAddValidator);
            $typeAddForm->setData($postData);
            //echo $validacionEdit;die;
            //print_r($optionAddForm);die;
            if ($typeAddForm->isValid()) {

                $typeData = $typeAddForm->getData();

                $typeEntity = New Type;
                $typeEntity->exchangeArray($typeData);

                $typeTableGateway = $this->getService('TypeTableGateway');
                $typeDao = new OptionDao($typeTableGateway);
//                print_r($typeEntity);die;

                $saved = $typeDao->saveType($typeEntity);
                if ($validacionEdit == 1) {
                    if ($saved) {
                        $this->message($saved);
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'option',
                                    'action' => 'list'
                        ));
                    } else {
                        $franchiseAddForm->populateValues($dataform);
                        $this->message($saved);
                    }
                } else {
                    if ($saved) {
                        $this->message($saved);
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'option',
                                    'action' => 'list'
                        ));
                    } else {
                        $this->message($saved);
                        $franchiseAddForm->populateValues($dataform);
                    }
                }
            } else {
                $messages = $franchiseAddForm->getMessages();
                print_r($messages);
                die;
                $franchiseAddForm->populateValues($dataform);
                return $this->forward()->dispatch('Admin\Controller\Category', array(
                            'action' => 'list',
                            'form' => $categoryForm
                ));
            }
        }
        $view['form'] = $typeAddForm;

        return new ViewModel($view);
    }

    public function addClasificationAction()
    {
        $request = $this->getRequest();
        $clasificationAddForm = new ClasificationAdd();

        if ($request->isPost()) {

            $postData = array_merge_recursive(
                    $request->getPost()->toArray()
            );
//            print_r($postData);die;
            $validacionEdit = $this->params()->fromPost('validationEdit');
            $clasificationAddForm->setInputFilter(New OptionAddValidator);
            $clasificationAddForm->setData($postData);
            //echo $validacionEdit;die;
            //print_r($optionAddForm);die;
            if ($clasificationAddForm->isValid()) {

                $clasificationData = $clasificationAddForm->getData();

                $clasificationEntity = New Clasification;
                $clasificationEntity->exchangeArray($clasificationData);

                $clasificationTableGateway = $this->getService('ClasificationTableGateway');
                $clasificationDao = new OptionDao($clasificationTableGateway);
//                print_r($clasificationEntity);die;

                $saved = $clasificationDao->saveClasification($clasificationEntity);
                if ($validacionEdit == 1) {
                    if ($saved) {
                        $this->message($saved);
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'option',
                                    'action' => 'list'
                        ));
                    } else {
                        $franchiseAddForm->populateValues($dataform);
                        $this->message($saved);
                    }
                } else {
                    if ($saved) {
                        $this->message($saved);
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'option',
                                    'action' => 'list'
                        ));
                    } else {
                        $this->message($saved);
                        $franchiseAddForm->populateValues($dataform);
                    }
                }
            } else {
                $messages = $franchiseAddForm->getMessages();
                print_r($messages);
                die;
                $franchiseAddForm->populateValues($dataform);
                return $this->forward()->dispatch('Admin\Controller\Category', array(
                            'action' => 'list',
                            'form' => $categoryForm
                ));
            }
        }
        $view['form'] = $clasificationAddForm;

        return new ViewModel($view);
    }

    public function addOptionValueAction()
    {
        $request = $this->getRequest();
        $optionValueAddForm = new OptionValueAdd();
        $option = $this->getOptionSelect();
        $optionValueAddForm->get('option_id')->setValueOptions($option);

        if ($request->isPost()) {

            $postData = array_merge_recursive(
                    $request->getPost()->toArray()
            );
//            print_r($postData);die;
            $validacionEdit = $this->params()->fromPost('validationEdit');
            $optionValueAddForm->setInputFilter(New OptionAddValidator);
            $optionValueAddForm->setData($postData);
            //echo $validacionEdit;die;
            //print_r($optionAddForm);die;
            if ($optionValueAddForm->isValid()) {

                $optionValueData = $optionValueAddForm->getData();

                $optionValueEntity = New OptionValue;
                $optionValueEntity->exchangeArray($optionValueData);
//                var_dump($optionData);die;

                $optionValueTableGateway = $this->getService('OptionValueTableGateway');
                $optionValueDao = new OptionDao($optionValueTableGateway);
//                print_r($optionEntity);die;

                $saved = $optionValueDao->saveOptionValue($optionValueEntity);
                
                if ($validacionEdit == 1) {
                    if ($saved) {
                        $this->message($saved);
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'option',
                                    'action' => 'list'
                        ));
                    } else {
                        $franchiseAddForm->populateValues($dataform);
                        $this->message($saved);
                    }
                } else {
                    if ($saved) {
                        $this->message($saved);
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'option',
                                    'action' => 'list'
                        ));
                    } else {
                        $this->message($saved);
                        $franchiseAddForm->populateValues($dataform);
                    }
                }
            } else {
                $messages = $franchiseAddForm->getMessages();
                print_r($messages);
                die;
                $franchiseAddForm->populateValues($dataform);
                return $this->forward()->dispatch('Admin\Controller\Category', array(
                            'action' => 'list',
                            'form' => $categoryForm
                ));
            }
        }
        $view['form'] = $optionValueAddForm;

        return new ViewModel($view);
    }
    
    public function deleteOptionAction() {

        $request = $this->getRequest();
        $response = $this->getResponse();

        $id = $request->getPost('id');
       $optionTableGateway = $this->getService('OptionTableGateway');
        $optionDao = new OptionDao($optionTableGateway);
        $delete = $optionDao->deleteOption($id);
        //print_r($delete);die;
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
    
    

    public function getService($serviceName)
    {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

    private function getOptionSelect()
    {
        $optionTableGateway = $this->getService('OptionTableGateway');
        $optionDao = new OptionDao($optionTableGateway);
        $results = $optionDao->getAlloption();

        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->option_id] = $bankR->name;
        }
        return $result;
    }

    private function getTypeSelect()
    {
        $typeTableGateway = $this->getService('TypeTableGateway');
        $typeDao = new OptionDao($typeTableGateway);
        $results = $typeDao->getAllType();

        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->type_id] = $bankR->name;
        }
        return $result;
    }

    private function getClasificationSelect()
    {
        $clasificationTableGateway = $this->getService('ClasificationTableGateway');
        $clasificationDao = new OptionDao($clasificationTableGateway);
        $results = $clasificationDao->getAllClasification();

        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->clasification_id] = $bankR->name;
        }
        return $result;
    }

    private function message($case, $message = null)
    {

        switch ($case) {
            case 1:
                (is_null($message) ) ? $message = 'Registro exitoso ' : $message;
                $this->flashMessenger()->addMessage($message, 'success');

                break;
            case 0:
                (is_null($message) ) ? $message = 'Error con la base de datos' : $message ;
                $this->flashMessenger()->addMessage($message, 'error');

                break;
        }
    }

}
