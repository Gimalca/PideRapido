<?php

namespace Catalog\Controller;

use Account\Model\Dao\UserDao;
use Catalog\Model\Dao\ProductDao;
use Sale\Model\Dao\CartDao;
use Sale\Model\Dao\CustomerDao;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\ArrayObject;
use Zend\View\Model\ViewModel;

class CartController extends AbstractActionController {

    public function indexAction() {
        return new ViewModel();
    }

    public function addAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id', 0);

        $comboHasProductTableGateway = $this->getService('ProductHasBranchHydratingTableGateway');
        $cartDao = new CartDao($comboHasProductTableGateway);
        $products = $cartDao->getProducts($id);
        //var_dump($products);die;

        $user = $this->getService('Account\Model\LoginAccount');

        if ($request->isXmlHttpRequest() && $request->isPost()) {

            $selected = [
                'extras'  => array_filter($this->params()->fromPost('extras', [])),
                'general' => array_filter($this->params()->fromPost('general', []))
            ];

            $options = array_merge(
                array_keys($selected['extras']),
                array_values($selected['general'])
            );
        }

        if ($user->isLoggedIn()) {


            $usrEmail = $user->getIdentity()->email;
//            echo $usrEmail;die;
            $userTableGateway = $this->getService('CustomerTableGateway');
            $cartDao = new UserDao($userTableGateway);
            $usr = $cartDao->getByEmail($usrEmail, array('customer_id', 'cart'));
                  // var_dump($usr);die;
            if ($usr != NULL) {
                $cartJason = $usr->cart;
                if ($cartJason == NULL) {
                    //print_r($products);
                    $cartArray = Array();
                    $productCart = Array();

                    $productCart['product_id'] = $products[0]['product_has_branch_id'];
                    $productCart['options'] = isset($options)? $options: [];

                    $cartJson = Json::encode([$productCart]);
                    //echo $cartJson;die;
                    $usr->cart = $cartJson;
//                  var_dump($usr);die;
                    $userTableGateway = $this->getService('CustomerTableGateway');
                    $cartDao = new CustomerDao($userTableGateway);
                    $cart = $cartDao->savedCustomer($usr);
                    //$cartLayout = $products;
                    //print_r($productCart);die;
                } else {
                    //                    print_r($cartEmpty);
                    $cartJson = Json::decode($cartJason, true);
                    $cartJson = !is_array($cartJson) ? []: $cartJson;
                    $productCart['product_id'] = $products[0]['product_has_branch_id'];
                    $productCart['options'] = isset($options)? $options: [];
                    //array_pu
                    array_push($cartJson, $productCart);
                    $cartJson = Json::encode($cartJson);
                    $usr->cart = $cartJson;
                    //var_dump($usr);die;
                    $userTableGateway = $this->getService('CustomerTableGateway');
                    $cartDao = new CustomerDao($userTableGateway);
                    $cart = $cartDao->savedCustomer($usr);
                    //print_r( $usr->cart);die;
                }
            } else {

                //echo 'no es null'; die;
            }

            if ($request->isXmlHttpRequest() && $request->isPost()) {
                $response = $this->getResponse();
                $response->setStatusCode(201);
                $response->setContent(\Zend\Json\Json::encode(["status" => true]));
                return $response;
            }
            
            $this->flashMessenger()->addMessage('Producto agregado satidfactoriamente!', 'warning');
            
            return $this->redirect()->toRoute('catalog', array( 'controller' => 'cart', 'action' => 'checkout', ));
        }

            if ($request->isXmlHttpRequest() && $request->isPost()) {

                //var_dump($path);die;
                $response = $this->getResponse();
                $response->setStatusCode(401)->getHeaders()
                    ->addHeaders([
                        'redirectUrl' => $request->getServer('HTTP_ORIGIN') . $this->url()->fromRoute('account', array('controller' => 'register'))
                ]);
                $response->setContent(\Zend\Json\Json::encode(["status" => false]));
                return $response;
            }

       $this->flashMessenger()->addMessage('Para comprar debe iniciar session!', 'warning');
       return $this->redirect()->toRoute('account', array( 'controller' => 'register', ));

    }

    public function checkoutAction(){

         $user = $this->getService('Account\Model\LoginAccount');

        if ($user->isLoggedIn()) {

            $usrEmail = $user->getIdentity()->email;

            $userTableGateway = $this->getService('CustomerTableGateway');
            $cartDao = new UserDao($userTableGateway);
            $usr = $cartDao->getByEmail($usrEmail, array('customer_id', 'cart'));

             $cartJson = $usr->cart;
             $cartArray= Json::decode($cartJson, true);
             //print_r($cartArray); //die;
             $productDao = new ProductDao($this->getService('ProductHydratingTableGateway'));

             $products = array();
             foreach ($cartArray as $value) {

//                 $productResult = $productDao->getProductHasBranch($value['product_id']);
//                 $products[] = $productResult->current()->getArrayCopy();


                 $productsOptions = new CartDao($this->getService('ProductHasBranchHydratingTableGateway'));
                 $productResult = $productsOptions->productOptionsPrice($value['product_id'], $value['options']);
                 $detailProduct = $productResult->current();
                 $detailProduct->cart = $value;
                 $products[] = $detailProduct;
               // print_r($products);

             }
            //die;
            $view['products'] = $products;


            return new ViewModel($view);
        }


            $this->flashMessenger()->addMessage('Para comprar debe iniciar session!', 'warning');
            return $this->redirect()->toRoute('account', array(
                                       'controller' => 'register',

                           ));

    }

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);
        return $service;
    }

    public function deleteAction() {

        $request = $this->getRequest();

        $id = (int) $this->params()->fromRoute('id', 0);

        $comboHasProductTableGateway = $this->getService('ProductHasBranchHydratingTableGateway');
        $userTableGateway = $this->getService('CustomerTableGateway');
        $user = $this->getService('Account\Model\LoginAccount');

        $userDao = new UserDao($userTableGateway);
        $cartDao = new CartDao($comboHasProductTableGateway);
        //var_dump($products);die;
        if ($user->isLoggedIn()) {
            $usrEmail = $user->getIdentity()->email;
            $usr = $userDao->getByEmail($usrEmail, array('customer_id', 'cart'));
            $userCart = Json::decode($usr->cart, true);

            if ($id >= 0 && $id <= sizeof($userCart)) {
                unset($userCart[$id]);
                $userCart = array_values($userCart);
            }

            if (!is_array($userCart) || empty($userCart)) {
                $cartJson = Json::encode([]);
            } else {
                $cartJson = Json::encode($userCart);
            }

            $usr->cart = $cartJson;
            $customerDao = new CustomerDao($userTableGateway);
            $customerDao->savedCustomer($usr);
        }
        $this->redirectBack();
    }

    public function redirectBack() {
        // we redirect back
        $referer = $this->getRequest()->getHeader('Referer');

        if ($referer) {
            $refererUrl = $referer->uri()->getPath(); // referer url
            $refererHost = $referer->uri()->getHost(); // referer host
            $host = $this->getRequest()->getUri()->getHost(); // current host

            // only redirect to previous page if request comes from same host
            if ($refererUrl && ($refererHost == $host)) {
                return $this->redirect()->toUrl($refererUrl);
            }
        }
        // redirect to home if no referer or from another page
        return $this->redirect()->toRoute('index');
    }

}
