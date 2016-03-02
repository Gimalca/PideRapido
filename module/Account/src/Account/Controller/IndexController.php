<?php

namespace Account\Controller;

use Sale\Model\Dao\CustomerDao;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    private $_user;

    public function indexAction()
    {
        $auth = $this->getService('Account/Model/LoginAccount');
                
        $this->_user = $auth->getIdentity();
        
  
        if ($this->_user->register_complete == 0) {

            $cutomerDao = new CustomerDao($this->getService('CustomerTableGateway'));
            $row = $cutomerDao->getById($this->_user->customer_id, array('register_complete'));
            
            if ($row->register_complete == 0) {
                $this->flashMessenger()->addMessage("Necesita actualizar sus datos!! ", 'warning');
                return $this->redirect()->toRoute('account', array(
                            'controller' => 'Information',
                            'action' => 'edit'
                ));
            }
            
        }
        $view ['user'] = $this->_user;

        return new ViewModel($view);
    }

    public function getService($serviceName)
    {
        $sm = $this->getServiceLocator();
                $service = $sm->get($serviceName);

                return $service;
    }


}

