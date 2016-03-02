<?php

namespace Payment\Controller;

use Account\Model\Dao\UserDao;
use Catalog\Model\Dao\ProductDao;
use Sale\Model\Dao\CartDao;
use Sale\Model\Dao\CustomerDao;
use Sale\Model\Dao\OrderDao;
use Sale\Model\Entity\Customer;
use Sale\Model\Entity\Order;
use Sale\Model\Entity\OrderBranch;
use Sale\Model\Entity\Payment;
use SebastianBergmann\RecursionContext\Exception;
use Zend\Http\Client;
use Zend\Json\Json;
use Zend\Mail\Message as MailMessage;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CheckoutController extends AbstractActionController
{
    private $user;

    public function indexAction()
    {
        return new ViewModel();
    }

    public function orderCartAction()
    {
        $this->user = $this->getService('Account\Model\LoginAccount');
         
        if ($this->user->isLoggedIn()) {
            
            if ($this->user->getIdentity()->register_complete == 0) {
                $this->flashMessenger()->addMessage("Necesita actualizar sus datos!! ", 'warning');
                return $this->redirect()->toRoute('account', array(
                            'controller' => 'Information',
                            'action' => 'edit'
                ));
            }
            
            $usrEmail = $this->user->getIdentity()->email;
            
            $userTableGateway = $this->getService('CustomerTableGateway');
            $cartDao = new UserDao($userTableGateway);
            $usr = $cartDao->getByEmail($usrEmail, array('customer_id', 'cart'));
            
             $cartJason = $usr->cart;
             $cartArray= Json::decode($cartJason, true);
             
             //print_r($usr);die;
             
             $productDao = new ProductDao($this->getService('ProductHydratingTableGateway'));
             
             $products = array();
             
             
             $priceBase = 0;
             
             foreach ($cartArray as $value) {
                 
//               $productResult = $productDao->getProductHasBranch($value['product_id']);
                 
                 $productsOptions = new CartDao($this->getService('ProductHasBranchHydratingTableGateway'));
                 $productResult = $productsOptions->productOptionsPrice($value['product_id'], $value['options']);
                   
                 $rowProduct = $productResult->current();
                 $rowProduct->options = $value['options'];
                 
                 $products[] = $rowProduct;
                 
                 $priceBase = $priceBase + $rowProduct->total ;
                    
             }
             
              $prices = $this->calculatePrices($priceBase);

              $order = $this->prepareDataOrder($prices);

              $orderEntity = new Order($order); 
              
              $orderDao = new OrderDao($this->getService('OrderTableGateway'));
              
              $orderPending = $orderDao->getAll()
                      ->where(array('customer_id' => $orderEntity->customer_id, 'order_status' => 1))
                      ->order('order_id DESC')
                      ->fetchAll();
              
              if($orderPending->count() > 0){
                  $orderEntity->order_id = $orderPending->current()->order_id ;
              }
             
              $orderId = $orderDao->saveOrder($orderEntity);
              
               //print_r($orderEntity);die;
            if($orderId){
                
                $orderDao->deleteProductsOrder($orderId);
                 
                $saveProducts = $orderDao->saveProductsOrder($orderId, $products);
                
                
                
                return $this->forward()->dispatch('Payment\Controller\Checkout', array(
                            'action' => 'payMethod',
                            'id' => $orderId
                           
                ));
                
            }
           
        }
        
        $this->flashMessenger()->addMessage('Para comprar debe iniciar session!', 'warning');
            return $this->redirect()->toRoute('account', array(
                                       'controller' => 'register',

                           ));
        
        //return new ViewModel();
    }
    
    private function calculatePrices($priceBase)
    {
             $price['base'] =  number_format($priceBase, 2, '.', '');
             $comision = 1.12;
             $iva = 1.12;
             
             $price['subtotal'] = $priceBase * $comision ;
             $total = $price['subtotal'] * $iva;
             $price['total_payment'] =  number_format($total, 2, '.', '');
        
             return $price;
    }

    private function prepareDataOrder($prices)
    { 
       // print_r($this->user->getIdentity());
    
        $dataOrder['invoice_number'] = $this->user->getIdentity()->customer_id.$this->randomDigits(4);
        $dataOrder['address_id'] = $this->user->getIdentity()->address_default;
        $dataOrder['customer_id'] = $this->user->getIdentity()->customer_id;
        $dataOrder['customer_id'] = $this->user->getIdentity()->customer_id;
        $dataOrder['subtotal'] = $prices['subtotal'];
        $dataOrder['total_base'] = $prices['base'];
        $dataOrder['total_payment'] = $prices['total_payment'];
        $dataOrder['order_status'] = 1;
        $dataOrder['order_dispatch'] = 0;
        $dataOrder['date_added'] = date("Y-m-d H:i:s");
        $dataOrder['date_modified'] = date("Y-m-d H:i:s");
      
        
        return $dataOrder;
    }
    
    private function branchOrders($orderId)
    {
         $orderDao = new OrderDao($this->getService('OrderHydratingTableGateway'));
         $orderProducts = $orderDao->getAll(array('order_id', 'invoice_number', 'customer_id'))
                 ->whithProduct(array('order_product_id', 'product_has_branch_id', 'price', 'total'))
                 ->where(array('pr_order.order_id' => $orderId))
                 ->fetchAll();
         $orderProducts = $orderProducts->buffer();
         $result = $orderProducts->toArray();
         //print_r($result);die;
         //$brachId = $orderProducts->current()->branch_id;
         $brachId = array();
        
         $branchProducts = array_reduce($result, function($branchProducts, $product) {
            if (isset($branchProducts[$product['branch_id']]['total'])) {
                $branchProducts[$product['branch_id']]['total'] += $product['total'];
            } else {
                $branchProducts[$product['branch_id']]['total'] = $product['total'];
            }

            return $branchProducts;
        }, []);

        //print_r($branchProducts);die;
         foreach ($orderProducts as $product) {
         
             
             //print_r($product);
             if(!in_array($product->branch_id, $brachId) ){ 
                 
                 $dataOrderBranch = $this->prepareDataOrderBranch($product);
                 $orderBranch = new OrderBranch($dataOrderBranch);
                 $orderBranch->subtotal = $branchProducts[$product->branch_id]['total'];
                 //print_r($orderBranch);die;
                 $orderBranch = $orderDao->saveOrderBranch($orderBranch);
                 
                
             } 
             //die;
             $orderDao->saveProductsOrderBranch($orderBranch, $product->order_product_id);
             
          array_push( $brachId, $product->branch_id);
         }
        return;
    }
    
      private function prepareDataOrderBranch($product)
    { 
       // print_r($this->user->getIdentity());
    
        $dataOrder['invoice_number_branch'] = $product->branch_id.$this->randomDigits(4);
        $dataOrder['branch_id'] = $product->branch_id;
        $dataOrder['order_id'] =  $product->order_id;
        $dataOrder['customer_id'] = $product->customer_id;
        $dataOrder['invoice_number'] = $product->invoice_number;
        $dataOrder['order_status'] = 1; 
        $dataOrder['order_dispatch'] = 0;
        //$dataOrder['subtotal'] = $totalPayment;  
        $dataOrder['date_added'] = date("Y-m-d H:i:s");
        $dataOrder['date_modified'] = date("Y-m-d H:i:s");
      
        
        return $dataOrder;
    }
            
    public function orderConfirmationAction()
    {
       
        
        return new ViewModel();
    }

    public function payMethodAction()
    {
        $this->user = $this->getService('Account\Model\LoginAccount');

        if (!$this->user->isLoggedIn()) {
            $this->flashMessenger()->addMessage('Para comprar debe iniciar session!', 'warning');
            return $this->redirect()->toRoute('account', array(
                        'controller' => 'register',
            ));
        }

        if ($this->user->getIdentity()->register_complete == 0) {
            $this->flashMessenger()->addMessage("Necesita actualizar sus datos!! ", 'warning');
            return $this->redirect()->toRoute('account', array(
                        'controller' => 'Information',
                        'action' => 'edit'
            ));
        }

        $orderId = $this->params()->fromRoute('id', false);

        //print_r($orderId);die;
        if (!$orderId) {
            $this->flashMessenger()->addMessage("Hubo un problema con su orden!", 'warning');
            return $this->redirect()->toRoute('catalog', array(
                        'controller' => 'cart',
                        'action' => 'checkout'
            ));
        }

        $orderDao = new OrderDao($this->getService('OrderTableGateway'));
        $order = $orderDao->getOrder($orderId)
                ->fetchAll();

        $view['order'] = $order->current();

        $view['boton123'] = $this->pago123Button($order->current(), $this->user->getIdentity());
        //$view['boton123'] = '';

        return new ViewModel($view);
    }

    public function paySendAction()
    {
       
        return new ViewModel();
    }

    public function payConfirmAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $returnPago = $request->getPost()->toArray();
           
            
            if(!empty($returnPago['inv'])){ //input de prueba
                
              $returnPago ['parametro3'] = '555555********5555';
              $returnPago ['parametro4'] = 183851;
              $returnPago ['parametro1'] = '00';
              $returnPago ['parametro2'] = $returnPago['inv'];
            }

            $orderDao = new OrderDao($this->getService('OrderHydratingTableGateway'));
            $order = $orderDao->getAll()
//                    ->whithProduct(array('order_product_id', 'product_has_branch_id', 'total'))
//                    ->whithProductDescription(array('*'))
                    ->whithCustomer(array('customer_id','firstname','lastname','email'))
                    ->where(array('invoice_number' => $returnPago['parametro2']))
                    ->fetchAll();
            $orderCustomer = $order->current();

            $orderList = $orderDao->getOrderProducts($orderCustomer->order_id);
            
              $orderList->buffer();
            if($orderList->count() > 0){
                
              $order =  $orderList->current();
              
                if($returnPago['parametro1'] == '00'){

                    $order->order_status = 2;
                    $order->date_modified = date("Y-m-d H:i:s");
                    $orderUpdate = new Order($order->getArrayCopy());
                    $orderSave = $orderDao->saveOrder($orderUpdate);
                    
                    $branchOrders = $this->branchOrders($order->order_id);
                    
                    try {
                        $this->sendMailPayConfirm($orderCustomer, $orderList);
                        
                    } catch (Exception $e) {
                        $this->flashMessenger()->addMessage("Compra registrada!, ocurrio un error al enviar email de confirmacion. Contacte a soporte@piderapido.com", "warning");
                        return $this->redirect()->toRoute('catalog', array(
                                    'controller' => 'cart',
                                    'action' => 'checkout'
                        ));
                    }
                    
                    $customer = new Customer(['customer_id' =>  $order->customer_id, 'cart' => '[]']);
                    $customerDao = new CustomerDao($this->getService('CustomerTableGateway'));
                    $updateCart = $customerDao->savedCustomer($customer);
                    
                    
                    
                }else{
                    
                    $order->order_status = 3;
                    $order->date_modified = date("Y-m-d H:i:s");
                    $orderUpdate = new Order($order->getArrayCopy());
                    $orderSave = $orderDao->saveOrder($orderUpdate);
                }
                                
                $payconfirm = new Payment($order->getArrayCopy()); 
                $payconfirm->cod = $returnPago ['parametro1'];
                $payconfirm->tdc = $returnPago ['parametro3'];
                $payconfirm->num_confirm = $returnPago ['parametro4'];
                $payconfirm->date_added = date("Y-m-d H:i:s");
                $savePay = $orderDao->savePayConfirm($payconfirm);
 
            }
          //echo 'registrado'; die();
        }

      
    }
    
    private function sendMailPayConfirm($orderCustomer, $order )
    {
       
        $render = $this->getService('ViewRenderer');       
        $content = $render->render('payment/checkout/email/pay-confirm',array(
                'order' => $orderCustomer,
                'products' => $order
               ));
        

        
    // make a header as html  
    $html = new MimePart($content);  
    $html->type = "text/html";  
    $body = new MimeMessage();  
    $body->setParts(array($html));  
  
    $mailer = $this->getServiceLocator()->get('Mailer');
            $message = new MailMessage;
            $message->setBody($body);
            $message->addTo( $orderCustomer->email)
                    ->addFrom('noreply@piderapido.com')
                    ->setSubject('Piderapido.com , Gracias por tu compra!');
                    
            
     return $mailer->send($message);
           
                  
    }

    public function paySummaryAction()
    {
         $this->user = $this->getService('Account\Model\LoginAccount');
         
         $user = $this->user->getIdentity();
         //print_r($user);die;
        if ($this->user->isLoggedIn()) {
            
             $orderDao = new OrderDao($this->getService('OrderTableGateway'));
              
              $orderPending = $orderDao->getAll()
                      ->where(array('customer_id' => $user->customer_id, 'order_status' => 2))
                      ->order('order_id DESC')
                      ->fetchAll();
              
              if ($orderPending->count() > 0) {
                //print_r($orderPending->current());

                return $this->redirect()->toRoute('account', array(
                            'controller' => 'OrderHistory',
                            'action' => 'detail',
                            'id' => $orderPending->current()->order_id
                ));
            }
        }

        return new ViewModel();
    }
    
    private function pago123Button($orderEntity, $customer)
    {
        $client = new Client();
                  
                $client->setOptions(array(
                    'strictredirects' => true,
                    'maxredirects' => 0,
                    'timeo-ut' => 30,
                    'sslcapath' => '/home/piderapido0515/ssl/certs/',
                    'adapter' => 'Zend\Http\Client\Adapter\Curl'
                ));
                
                $ciSplit = preg_split('/([J,V])/', $customer->document_identity ,null,PREG_SPLIT_DELIM_CAPTURE);
                $document_identity = $ciSplit[2];
                //$type_document_identity = $ciSplit[1];
                
                $nbproveedor = 'PIDE RAPIDO';
                $cs = '5d76add5a35262d68ecdde93fa154d79';
                // Setting several POST parameters, one of them with several values
                $params = array(
                    'nbproveedor' => $nbproveedor,
                    'nb' => strtolower($customer->firstname),
                    'ap' => strtolower($customer->lastname),
                    'ci' => $document_identity,
                    'em' => $customer->email,
                    'cs' => $cs,
                    'nai' => $orderEntity->invoice_number,
                    'co' => 'PIDE RAPIDO',
                    'mt' => $orderEntity->total_payment,
                    'tl' => $customer->telephone,
                    'ancho' => '190px',
                    'id' => 381
                );
                
                
                
                $client->setParameterPost($params);
                $client->setUri('https://123pago.net/msBotonDePago/index.jsp');
                //$client->setUri('http://190.153.48.117/msBotonDePago/index.jsp');
                $client->setMethod('POST');
                
                //print_r($params);
                $response = $client->send();
                //echo $response->getBody(); die;
             
                return $response->getBody();
    }
    
    private function randomDigits($length)
    {
        $digits = 0;
        $numbers = range(0, 9);
        shuffle($numbers);
        for ($i = 0; $i < $length; $i++)
            $digits .= $numbers[$i];
        
        return $digits;
    }

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);
       

        return $service;
    }


}

