<?php

namespace Franchise\Controller;

use Franchise\Form\BranchAdd;
use Admin\Form\Validator\BranchAddValidator;
use Franchise\Model\Dao\BranchDao;
use Franchise\Model\Entity\Branch;
use Franchise\Model\Entity\BranchContact;
use Franchise\Model\Entity\Franquimovil;
use Sale\Model\Dao\CustomerDao;
use Sale\Model\Dao\OrderDao;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController {

    private $_user; 

    public function indexAction() {
        $auth = $this->getService('Franchise\Model\LoginFranchise');
        if ($auth->isLoggedIn()) {
            $this->_user = $auth->getIdentity();
//            var_dump($this->_user);die;
            $this->layout()->branch_id = $this->_user->branch_id;
            $this->layout()->user = $this->_user->user;
//            print_r($this->_user);die;
            $branch_id = $this->_user->branch_id;
            $branchTableGateway = $this->getService('BranchTableGateway');
            $branchDao = new BranchDao($branchTableGateway);
            $branch = $branchDao->getById($branch_id);
            $view['branch'] = $branch;
            //print_r($branch->getArrayCopy());die;
            $orderTableGateway = $this->getService('OrderHydratingTableGateway');
            $orderDao = new OrderDao($orderTableGateway);
            $order = $orderDao->getBranchOrdersLastest($branch_id);
            $order = $order->fetchAll();
            
            $totalOrder = $orderDao->getTotalOrders($branch_id);
            $orderTotalPrice = $orderDao->getBranchOrders($branch_id);
            $orderTotalPrice = $orderTotalPrice->fetchAll();
            
            $price = 0;
            foreach ($orderTotalPrice as $order_price):
                $price = $price + $order_price->subtotal_order_branch;
            endforeach;
//            echo($price);die;
//            $order = $order->fetchAll();
            $view['order'] = $order;
            $view['total_orders'] = $totalOrder->total;
            $view['total_price'] = $price;
            
            return new ViewModel($view);
        } else {
            return $this->forward()->dispatch('Franchise\Controller\Login', array('action' => 'index'));
        }
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

    public function addAction() {
        $auth = $this->getService('Franchise\Model\LoginFranchise');
        if ($auth->isLoggedIn()) {
            $this->_user = $auth->getIdentity();
        }
        $request = $this->getRequest();
        $branchAddForm = new BranchAdd();
//        var_dump($this->_user);
        $branch_id = $this->_user->branch_id;
//        var_dump($branchAddForm->getElements());die;
        //print_r($city);die;
        if ($request->isPost()) {
//
            $postData = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
            $postData['branch_id'] = $branch_id;
            $postData['branch_contact_id'] = $this->_user->branch_contact_id;
            $postData['user'] = $this->_user->user;
            $postData['email'] = $this->_user->email;
            $postData['email_franchise'] = $this->_user->email_franchise;
//            var_dump($postData);die;
//            $franchiseAddForm = new FranchiseAdd();
//            var_dump($branchAddForm);die;
            if ($postData) {
//              echo "si";die;
                $prepareBranchData = $this->prepareBranchData($postData);
//                var_dump($prepareBranchData);die;
                $branchContactEntity = new BranchContact;
                $branchContactEntity->exchangeArray($prepareBranchData);
//                var_dump($postData);die;
//                var_dump($branchContactEntity);die;
                $branchTableGateway = $this->getService('BranchTableGateway');
                $branchDao = new BranchDao($branchTableGateway);
                $saved = $branchDao->updateBranchContactByBranch($branchContactEntity);
//                var_dump($saved);die;
                if ($saved) {
                    $this->messageAdd($saved);
                    return $this->redirect()->toRoute('franchise', array(
                                'controller' => 'index',
                                'action' => 'index'
                    ));
                } else {
                    $this->messageAdd($saved);
                    $branchAddForm->populateValues($prepareBranchData);
                }
            } else {
                $messages = $branchAddForm->getMessages();
                print_r($branchAddForm->getData());
                $branchAddForm->populateValues($branchAddForm->getData());
                return $this->forward()->dispatch('Franchise\Controller\Index', array(
                            'action' => 'index'
                ));
            }
        }
        $view['form'] = $branchAddForm;
        return new ViewModel($view);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
//        echo $id;die;
        if (!$id) {
            return $this->redirect()->toRoute('franchise', array(
                        'controller' => 'index',
                        'action' => 'index'
            ));
        }
        $branchTableGateway = $this->getService('BranchTableGateway');
        $branchDao = new BranchDao($branchTableGateway);
        $branch = $branchDao->getBranch($id);

        $branchContactTableGateway = $this->getService('BranchContactTableGateway');
        $branchDao = new BranchDao($branchContactTableGateway);
        $branchContact = $branchDao->getBranchContact($id);

        $branchForm = new BranchAdd();
        
        $branchForm->setData($branch->getArrayCopy());
        $branchForm->setData($branchContact->getArrayCopy());
//        var_dump($branchForm);die;
        $view['logo'] = $branch->logo;
        $view['form'] = $branchForm;
        $view['branch'] = $branch;
        //var_dump($view);die;
        return new ViewModel($view);
    }

    private function prepareBranchData($data) {
        // print_r($data);die;

        $data['salt'] = time();
        $data['password'] = md5($data['password'] . $data['salt']);
        return $data;
    }
     private function messageAdd($message) {
        // echo $message;die;
        switch ($message) {
            case 1:
                $this->flashMessenger()->addMessage('Clave editada satisfactoriamente  ', 'success');
                break;
            case 0:
                $this->flashMessenger()->addMessage('Error con la base de datos', 'error');
                break;
        }
    }

}
