<?php

namespace Sale\Controller;

use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CheckoutController extends AbstractActionController {

    public function indexAction() {
        return new ViewModel();
    }

    public function cartDetailAction(){
        return new ViewModel();
    }

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

}
