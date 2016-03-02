<?php

namespace Franchise\Controller;

use Franchise\Form\OperatorAdd;
use Franchise\Form\OperatorList;
use Franchise\Form\Validator\OperatorAddValidator;
use Franchise\Model\Dao\BranchDao;
use Franchise\Model\Entity\Operator;
use Zend\Mail\Message as MailMessage;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class OperatorController extends AbstractActionController {

    public function addAction() {
        $request = $this->getRequest();
        $operatorAddForm = new OperatorAdd();
        $auth = $this->getService('Franchise\Model\LoginFranchise');
        if ($auth->isLoggedIn()) {
            $this->_user = $auth->getIdentity();
            $branch_id = $this->_user->branch_id;
//            echo "verga";die;
            if ($request->isPost()) {
                $postData = array_merge_recursive(
                        $request->getPost()->toArray(), $request->getFiles()->toArray()
                );
//            var_dump($postData);die;
                $operatorAddForm->setInputFilter(New OperatorAddValidator);
                $operatorAddForm->setData($postData);

                if ($operatorAddForm->isValid()) {

                    $operatorData = $operatorAddForm->getData();
                    $prepareOperatorData = $this->prepareOperatorData($operatorData);
                    $prepareOperatorData['branch_id'] = $branch_id;
//                    var_dump($prepareOperatorData);die;
                    $operatorEntity = New Operator;
                    $operatorEntity->exchangeArray($prepareOperatorData);
                    //print_r($franquimovilEntity);die;

                    $operatorTableGateway = $this->getService('OperatorTableGateway');
                    $operatorDao = new BranchDao($operatorTableGateway);

                    $saved = $operatorDao->saveOperator($operatorEntity);
                    if ($saved) {
                        $sendMail = $this->sendMailRegisterConfirm($operatorEntity, $postData['password']);
                        //seteamos el mesanje de registro exitoso
                        $this->flashMessenger()->addMessage("Registro exitoso!!. Acabamos de enviarle un correo a $operatorEntity->email", 'success');

                        return $this->redirect()->toRoute('franchise', array(
                                    'controller' => 'index',
                                    'action' => 'index'
                        ));
                    } else {
                        $brachData->populateValues($dataform);
                        $this->message($saved);
                    }
                } else {
                    $messages = $operatorAddForm->getMessages();
                    print_r($messages);
                    print_r($operatorAddForm->getData());
                    $operatorAddForm->populateValues($operatorAddForm->getData());

                    return $this->forward()->dispatch('Franchise\Controller\index', array(
                                'action' => 'index',
                                'form' => $operatorAddForm
                    ));
                }
            }

            $view['form'] = $operatorAddForm;

            return new ViewModel($view);
        }
    }

    private function prepareOperatorData($operatorData) {

        $operatorData['salt'] = time();
        $operatorData['password'] = md5($operatorData['password'] . $operatorData['salt']);

        return $operatorData;
    }

    private function sendMailRegisterConfirm($operatorEntity, $password) {
       
        $render = $this->getService('ViewRenderer');       
        $content = $render->render('admin/operator/confirm-email',array(
                'operator' => $operatorEntity,
                'contraseña' => $password,
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
            $message->addTo( $operatorEntity->email)
                    ->addFrom('noreply@piderapido.com')
                    ->setSubject('Piderapido.com');
                    
            
     $sendMail =  $mailer->send($message);
           
             
    return $sendMail;  
    }

    public function listAction() {
        $auth = $this->getService('Franchise\Model\LoginFranchise');
        if ($auth->isLoggedIn()) {
            $request = $this->getRequest();
            $operatorListSelect = new OperatorList();
            $this->_user = $auth->getIdentity();
            $branch_id = $this->_user->branch_id;

            if ($request->isPost()) {
                $postData = array_merge_recursive(
                        $request->getPost()->toArray(), $request->getFiles()->toArray()
                );
                $operatorId = $postData['operator_id'];
//                var_dump($operatorId);die;

                $operatorList = $this->getOperatorSelect($branch_id);
                $operatorListSelect->get('operator_id')->setValueOptions($operatorList);

                $operatorTableGateway = $this->getService('OperatorTableGateway');
                $operatorDao = new BranchDao($operatorTableGateway);
                $operator = $operatorDao->getOperatorSearch($branch_id, $operatorId);
//                var_dump($operator);die;

                $view['operator'] = $operator;
                $view['operator_list'] = $operatorListSelect;
                return new ViewModel($view);
            } else {

                $operatorList = $this->getOperatorSelect($branch_id);
                $operatorListSelect->get('operator_id')->setValueOptions($operatorList);


                $operatorTableGateway = $this->getService('OperatorTableGateway');
                $operatorDao = new BranchDao($operatorTableGateway);
                $operator = $operatorDao->getOperator($branch_id);
                $view['operator'] = $operator;
                $view['operator_list'] = $operatorListSelect;
//            print_r($view['branch']);die;
                return new ViewModel($view);
            }
        }
    }

    private function getOperatorSelect($branch_id) {
        $operatorTableGateway = $this->getService('OperatorTableGateway');
        $operatorDao = new BranchDao($operatorTableGateway);
        $operator = $operatorDao->getOperator($branch_id);
        $result = array();
        foreach ($operator as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->operator_id] = $bankR->name;
        }
        return $result;
    }

    public function deleteAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id', 0);
        $operatorTableGateway = $this->getService('OperatorTableGateway');
        $operatorDao = new BranchDao($operatorTableGateway);
        $delete = $operatorDao->deleteOperator($id);
//        var_dump($delete);die;
        if ($delete) {
            return $this->redirect()->toRoute('franchise', array(
                        'controller' => 'operator',
                        'action' => 'list'
            ));
        } else {
            return $this->redirect()->toRoute('franchise', array(
                        'controller' => 'operator',
                        'action' => 'list'
            ));
        }
        return $response;
    }

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

}
