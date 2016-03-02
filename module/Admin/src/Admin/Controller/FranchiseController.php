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

class FranchiseController extends AbstractActionController {

    public function indexAction() {
        return new ViewModel();
    }

    public function listAction() {
        $request = $this->getRequest();
        $franchiseListSelect = new FranchiseList();

        if ($request->isPost()) {
            $postData = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
            $franchise_id = $postData['franchise_id'];
//                var_dump($product_id);die;
            $franchiseList = $this->getFranchiseSelect();
            $franchiseListSelect->get('franchise_id')->setValueOptions($franchiseList);

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
            $franchiseListSelect->get('franchise_id')->setValueOptions($franchiseList);
            $view['franchieses'] = $franchies;
            $view['franchies_list'] = $franchiseListSelect;
            //print_r($view);die;
            return new ViewModel($view);
        }
    }

    private function getFranchiseSelect() {
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

    public function addAction() {
        $request = $this->getRequest();
        $category = $this->getFranchiseCategorySelect();
        $franchiseAddForm = new FranchiseAdd();

        if ($request->isPost()) {

            $postData = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
            //print_r($postData);die;
            $validacionEdit = $this->params()->fromPost('validationEdit');
            $franchiseAddForm->setInputFilter(New FranchiseAddValidator);
            $franchiseAddForm->setData($postData);
            //echo $validacionEdit;die;
            //print_r($franchiseAddForm);die;
            if ($franchiseAddForm->isValid()) {

                $franchiseData = $franchiseAddForm->getData();
                $prepareFranchiseData = $this->prepareFranchiseData($franchiseData);

                $franchiseEntity = New Franchise;
                $franchiseEntity->exchangeArray($prepareFranchiseData);
//                var_dump($franchiseData);die;

                $franchiseTableGateway = $this->getService('FranchiseTableGateway');
                $franchiseDao = new FranchiseDao($franchiseTableGateway);

                $saved = $franchiseDao->saveFranchise($franchiseEntity);
                if ($validacionEdit == 1) {
                    if ($saved) {
                        $this->message($saved);
                        return $this->redirect()->toRoute('admin', array(
                                    'controller' => 'franchise',
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
                                    'controller' => 'franchise',
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
        $view['form'] = $franchiseAddForm;

        return new ViewModel($view);
    }

    private function prepareFranchiseData($data) {
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

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin', array(
                        'controller' => 'franchise',
                        'action' => 'list'
            ));
        }

        $franchiseTableGateway = $this->getService('FranchiseTableGateway');
        $franchiseDao = new FranchiseDao($franchiseTableGateway);

        $franchise = $franchiseDao->getFranchise($id);

        $category = $this->getFranchiseCategorySelect();
        $franchiseForm = new FranchiseAdd();
        $franchiseForm->get('category_id')->setValueOptions($category);
        $franchiseForm->bind($franchise);

        $franchies = $franchiseDao->getById($id);
        $view['form'] = $franchiseForm;
        //print_r($franchies);die;
        $view['franchieses'] = $franchies;
        //print_r($view);die;
        //var_dump($view);die;
        return new ViewModel($view);
    }

    public function statusAction() {
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

    public function deleteAction() {

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

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

    private function getFranchiseCategorySelect() {
        $franchiseCategoryDao = $this->getService('FranchiseCategoryDao');
        $results = $franchiseCategoryDao->getAllCategory();

        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->category_id] = $bankR->name;
        }
        return $result;
    }

    private function message($message) {
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
