<?php

namespace Franchise\Controller;

use Exception;
use Franchise\Form\LoginFranchiseForm;
use Franchise\Form\Validator\LoginFranchiseValidator;
use Franchise\Model\Dao\BranchDao;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LoginController extends AbstractActionController {

    public function indexAction() {
        $request = $this->getRequest();
        $loginForm = new LoginFranchiseForm();

        if ($request->isPost()) {

            $postData = $request->getPost();
//            var_dump($postData);die;
            $loginValidator = $loginForm->setInputFilter(new LoginFranchiseValidator());
            $loginValidator->setData($postData);
//            var_dump($loginValidator->isValid());die;
//            var_dump($loginValidator->getMessages());die;
            if ($loginValidator->isValid()) {

                $loginData = $loginValidator->getData();

                $user = $loginData['user'];
                $password = $loginData['password'];

                $userTableGateway = $this->getService('BranchContactTableGateway');
                $userDao = new BranchDao($userTableGateway);
                $userData = $userDao->getBranchContactByUser($user, array('branch_id', 'user', 'salt'));
//                var_dump($userData);die;
                if (!$userData) {
                    $messages = $loginForm->getMessages();
//                print_r($messages);die;
                    $this->flashMessenger()->addMessage('Login incorrecto verifique sus datos!', 'error');
                    return $this->forward()->dispatch('Franchise\Controller\Login', array(
                                'action' => 'index',
                                'loginForm' => $loginForm
                    ));
                } else {
//                print_r($userData);die;
                    $passwordEncript = md5($password . $userData->salt);
//                echo $passwordEncript;
//                var_dump($userData);die;
                    $loginFranchise = $this->getService('Franchise\Model\LoginFranchise');

                    try {

                        $loginFranchise->login($user, $passwordEncript);
                        $this->flashMessenger()->addMessage('Login Correcto!', 'success');
//                    echo "si";die;
                        return $this->redirect()->toRoute('franchise');
                    } catch (Exception $e) {
//                    var_dump( $e->getMessage());die;
                        $this->flashMessenger()->addMessage($e->getMessage(), 'error');
                        return $this->forward()->dispatch('Franchise\Controller\Index', array('action' => 'index'));
                    }
                }
            } else {
                $messages = $loginForm->getMessages();
//                print_r($messages);die;
                $this->flashMessenger()->addMessage('Login incorrecto verifique sus datos!', 'error');
                return $this->forward()->dispatch('Franchise\Controller\Login', array(
                            'action' => 'index',
                            'loginForm' => $loginForm
                ));
            }
        }
        $view['loginForm'] = $loginForm;
        return new ViewModel($view);
    }

    public function logoutAction() {
        $LoginFranchise = $this->getService('Franchise\Model\LoginFranchise');
        $LoginFranchise->logout();
        //seteamos el mesanje de registro exitoso
        $this->flashMessenger()->addMessage("Session Finalizada", 'success');

        return $this->forward()->dispatch('Franchise\Controller\Login', array('action' => 'index'));
    }

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

}
