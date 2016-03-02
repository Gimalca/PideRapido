<?php

namespace Franchise\Controller;

use DateTimeZone;
use Sale\Model\Dao\CustomerDao;
use Sale\Model\Dao\OrderDao;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use Zend\XmlRpc\Value\DateTime;
use DOMPDFModule\View\Model\PdfModel;

class InvoiceController extends AbstractActionController {

    private $_user;

    public function invoiceDailyAction() {

        $auth = $this->getService('Franchise\Model\LoginFranchise');
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

        if ($auth->isLoggedIn()) {
            $this->_user = $auth->getIdentity();
            $branch_id = $this->_user->branch_id;
//            var_dump($branch_id);die;
            $orderTableGateway = $this->getService('OrderHydratingTableGateway');
            $orderDao = new OrderDao($orderTableGateway);
            if ($day !== 0) {
                $order = $orderDao->getSearchBranchDailyOrders($branch_id, $validDate);
            } else {
                $order = $orderDao->getBranchDailyOrders($branch_id);
            }
            $order->buffer();
            $price = 0;
            foreach ($order as $order_price):
                $price = $price + $order_price->subtotal_order_branch;
            endforeach;
            
            $iva = 0.12 * $price;
            $subtotalBranch = $price + $iva;
            $service = $price * 0.12;
            $ivaService = $service * 0.12;
            $subtotalPr = $service + $ivaService;
            $total = $subtotalBranch + $subtotalPr;
//            echo $subtotalBranch;die;
            $view['order'] = $order;
//            var_dump($view['order']);die;
            $view['comision'] = $service;
            $view['subtotal_branch'] = $subtotalBranch;
            $view['subtotal_pr'] = $subtotalPr;
            $view['iva'] = $iva;
            $view['iva_service'] = $ivaService;
            $view['price'] = $price;
            $view['total'] = $total;
//            var_dump($view['order']);die;
            return new ViewModel($view);
        }
    }

    public function invoiceMonthlyAction() {


        $auth = $this->getService('Franchise\Model\LoginFranchise');
        $month = $this->params()->fromQuery('month', 0);

        if ($month !== 0) {

            date_default_timezone_set('UTC');

            $validator = new Date(array(
                'format' => 'm/Y',
                'locale' => 'UTC'
            ));

            if ($validator->isValid($month)) {

                $validDate = date_parse_from_format("m/Y", date($month));
            } else {
                
            }
        }

        if ($auth->isLoggedIn()) {
            $this->_user = $auth->getIdentity();
            $branch_id = $this->_user->branch_id;
//            var_dump($branch_id);die;
            $orderTableGateway = $this->getService('OrderHydratingTableGateway');
            $orderDao = new OrderDao($orderTableGateway);
            if ($month !== 0) {
                $order = $orderDao->getSearchBranchMonthlyOrders($branch_id, $validDate);
//                var_dump($order);die;
            } else {
                $order = $orderDao->getBranchMonthlyOrders($branch_id);
//                var_dump($order);die;
            }
            $order->buffer();
            $price = 0;
            foreach ($order as $order_price):
//                echo $order_price->subtotal;
                $price = $price + $order_price->subtotal_order_branch;
            endforeach;
//            echo $price;die;
            $iva = 0.12 * $price;
            $subtotalBranch = $price + $iva;
            $service = $price * 0.12;
            $ivaService = $service * 0.12;
            $subtotalPr = $service + $ivaService;
            $total = $subtotalBranch + $subtotalPr;
//            echo $subtotalBranch;die;
            $view['order'] = $order;
//            var_dump($view['order']);die;
            $view['comision'] = $service;
            $view['subtotal_branch'] = $subtotalBranch;
            $view['subtotal_pr'] = $subtotalPr;
            $view['iva'] = $iva;
            $view['iva_service'] = $ivaService;
            $view['price'] = $price;
            $view['total'] = $total;
//            var_dump($view['order']);die;
            return new ViewModel($view);
        }
    }

    public function orderDetailAction() {

        $orderId = (int) $this->params()->fromRoute('id', 0);

        $auth = $this->getService('Franchise\Model\LoginFranchise');
        if ($auth->isLoggedIn()) {
            $this->_user = $auth->getIdentity();
            $branch_id = $this->_user->branch_id;

            $productOrderTableGateway = $this->getService('OrderHydratingTableGateway');
            $orderDao = new OrderDao($productOrderTableGateway);
            $oderDetail = $orderDao->getOrderDetail($orderId);
            $oderDetail = $oderDetail->fetchAllCurrent();
//        var_dump($oderDetail);die;
//        $productOrderTableGateway = $this->getService('OrderHydratingTableGateway');
            $orderDao = new OrderDao($productOrderTableGateway);
            $productOrder = $orderDao->getProductOption($orderId, $branch_id);
            $productOrder = $productOrder->fetchAll();
            $productOrder->buffer();

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

            endforeach;

//            echo $oderDetail->sub_total;die;
//            $subtotalBranch = $comisionPrice + $price;
            $iva = 0.12 * $price;
            $subtotalBranch = $price + $iva;
            $service = $price * 0.12;
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

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

    public function generatepdfAction() {
        $pdf = new PdfModel();
        $this->layout('layout/empty');
        return $pdf;
    }

}
