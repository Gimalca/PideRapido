<?php

namespace Account\Controller;

use Account\Form\ForgetForm;
use Account\Form\LoginForm;
use Account\Form\RespasswordForm;
use Account\Form\Validator\LoginValidator;
use Account\Model\Dao\UserDao;
use Account\Model\LoginFacebook;
use ReCaptcha\ReCaptcha;
use Sale\Model\Dao\CustomerDao;
use Zend\Mail\Message as MailMessage;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LoginController extends AbstractActionController
{

    public function indexAction()
    {
        $request = $this->getRequest();
        $loginForm = new LoginForm();

        if ($request->isPost()) {

            $postData = $request->getPost();

            $loginValidator = $loginForm->setInputFilter(new LoginValidator());
            $loginValidator->setData($postData);

            $secret = '6LctMxkTAAAAAOVRFZwxvOzbX6nDxVxXb7t5bbPF';
            $postReCaptcha = $postData['g-recaptcha-response'];

            $reCaptcha = new ReCaptcha($secret);
            $resp = $reCaptcha->verify($postReCaptcha);

            if ($loginValidator->isValid() && $resp->isSuccess()) {

                $loginData = $loginValidator->getData();

                $email = $loginData['email'];
                $password = $loginData['password'];

                $customerTableGateway = $this->getService('CustomerTableGateway');
                $customerDao = new UserDao($customerTableGateway);
                $customerData = $customerDao->getByEmail($email, array('customer_id', 'email', 'salt'));

                //print_r($customerData);die;
                $passwordEncript = md5($password . $customerData->salt);

                $this->login($email, $passwordEncript);
            } else {
                $messages = $loginForm->getMessages();
                //print_r($messages);die;
                if (!$resp->isSuccess()) {
                    $this->flashMessenger()->addMessage('No pudimos verificar su identidad, verifique el recaptcha', 'error');
                };
                $this->flashMessenger()->addMessage('Login incorrecto verifique sus datos!', 'error');
                return $this->forward()->dispatch('Account\Controller\Register', array(
                            'action' => 'index',
                            'forwardLogin' => true,
                            'loginForm' => $loginForm
                ));
            }
        }

        return $this->forward()->dispatch('Account\Controller\Register', array('action' => 'index'));
    }

    public function facebookCallbackAction()
    {
        $globalConfig = $this->getService('config');
        $fb = new LoginFacebook($globalConfig['fbapi']);
        $fbLogin = $fb->getUser();

        if ($fbLogin) {

            $fbLogin->asArray();
            $email = $fbLogin['email'];

            $customerTableGateway = $this->getService('CustomerTableGateway');
            $customerDao = new UserDao($customerTableGateway);
            $customerData = $customerDao->getByEmail($email, array('customer_id', 'password'));

            if (empty($customerData)) {

                return $this->forward()->dispatch('Account\Controller\Register', array(
                            'action' => 'facebook',
                            'fblogin' => $fbLogin->asArray()
                ));
            } else {

                $this->login($email, $customerData->password);
            }
        } else {
            $this->flashMessenger()->addMessage('No pudimos verificar sus datos !', 'error');

            return $this->redirect()->toRoute('account', array(
                        'controller' => 'login',
            ));
        }
    }

    public function resPasswordAction()
    {
        $request = $this->getRequest();
        $email = $request->getPost('email');
        
        $resForm = new RespasswordForm();

        if ($request->isPost() && isset($email)) {
            
            $userDao = new UserDao($this->getService('CustomerTableGateway'));
            $userRow = $userDao->getByEmail($email, ['*']);
            
            if(empty($userRow->token)){
                
                $this->flashMessenger()->addMessage("Email invalido", 'error');
                return $this->forward()->dispatch('Account\Controller\Register', array('action' => 'index'));
                
            }  else {
                
                $this->sendMailResetPassword($userRow);
                $this->flashMessenger()->addMessage("Revise su email para restablecer su cuenta", 'success');
                return $this->forward()->dispatch('Account\Controller\Register', array('action' => 'index'));
            }
           
            
            
        }
        
        
    }   
    public function forgetAction()
    {   
        $token = $this->params()->fromRoute('id', 0);
        
        $request = $this->getRequest();
        $forgetForm = new ForgetForm();
        $userDao = new UserDao($this->getService('CustomerTableGateway'));
        
        if ($request->isPost()) {

            $postData = $request->getPost();
            //$forgetValidate = $forgetForm->setInputFilter(new ForgetValidator());
            $forgetForm->setData($postData);

            if ($forgetForm->isValid()) {
                
                $customerRow = $userDao->getUserByToken($postData->token);
                //print_r($customerRow);
                $customerRow->password = md5($postData->password . $customerRow->salt);
                
                $customerDao = new CustomerDao($this->getService('CustomerTableGateway'));
                $savePassword = $customerDao->savedCustomer($customerRow);
                //print_r($savePassword);die;
                if($savePassword){
                    $this->flashMessenger()->addMessage("Contraseña restaurada para $customerRow->email", 'success');    
                }else{
                    $this->flashMessenger()->addMessage("Tenemos problemas para restablecer su cuenta, contacte a soporte@piderapido.com", 'error');  
                }
                
                return $this->forward()->dispatch('Account\Controller\Register', array('action' => 'index'));
                
            } else {
                
                $messages = $forgetForm->getMessages();
                //print_r($messages);di
                $this->flashMessenger()->addMessage($messages, 'error');
            }
        }
        
        
        $userRow = $userDao->getUserByToken($token);
        
        if($userRow){
            
            $forgetForm->get('token')->setValue($userRow->token);
        }else{
            $this->flashMessenger()->addMessage("Token invalido", 'error');
            return $this->forward()->dispatch('Account\Controller\Register', array('action' => 'index'));
        }
        
        $view['forgetForm'] = $forgetForm;

        return new ViewModel($view);
    }

    public function logoutAction()
    {
        $loginAccount = $this->getService('Account\Model\LoginAccount');
        $loginAccount->logout();
        //seteamos el mesanje de registro exitoso
        $this->flashMessenger()->addMessage("Sesión Finalizada", 'success');

        return $this->redirect()->toRoute('account', array(
                    'controller' => 'register',
                    'action' => 'add'
        ));
    }

    private function login($email, $passwordEncript)
    {
        $loginAccount = $this->getService('Account\Model\LoginAccount');

        try {

            $loginAccount->login($email, $passwordEncript);
            $this->flashMessenger()->addMessage('Login Correcto!', 'success');

            return $this->redirect()->toRoute('account');
        } catch (Exception $e) {
            //$this->layout()->mensaje = $e->getMessage();
            $this->flashMessenger()->addMessage($e->getMessage(), 'error');
            return $this->forward()->dispatch('Account\Controller\Register', array('action' => 'index'));
        }
    }
    
    private function sendMailResetPassword($customerEntity)
    {
        $render = $this->getService('ViewRenderer');       
        $content = $render->render('account/login/email/res-password',array(
                'customer' => $customerEntity,
                'link' => 'http://piderapido.com' . $this->url()->fromRoute('account', array(
                                    'controller' => 'login',
                                    'action' => 'forget',
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

    public function getService($serviceName)
    {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

}
