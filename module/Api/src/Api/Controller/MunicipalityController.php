<?php

namespace Api\Controller;

use Api\Model\Dao\LocationDao;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class MunicipalityController extends AbstractRestfulController {

    public function getList() {
        # code...
    }

    public function get($id) {
        
         
        $response = $this->getResponse();
        //echo "get";die;
        $municipalityTableGAteway = $this->getService('MunicipalityTableGateway');
        $munDao = new LocationDao($municipalityTableGAteway);

        $id = $this->params()->fromRoute('id', 0);
        $mun = $munDao->getAllMunicipality($id);
        //var_dump($states);die;
        return new JsonModel($mun);
    }

    public function create($data) {
        echo "create";
        die;
        # code...
    }

    public function update($id, $data) {
        echo "update";
        die;
        # code...
    }

    public function delete($id) {
        echo "delete";
        die;
        # code...
    }

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

}
