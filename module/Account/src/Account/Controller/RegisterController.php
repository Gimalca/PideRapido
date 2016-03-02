<?php

namespace Account\Controller;

use Account\Form\LoginForm;
use Account\Form\RegisterForm;
use Account\Form\Validator\RegisterValidator;
use Account\Model\Dao\UserDao;
use Account\Model\LoginFacebook;
use Exception;
use ReCaptcha\ReCaptcha;
use Sale\Model\Dao\CustomerDao;
use Sale\Model\Entity\Customer;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Mail\Message as MailMessage;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RegisterController extends AbstractActionController
{

    public function indexAction()
    {
       // $request = $this->getRequest();
        $registerForm = new RegisterForm();
        $loginForm = new LoginForm();
        
        //Validamos si exite forward desde login
        $forwardLogin = $this->params()->fromRoute('loginForm', false);
        if($forwardLogin){
            
            $view['loginForm'] =  $forwardLogin;
        }else{
            $view['loginForm'] = $loginForm; 
        }
        
        //$view['linkfb'] = $this->facebookUrl();
        $view['registerForm'] = $registerForm; 
        
         
        $viewModel =  new ViewModel($view);
        $viewModel->setTemplate('account/register/add.phtml');
                
        return $viewModel;
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $registerForm = new RegisterForm();
        $loginForm = new LoginForm();
        
        //print_r($registerForm->p);die;
        
        if ($request->isPost()) {
            //recibimos los datos del form
            $postData = $request->getPost();
            
            
             $secret = '6LctMxkTAAAAAOVRFZwxvOzbX6nDxVxXb7t5bbPF';
             $postReCaptcha = $postData['g-recaptcha-response'] ;
             
             $reCaptcha = new ReCaptcha($secret);
             $resp = $reCaptcha->verify($postReCaptcha);
             
            //validamos los datos enviados
            $registerValidator = $registerForm->setInputFilter(New RegisterValidator($this->getServiceLocator()));
            $registerValidator->setData($postData);
           
            if ($registerValidator->isValid() && $resp->isSuccess()) {  //si son validados continuamos
            
                //obtenemos los datos validados y filtrados 
                $registerData = $registerValidator->getData();
                //seteamos los campos requeridos en la Tabla
                $customerPrepareData = $this->prepareDataCustomer($registerData);
                
                $this->savedCustomer($customerPrepareData);
                
            } else {
                $registerForm->get('firstname')->setAttribute('autofocus', true);
                $messages = $registerValidator->getMessages();
                
                ($resp->isSuccess()) ?  $messages:  $messages['recaptcha'] = 'No pudimos verificar su identidad, verifique el recaptcha'; 
                //print_r($messages);die;
                $this->flashMessenger()->addMessage($messages, 'error');
            }
        }
        
        //$view['linkfb'] = $this->facebookUrl();
        $view['loginForm'] = $loginForm;
        $view['registerForm'] = $registerForm; 

        return new ViewModel($view);
    }
    
    public function facebookAction()
    {
        $fbData = $this->params()->fromRoute('fblogin', false);
        
        $registerData['firstname'] = $fbData['name'];
        $registerData['email']     = $fbData['email'];
        $registerData['password']  = $fbData['email'];
        
              
        $customerPrepareData = $this->prepareDataCustomer($registerData);
        
        $saved = $this->savedCustomer($customerPrepareData);
        
        if($saved){
            
            $this->loginFb($customerPrepareData['email'], $customerPrepareData['password']);
         
            
        }else{
            
            echo ' no guardo registro';
        }
      
    }
    
    private function savedCustomer($customerPrepareData)
    {
         //incializamos la clase Entity Customer y le inyectamos los datos
                $customerEntity = new Customer($customerPrepareData);
                
                //traemos el servicio CustomerTableGateway el cual nos trae el Adapter de la DB
                $customerTableGateway = $this->getService('CustomerTableGateway');
                //inicializamos la clase CustomerDao y le inyectamos el Adapter
                $customerDao = new CustomerDao($customerTableGateway);
                //Salvamos el registro en la bd
                //print_r($customerEntity);die;
                $saved = $customerDao->savedCustomer($customerEntity);
                // print_r($customerPrepareData);die;
              
                if ($saved) { //si se guardo la fila en la BD continuamos
                    //enviamos el mail con
                    $sendMail = $this->sendMailRegisterConfirm($customerEntity);
                    //seteamos el mesanje de registro exitoso
                    $this->flashMessenger()->addMessage("Registro exitoso!!. Acabamos de enviarle un correo a $customerEntity->email para confirmar su cuenta", 'success');
                    
                    return true;
                    //$this->flashMessenger()->addMessage("Bienvenido $customerEntity->firstname $customerEntity->lastname !!. Acabamos de enviarle un correo de confirmacion ", 'success');
                } else {
                    $this->flashMessenger()->addMessage("Disculpe no pudimos procesar su registro ", "error");
                    // throw new \Exception("Not Save Row");
                    
                    return false;
                }         
        
    }
     private function facebookUrl()
    {
        
        $globalConfig = $this->getService('config');
        $fb = new LoginFacebook($globalConfig['fbapi']);
                 
         $fbUrl = $fb->loginUrl($globalConfig['fbapi']['fbcallback']);
                
        return $fbUrl;
    }
    
     private function loginFb($email, $passwordEncript)
    {
             $loginAccount = $this->getService('Account\Model\LoginAccount');                

                try {
                    
                    $loginAccount->login($email, $passwordEncript);
                    $this->flashMessenger()->addMessage('Login Correcto!', 'success');
                    
                     return $this->redirect()->toRoute('account');
                     
                } catch (\Exception $e) {
                    //$this->layout()->mensaje = $e->getMessage();
                     $this->flashMessenger()->addMessage( $e->getMessage(), 'error');
                    return $this->forward()->dispatch('Account\Controller\Register', array('action' => 'index'));
                }
        
    }

    private function prepareDataCustomer($customerData)
    {
        $remote = new RemoteAddress;
        $ipClient = $remote->getIpAddress();

        $customerData['address_id'] = 0;
        $customerData['ip'] = $ipClient;
        $customerData['status'] = 0;
        $customerData['approved'] = 0;
        $customerData['address_default'] = 0;
        $customerData['register_complete'] = 0;
        $customerData['newsletter'] = 1;
        $customerData['salt'] = time();
        $customerData['password'] = md5($customerData['password'] . $customerData['salt']);
        $customerData['token'] = md5(uniqid(mt_rand(), true));
        $customerData['email_confirmed'] = 0;
        $customerData['date_added'] = date("Y-m-d H:i:s");

        return $customerData;
    }
    
    private function sendMailRegisterConfirm($customerEntity)
    {
        $render = $this->getService('ViewRenderer');       
        $content = $render->render('account/register/email/confirm-email',array(
                'customer' => $customerEntity,
                'link' => 'http://piderapido.com' . $this->url()->fromRoute('account', array(
                                    'controller' => 'register',
                                    'action' => 'confirm-email',
                                    'id' => $customerEntity->token))
                ));
        

    // make a header as html  
    $html = new MimePart($content);  
    $html->type = "text/html";  
    $body = new MimeMessage();  
    $body->setParts(array($html));  
        
    //print_r($body);die;
    $mailer = $this->getServiceLocator()->get('Mailer');
            $message = new MailMessage;
            $message->setBody($body);
            $message->addTo( $customerEntity->email)
                    ->addFrom('noreply@piderapido.com')
                    ->setSubject('Piderapido.com, confirma tu registro!');
                    
            
     $sendMail =  $mailer->send($message);
           
             
    return $sendMail;         
    }

    public function confirmEmailAction()
    {
        $token = $this->params()->fromRoute('id');
        // echo print_r($token).' - token';die;
        $viewModel = new ViewModel(array('token' => $token));
        try {
            //traemos el servicio CustomerTableGateway el cual nos trae el Adapter de la DB
            $customerTableGateway = $this->getService('CustomerTableGateway');
            //inicializamos la clase CustomerDao y le inyectamos el Adapter
            $userDao = new UserDao($customerTableGateway);
            $user = $userDao->getUserByToken($token);
            //print_r($user);die;
            $usr_id = $user->customer_id;
            //print_r($user);die;
            $userDao->activateUser($usr_id);
            
            $loginAccount = $this->getService('Account\Model\LoginAccount');
            
            $loginAccount->login($user->email, $user->password);
            $this->flashMessenger()->addMessage("Usuario Activado", 'success');
                    
            return $this->redirect()->toRoute('account');
            //seteamos el mesanje de registro exitoso
      
//             return $this->redirect()->toRoute('account', array(
//                        'controller' => 'register',
//                        'action' => 'add'
//            ));
             
        } catch (Exception $e) {
             $this->flashMessenger()->addMessage("Token invalido", "error");
              return $this->redirect()->toRoute('account', array(
                        'controller' => 'register',
                        'action' => 'add'
            ));
             //echo $e->getMessage();
            //$viewModel->setTemplate('auth/registration/confirm-email-error.phtml');
        }
        return $viewModel;
    }
    
     public function getService($serviceName)
    {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

}
