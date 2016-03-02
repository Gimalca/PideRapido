<?php

namespace Admin\Controller;

use Admin\Form\LoginAdminForm;
use Admin\Form\Validator\LoginAdminValidator;
use Admin\Model\Dao\UserDao;
use Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LoginController extends AbstractActionController {

    public function indexAction() {
        $request = $this->getRequest();
        $loginForm = new LoginAdminForm();

        if ($request->isPost()) {

            $postData = $request->getPost();

            $loginValidator = $loginForm->setInputFilter(new LoginAdminValidator());
            $loginValidator->setData($postData);

            if ($loginValidator->isValid()) {

                $loginData = $loginValidator->getData();

                $email = $loginData['email'];
                $password = $loginData['password'];

                $userTableGateway = $this->getService('UserAdminTableGateway');
                $userDao = new UserDao($userTableGateway);
                $userData = $userDao->getByEmail($email, array('user_id', 'salt'));
//                var_dump($userData);die;
                if ($userData) {

                    $passwordEncript = md5($password . $userData->salt);
//                    echo $passwordEncript;die;

                    $loginAdmin = $this->getService('Admin\Model\LoginAdmin');

                    try {

                        $loginAdmin->login($email, $passwordEncript);
                        $this->flashMessenger()->addMessage('Login Correcto!', 'success');

                        return $this->redirect()->toRoute('admin');
                    } catch (Exception $e) {
                        //$this->layout()->mensaje = $e->getMessage();
                        $this->flashMessenger()->addMessage($e->getMessage(), 'error');
                        return $this->forward()->dispatch('Admin\Controller\Index', array('action' => 'index'));
                    }
                } else {
                    $this->flashMessenger()->addMessage('Login incorrecto verifique sus datos!', 'error');
                    return $this->forward()->dispatch('Admin\Controller\Login', array(
                                'action' => 'index',
                                'loginForm' => $loginForm
                    ));
                }
            } else {
                $messages = $loginForm->getMessages();
                print_r($messages);die;
                $this->flashMessenger()->addMessage('Login incorrecto verifique sus datos!', 'error');
                return $this->forward()->dispatch('Admin\Controller\Login', array(
                            'action' => 'index',
                            'loginForm' => $loginForm
                ));
            }
        }
        $view['loginForm'] = $loginForm;
        return new ViewModel($view);
    }

    public function logoutAction() {
        $LoginAdmin = $this->getService('Admin\Model\LoginAdmin');
        $LoginAdmin->logout();
        //seteamos el mesanje de registro exitoso
        $this->flashMessenger()->addMessage("Session Finalizada", 'success');

        return $this->forward()->dispatch('Admin\Controller\Login', array('action' => 'index'));
    }

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

}
