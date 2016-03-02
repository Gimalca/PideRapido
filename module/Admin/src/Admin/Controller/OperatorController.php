<?php

namespace Admin\Controller;

use Admin\Form\OperatorAdd;
use Admin\Form\Validator\OperatorAddValidator;
use Franchise\Model\Dao\BranchDao;
use Franchise\Model\Entity\Operator;
use Zend\Mail\Message as MailMessage;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class OperatorController extends AbstractActionController {

    public function addAction() {
        $request = $this->getRequest();
        $operatorAddForm = new OperatorAdd();
        if ($request->isPost()) {
            $postData = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
//            var_dump($postData);die;
            $operatorAddForm->setInputFilter(New OperatorAddValidator);
//            var_dump($postData);die;
            $operatorAddForm->setData($postData);
//            var_dump($postData);die;
//            var_dump($operatorAddForm->isValid());die;
            if ($operatorAddForm->isValid()) {

                $operatorData = $operatorAddForm->getData();
                $prepareOperatorData = $this->prepareOperatorData($operatorData);
//                var_dump($prepareOperatorData);die;
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
                    return $this->redirect()->toRoute('admin', array(
                                'controller' => 'branch',
                                'action' => 'listFranchise'
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
                return $this->forward()->dispatch('Admin\Controller\Operator', array(
                            'action' => 'Add',
                            'form' => $operatorAddForm
                ));
            }
        } else {
            $branchId = (int) $this->params()->fromRoute('id', 0);

            $branchTableGateway = $this->getService('BranchTableGateway');
            $branchDao = new BranchDao($branchTableGateway);
            $branch = $branchDao->getId($branchId);            
            $operatorAddForm->bind($branch);
        }

        $view['form'] = $operatorAddForm;

        return new ViewModel($view);
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

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

}
