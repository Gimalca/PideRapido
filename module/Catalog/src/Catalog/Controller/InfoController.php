<?php

namespace Catalog\Controller;

use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail\Message as MailMessage;
use Zend\Mime\Part as MimePart;  
use Zend\Mime\Message as MimeMessage;   

class InfoController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function comoFuncionaAction()
    {
        return new ViewModel();
    }

    public function contactoAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $comment = $request->getPost();
            
           
            if ($comment->email) {
                $dbAdapter = $this->getService('Zend\Db\Adapter\Adapter');
                $contactTable = new TableGateway('pr_contact', $dbAdapter, null, null);
  
                 $comment->date_added = date("Y-m-d H:i:s");
                
                $contactTable->insert(array(
                    'contact_id' => null,
                    'name' => $comment->name,
                    'email' => $comment->email,
                    'phone' => $comment->phone,
                    'subject' => $comment->subject,
                    'comment' => $comment->comment,
                    'date_added' => $comment->date_added
                ));
                
                $this->sendMailContact($comment);
                $this->flashMessenger()->addMessage("Gracias por contactarnos en breve le responderemos!", 'success');
            }      
        }
        return new ViewModel();
    }
    
    private function sendMailContact($comment)
    {
        $render = $this->getService('ViewRenderer');       
        $content = $render->render('catalog/info/email/contact-email',array(
                'comment' => $comment,
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
            $message->addTo(array('edrop14@gmail.com', 'info@piderapido.com') )
                    ->addFrom('noreply@piderapido.com')
                    ->setSubject('Piderapido.com, Contacto!');
                    
            
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
