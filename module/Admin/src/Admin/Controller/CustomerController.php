<?php

namespace Admin\Controller;

use Sale\Model\Dao\CustomerDao;
use Sale\Model\Dao\OrderDao;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CustomerController extends AbstractActionController {

    public function indexAction() {
        $customerTableGateway = $this->getService('CustomerTableGateway');
        $customerDao = new CustomerDao($customerTableGateway);

        $customer = $customerDao->getAllSimple();

        $view['customer'] = $customer;

        return new ViewModel($view);
    }

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);
        return $service;
    }

    public function listAction() {

        $customerID = (int) $this->params()->fromRoute('id', 0);
        $sm = $this->getServiceLocator();

        $orderTableGateway = $sm->get('OrderHydratingTableGateway');
        $orderDao = new OrderDao($orderTableGateway);

        $orders = $orderDao->getUserOrders($customerID);

        $customerTableGateway = $this->getService('CustomerTableGateway');
        $customerDao = new CustomerDao($customerTableGateway);

        $customer = $customerDao->getById($customerID);

        return new ViewModel([
            'orders' => $orders,
            'customer' => $customer
        ]);
    }

    public function orderDetailAction() {

        $orderId = (int) $this->params()->fromRoute('id', 0);
        $customerId = (int) $this->params()->fromRoute('idd', 0);
//        echo $branch_id;die;

        $productOrderTableGateway = $this->getService('OrderHydratingTableGateway');
        $orderDao = new OrderDao($productOrderTableGateway);
        $oderDetail = $orderDao->getOrderDetail($orderId);
        $oderDetail = $oderDetail->fetchAllCurrent();
//        var_dump($oderDetail);die;
//        $productOrderTableGateway = $this->getService('OrderHydratingTableGateway');
        $orderDao = new OrderDao($productOrderTableGateway);
//            echo $branch_id;die;
        $productOrder = $orderDao->getProductOptionCustomer($orderId);
        $productOrder = $productOrder->fetchAll();
        $productOrder->buffer();
//            var_dump($productOrder);die;

        $priceOption = 0;
        foreach ($productOrder as $productOrderOption):
            $productOptionsTableGateway = $this->getService('OrderHydratingTableGateway');
            $orderDao = new OrderDao($productOrderTableGateway);
            $productOrderId = $productOrderOption->order_product_id;
            $invoicenumerBranch = $productOrderOption->invoice_number_branch;
//                echo $productOrderId;die;
            $productOrderOptions = $orderDao->getProductOptionDetail($invoicenumerBranch, $productOrderId);
//                $productOrderOptions = $productOrderOptions->fetchAll();

            $productOrderOptions->buffer();
//                var_dump($productOrderOptions);die;

        endforeach;

//            echo $oderDetail->sub_total;die;
//            $subtotalBranch = $comisionPrice + $price;
        $iva = 0.12 * $oderDetail->subtotal;
        $subtotalBranch = $oderDetail->subtotal + $iva;
        $service = $oderDetail->subtotal * 0.12;
        $ivaService = $service * 0.12;
        $subtotalPr = $service + $ivaService;
        $total = $subtotalBranch + $subtotalPr;
//            echo $subtotalBranch;die;
//            var_dump($productOrderOption);die;
        $view['comision'] = $service;
        $view['subtotal_branch'] = $subtotalBranch;
        $view['subtotal_pr'] = $subtotalPr;
        $view['iva'] = $iva;
        $view['iva_service'] = $ivaService;
        $view['price'] = $oderDetail->subtotal;
        $view['total'] = $total;
        $view['producto'] = $oderDetail;
        $view['product_order'] = $productOrder;
        $view['product_option'] = $productOrderOptions;
        return new ViewModel($view);
    }

}
