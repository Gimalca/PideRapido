<?php

namespace Franchise\Controller;

use Sale\Model\Dao\CustomerDao;
use Sale\Model\Dao\OrderDao;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class OrdersController extends AbstractActionController {

    private $_user;    

    public function indexAction() {
        $day = $this->params()->fromQuery('day', 0);

        if ($day !== 0) {

            date_default_timezone_set('UTC');

            $validator = new \Zend\Validator\Date(array(
                'format' => 'd/m/Y',
                'locale' => 'UTC'
            ));

            if ($validator->isValid($day)) {
                $validDate = date_parse_from_format('d/m/Y', date($day));
//                var_dump($validDate);die;
            } else {
                echo 'fecha no valida';
                die;
            }
        }
        $auth = $this->getService('Franchise\Model\LoginFranchise');
        if ($auth->isLoggedIn()) {
            $this->_user = $auth->getIdentity();
            $branch_id = $this->_user->branch_id;
//            var_dump($branch_id);die;
            $orderTableGateway = $this->getService('OrderHydratingTableGateway');
            $orderDao = new OrderDao($orderTableGateway);
            if ($day !== 0) {
                $order = $orderDao->getSearchBranchOrders($branch_id,$validDate);
            } else {
                $order = $orderDao->getBranchOrders($branch_id);
                $order = $order->fetchAll();
            }
            
            $view['order'] = $order;            
//            var_dump($view['order']);die;
            return new ViewModel($view);
        }
    }

    public function detailAction() {
        $auth = $this->getService('Franchise\Model\LoginFranchise');
        if ($auth->isLoggedIn()) {
            $this->_user = $auth->getIdentity();
            $branch_id = $this->_user->branch_id;
            $orderId = (int) $this->params()->fromRoute('id', 0);
            $orderTableGateway = $this->getService('OrderHydratingTableGateway');
            $orderDao = new OrderDao($orderTableGateway);
            $order = $orderDao->getOrderDetail($orderId);
            $order = $order->fetchAllCurrent();

            $view['order'] = $order;
//            var_dump($view['order']);die;
            $productOrderTableGateway = $this->getService('OrderHydratingTableGateway');
            $orderDao = new OrderDao($orderTableGateway);
            $productOrder = $orderDao->getProductOption($orderId, $branch_id);
            $productOrder = $productOrder->fetchAll();
            $productOrder->buffer();
//            var_dump($productOrder);die;
            foreach ($productOrder as $productOrderOption):
                $productOptionsTableGateway = $this->getService('OrderHydratingTableGateway');
                $orderDao = new OrderDao($orderTableGateway);
                $productOrderId = $productOrderOption->order_product_id;
                $invoicenumerBranch = $productOrderOption->invoice_number_branch;
//                echo $productOrderId;die;
                $productOrderOptions = $orderDao->getProductOptionDetail($invoicenumerBranch, $productOrderId);
//                $productOrderOptions = $productOrderOptions->fetchAll();
                $productOrderOptions->buffer();
//                var_dump($productOrderOptions);die;
            endforeach;
//                var_dump($productOrderOptions);die;
            $view['product_order'] = $productOrder;
            $view['product_option'] = $productOrderOptions;
//        var_dump($view['product_order']);die;
            return new ViewModel($view);
        }
    }

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);
        return $service;
    }   
    

}
