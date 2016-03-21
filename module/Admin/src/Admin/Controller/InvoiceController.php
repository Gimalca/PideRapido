<?php

namespace Admin\Controller;

use Admin\Form\BranchList;
use Admin\Form\FranchiseList;
use DOMPDFModule\View\Model\PdfModel;
use Franchise\Model\Dao\BranchDao;
use Franchise\Model\Dao\FranchiseDao;
use Sale\Model\Dao\OrderDao;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;

class InvoiceController extends AbstractActionController {

    private $_user;

    public function listFranchiseAction() {
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

    public function listAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id', 0);
        $branchListSelect = new BranchList();

        if ($request->isPost()) {
            $postData = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
            $branch_id = $postData['branch_id'];
//                var_dump($product_id);die;
            $branchList = $this->getBranchSelect($id);
            $branchListSelect->get('branch_id')->setValueOptions($branchList);

            $branchTableGateway = $this->getService('BranchTableGateway');
            $branchDao = new BranchDao($branchTableGateway);
            $branch = $branchDao->getBranchSelect($branch_id);
//            var_dump($branch);die;
            
            $view['branch'] = $branch;
            $view['branch_list'] = $branchListSelect;
            $view['frnachise_id'] = $id;
            return new ViewModel($view);
        } else {
            $branchList = $this->getBranchSelect($id);
            $branchListSelect->get('branch_id')->setValueOptions($branchList);
            $branchTableGateway = $this->getService('BranchTableGateway');
            $branchDao = new BranchDao($branchTableGateway);
            $branch = $branchDao->getAll($id);
            
            $view['branch'] = $branch;
            $view['branch_list'] = $branchListSelect;
            $view['franchise_id'] = $id;
//            var_dump($view['frnachis_id']);die;
            //print_r($branch->getArrayCopy());die;
            return new ViewModel($view);
        }
    }
    private function getBranchSelect($id) {
        
        $branchTableGateway = $this->getService('BranchTableGateway');
        $branchDao = new BranchDao($branchTableGateway);
        $branch = $branchDao->getAll($id);
        $result = array();
        foreach ($branch as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->branch_id] = $bankR->name;
        }
        return $result;
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

    public function invoiceDailyAction() {

        $auth = $this->getService('Admin\Model\LoginAdmin');
        $day = $this->params()->fromQuery('day', 0);
        $branch_id = (int) $this->params()->fromRoute('id', 0);

        if ($day !== 0) {

            date_default_timezone_set('UTC');

            $validator = new Date(array(
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
//            var_dump($branch_id);die;
            $orderTableGateway = $this->getService('OrderHydratingTableGateway');
            $orderDao = new OrderDao($orderTableGateway);
            if ($day !== 0) {
                $order = $orderDao->getSearchBranchDailyOrders($branch_id, $validDate);
            } else {
                $order = $orderDao->getBranchDailyOrders($branch_id);
            }
            $order->buffer();
            //print_r($order->current());die;
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


        $auth = $this->getService('Admin\Model\LoginAdmin');
        $month = $this->params()->fromQuery('month', 0);
        $branch_id = (int) $this->params()->fromRoute('id', 0);

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
            //print_r($order->current());die;
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
            $view['branch'] = $branch_id;
//            var_dump($view['order']);die;
            return new ViewModel($view);
        }
    }

    public function orderDetailAction() {

        $orderId = (int) $this->params()->fromRoute('id', 0);
        $branch_id = (int) $this->params()->fromRoute('idd', 0);
        //echo $branch_id;die;

        $auth = $this->getService('Admin\Model\LoginAdmin');
        if ($auth->isLoggedIn()) {
            $this->_user = $auth->getIdentity();

            $productOrderTableGateway = $this->getService('OrderHydratingTableGateway');
            $orderDao = new OrderDao($productOrderTableGateway);
            $order = $orderDao->getOrderDetail($orderId);
                   $order->where(['ob.order_branch_id' =>  $branch_id]);
            $oderDetail = $order->fetchAllCurrent();
//        var_dump($oderDetail);die;
//        $productOrderTableGateway = $this->getService('OrderHydratingTableGateway');
            $orderDao = new OrderDao($productOrderTableGateway);
//            echo $branch_id;die;
            $productOrder = $orderDao->getProductOption($orderId, $branch_id);
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
