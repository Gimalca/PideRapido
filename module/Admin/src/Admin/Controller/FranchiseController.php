<?php

namespace Admin\Controller;

use Admin\Form\FranchiseAdd;
use Admin\Form\FranchiseList;
use Admin\Form\Validator\FranchiseAddValidator;
use Franchise\Model\Dao\FranchiseDao;
use Franchise\Model\Entity\Franchise;
use Zend\Json\Json;
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
        $franchiseListSelect = new FranchiseList();

        $postData = array_merge_recursive(
                $request->getPost()->toArray(), $request->getFiles()->toArray()
        );
        @$franchise_id = $postData['franchise_id'];

        if ($request->isPost() && $franchise_id != '0') {




            $franchiseList['0'] = 'Todas';
            $franchiseListSelect->get('franchise_id')->setValueOptions($franchiseList);
            $franchiseListSelect->get('franchise_id')->setValue('0');

            $franchiseListSelect->get('franchise_id')->setValueOptions($franchiseList);

            $franchiseTableGateway = $this->getService('FranchiseTableGateway');
            $franchiseDao = new FranchiseDao($franchiseTableGateway);

            $franchies = $franchiseDao->getFranchiseSearch($franchise_id);
//            var_dump($franchies);die;
            $view['franchieses'] = $franchies;
            $view['franchies_list'] = $franchiseListSelect;
            return new ViewModel($view);
        }

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

    private function getFranchiseSelect()
    {
        $franchiseTableGateway = $this->getService('FranchiseTableGateway');
        $franchiseDao = new FranchiseDao($franchiseTableGateway);

        $franchies = $franchiseDao->getAll()
                ->fetchAll();
        $result = array();
        foreach ($franchies as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->franchise_id] = $bankR->name;
        }
        return $result;
    }

    public function addAction()
    {

        $request = $this->getRequest();
        $category = $this->getFranchiseCategorySelect();

        $franchiseAddForm = new FranchiseAdd();
        $franchiseAddForm->get('category_id')->setValueOptions($category);

        if ($request->isPost()) {

            $dataform = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
            //print_r($postData);die;
            $validacionEdit = $this->params()->fromPost('validationEdit');
            $franchiseAddForm->setInputFilter(New FranchiseAddValidator);
            $franchiseAddForm->setData($dataform);
            //echo $validacionEdit;die;
            //print_r($franchiseAddForm);die;
            if ($franchiseAddForm->isValid()) {


                $franchiseData = $franchiseAddForm->getData();
                //print_r($franchiseData);die;
                $prepareFranchiseData = $this->prepareFranchiseData($franchiseData);

                $franchiseEntity = New Franchise;
                $franchiseEntity->exchangeArray($prepareFranchiseData);


                $franchiseTableGateway = $this->getService('FranchiseTableGateway');
                $franchiseDao = new FranchiseDao($franchiseTableGateway);

                $saved = $franchiseDao->saveFranchise($franchiseEntity);

                if ($saved) {

                    $this->flashMessenger()->addMessage('Franquicia Creada', 'success');
                    return $this->redirect()->toRoute('admin', array(
                                'controller' => 'franchise',
                                'action' => 'list'
                    ));
                    
                } else {
                     $this->flashMessenger()->addMessage('No se pudo guardar el registro', 'error');
                  
                }
            } else {
                $messages = $franchiseAddForm->getMessages();
               
                $franchiseAddForm->populateValues($dataform);
                $this->flashMessenger()->addMessage($messages, 'error');
            }
        }
        $view['form'] = $franchiseAddForm;

        return new ViewModel($view);
    }



    public function editAction()
    {
        $request = $this->getRequest();
        
        $id = (int) $this->params()->fromRoute('id', 0);
        
        if (!$id && !$request->isPost() ){
            return $this->redirect()->toRoute('admin', array(
                        'controller' => 'franchise',
                        'action' => 'list'
            ));
        }

        $franchiseTableGateway = $this->getService('FranchiseTableGateway');
        $franchiseDao = new FranchiseDao($franchiseTableGateway);
        $franchiseForm = new FranchiseAdd();
        
        if ($request->isPost()) {

            $dataform = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
            
            print_r($dataform);
            $validate = New FranchiseAddValidator();
          
            if(!$dataform['logo']['tmp_name']){
             $validate->remove('logo');
             $dataform['logo'] = null;
            }
            if(!$dataform['banner']['tmp_name']){
             $validate->remove('banner');
              $dataform['banner'] = null;
            }
            
            $franchiseForm->setInputFilter($validate);
            $franchiseForm->setData($dataform);
            
             if ($franchiseForm->isValid()) {


                $franchiseData = $franchiseForm->getData();
                //print_r($franchiseData);die;
                $prepareFranchiseData = $this->prepareFranchiseData($franchiseData);

                $franchiseEntity = New Franchise;
                $franchiseEntity->exchangeArray($prepareFranchiseData);
                
                
                 $saved = $franchiseDao->saveFranchise($franchiseEntity);

                if ($saved) {

                    $this->flashMessenger()->addMessage('Franquicia Guardada', 'success');
                     return $this->redirect()->toRoute('admin', array(
                                'controller' => 'franchise',
                                'action' => 'list'
                    ));
                    
                } else {
                     $this->flashMessenger()->addMessage('No se pudo guardar el registro', 'error');
                    return $this->redirect()->toRoute('admin', array(
                                'controller' => 'franchise',
                                'action' => 'list'
                    ));
                }
                
             }  else {
                 
                $messages = $franchiseForm->getMessages();
              
                $category = $this->getFranchiseCategorySelect();
                $franchiseForm->get('category_id')->setValueOptions($category);
                
                $franchiseForm->populateValues($dataform);
                $this->flashMessenger()->addMessage($messages, 'error');
                
                $view['form'] = $franchiseForm;  
                //$view['franchieses'] = $franchies;
                return new ViewModel($view);
                 
             }

          
        }

        $franchise = $franchiseDao->getFranchise($id); 
        $category = $this->getFranchiseCategorySelect();
        
        $franchiseForm->setAttribute('action', 'admin/franchise/edit');
        $franchiseForm->get('category_id')->setValueOptions($category);
        $franchiseForm->get('banner')->setValue($franchise->banner);
        $franchiseForm->bind($franchise);

        $franchies = $franchiseDao->getById($id);
        $view['form'] = $franchiseForm;
        //print_r($franchies);die;
        $view['franchieses'] = $franchies;
        //print_r($view);die;
        //var_dump($view);die;
        return new ViewModel($view);
    }

    public function statusAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $status = $request->getPost('status');
        $id = $request->getPost('id');

        $franchiseTableGateway = $this->getService('FranchiseTableGateway');
        $franchiseDao = new FranchiseDao($franchiseTableGateway);

        $update = $franchiseDao->updateProductStatus($status, $id);

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

    public function deleteAction()
    {

        $request = $this->getRequest();
        $response = $this->getResponse();

        $id = $request->getPost('id');
        $franchiseTableGateway = $this->getService('FranchiseTableGateway');
        $franchiseDao = new FranchiseDao($franchiseTableGateway);
        $delete = $franchiseDao->deleteFranchise($id);
//        print_r($delete);die;
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
    
        private function prepareFranchiseData($data)
    {
//         var_dump($data);die;
        if ($data['logo']['tmp_name'] != '') {
            $explo = explode('img_', $data['logo']['tmp_name']);
            $img = 'img_' . $explo[1];
//            var_dump($explo);die;
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
//        var_dump($data);die;
        return $data;
    }
    
    public function getService($serviceName)
    {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
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

    private function message($message)
    {
        // echo $message;die;
        switch ($message) {
            case 1:
                $this->flashMessenger()->addMessage('Franquicia creada satisfactoriamente  ', 'success');

                break;
            case 0:
                $this->flashMessenger()->addMessage('Error con la base de datos', 'error');

                break;
        }
    }

}
