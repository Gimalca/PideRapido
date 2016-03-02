<?php

namespace Admin\Controller;

use Sale\Model\Dao\CustomerDao;
use Sale\Model\Dao\OrderDao;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController {

    private $_user;

    public function indexAction() {
        $auth = $this->getService('Admin\Model\LoginAdmin');
        if ($auth->isLoggedIn()) {
            $this->_user = $auth->getIdentity();
//            var_dump($this->_user);die;
            $this->layout()->first_name = $this->_user->firstname;
            $this->layout()->last_name = $this->_user->lastname;
//            $this->layout()->user_id = $this->_user->user_id;
            $orderTableGateway = $this->getService('OrderHydratingTableGateway');
            $orderDao = new OrderDao($orderTableGateway);
            $order = $orderDao->getLatest();
            $order = $order->fetchAll();            
            $view['order'] = $order;
//            var_dump($view['order']);die;
            $customerTableGateway = $this->getService('CustomerTableGateway');
            $customerDao = new CustomerDao($customerTableGateway);
            $customer = $customerDao->getLatest();       
            $view['customer'] = $customer;
            return new ViewModel($view);
        } else {
            return $this->forward()->dispatch('Admin\Controller\Login', array('action' => 'index'));
        }
    }

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

}
