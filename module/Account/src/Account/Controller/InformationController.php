<?php

namespace Account\Controller;

use Account\Form\InformationForm;
use Account\Form\Validator\InformationValidator;
use Account\Model\Entity\Address;
use Api\Model\Dao\LocationDao;
use Sale\Model\Dao\CustomerDao;
use Sale\Model\Entity\Customer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class InformationController extends AbstractActionController
{
    private $_user;
    private $locationTable;



    public function indexAction()
    {
        return new ViewModel();
    }

    public function editAction()
    {
        $login = $this->getService('Account\Model\LoginAccount');
        $this->_user = $login->getIdentity();
        
        $infoForm = new InformationForm();
        $request = $this->getRequest();

         if ($request->isPost()) {
            //recibimos los datos del form
            $postData = $request->getPost();
            
            $infoValidator = $infoForm->setInputFilter(new InformationValidator);
            $infoValidator->setData($postData);
            
            if($infoForm->isValid()){
                
                //print_r($infoForm->getData());
                $customerData = $this->prepareDataCustomer($infoForm->getData());
                //print_r($customerData);
                $customerEntity = new Customer($customerData);
                //print_r($customerEntity);
                $addresEntity = new Address($customerData);
                //print_r($addresEntity);
                $customerTableGateway = $this->getService('CustomerTableGateway');
                $customerDao = new CustomerDao($customerTableGateway);
                $saved = $customerDao->saveInfoCustomer($customerEntity, $addresEntity);
                
                 if ($saved) { //si se guardo la fila en la BD continuamos
                    $this->_user->register_complete = 1;
                    //seteamos el mesanje de registro exitoso
                    $this->flashMessenger()->addMessage("Sus datos han sido actualizados!! ", 'success');
                     return $this->redirect()->toRoute('account', array(
                                'controller' => 'Index',
                                'action' => 'index'
                    ));
                    //$this->flashMessenger()->addMessage("Bienvenido $customerEntity->firstname $customerEntity->lastname !!. Acabamos de enviarle un correo de confirmacion ", 'success');
                } else {
                    $this->flashMessenger()->addMessage("Disculpe no pudimos procesar su registro ", "error");
                    // throw new \Exception("Not Save Row");
                }
                
                        
            }else{
                $messages = $infoForm->getMessages();
                //print_r($messages);die;
                $this->flashMessenger()->addMessage($messages, 'error');
            }
         }

        $customerTableGateway = $this->getService('CustomerHydratingTableGateway');
        $customeDao = new CustomerDao($customerTableGateway);
        
        $customerColums = array('firstname', 
            'lastname', 
            'email',
            'birthday',
            'telephone',          
            'document_identity',
            'gender',
            'newsletter',
            'register_complete',
            'address_default',
            
            );
        
        $customerRow = $customeDao->getInfoCustomer($this->_user->customer_id, $this->_user->register_complete, $customerColums);
        //var_dump($customerRow);die;
//        $defaultLocation = array('Country' => '1', 
//            'State' => '1', 
//            'Municipality' => '1', 
//            'City' => '1');
        
//       $locationDao = $this->getLocationDao();
//       $locationData = $locationDao->getCountrySelect();
       
         
        $country = $this->getCountrySelect();
        $infoForm->get('country_id')->setValueOptions($country);
        $infoForm->get('country_id')->setValue(1);
        
        
        $state = $this->getStateSelect();

        $infoForm->get('state_id')->setValueOptions($state);
              
        $municipality = $this->getMunicipalitySelect();
        $infoForm->get('municipality_id')->setValueOptions($municipality);

        $city = $this->getCitySelect();
        $infoForm->get('city_id')->setValueOptions($city);
        
        if($this->_user->register_complete == 1){
            $infoForm->get('state_id')->setValue($customerRow->state_id);
            $infoForm->get('municipality_id')->setValue($customerRow->municipality_id);
            $infoForm->get('city_id')->setValue($customerRow->city_id);
            
           
           
        }

        
        if ($this->_user->register_complete == 0) {
            $data = $customerRow->getArrayCopy();
            //print_r($data);die;
            $infoForm->setData($data);
            
        } else {
            
            $customerData = $this->prepareDataForm($customerRow);
            $infoForm->setData($customerData);
        }
        
        $infoForm->get('email')->setAttribute('readonly', 'readonly');
        $view['infoForm'] = $infoForm;
        
        //print_r($infoForm);die;
        return new ViewModel($view);
    }
    
    private function prepareDataForm(Customer $customer = null)
    {
        $customerData = array_filter($customer->getArrayCopy());

        $ciSplit = preg_split('/([J,V])/', $customerData['document_identity'],null,PREG_SPLIT_DELIM_CAPTURE);
        $customerData['document_identity'] = $ciSplit[2];
        $customerData['type_document_identity'] = $ciSplit[1];
        
        $birthdaySplit = preg_split('/([-])/', $customerData['birthday']);
        $customerData['year'] = $birthdaySplit[0];
        $customerData['month'] = $birthdaySplit[1];
        $customerData['day'] = $birthdaySplit[2];
        
        //$telephoneSplit = preg_split('/([-])/', $customerData['telephone']);
        $customerData['code_phone'] = substr($customerData['telephone'],0,4);
        $customerData['number_phone'] = substr($customerData['telephone'],4,20);
        
        //print_r($customerData);die;
        
        return $customerData;
    }
    
    private function prepareDataCustomer($customerData)
    {
        $customerData['customer_id'] = $this->_user->customer_id;
        $birthday = $customerData['year'].'-'.$customerData['month'].'-'.$customerData['day'];
        $customerData['birthday'] = $birthday;
        
        $identityDocument = $customerData['type_document_identity'].$customerData['document_identity'];
        $customerData['document_identity'] = $identityDocument;
        
        $telephone = $customerData['code_phone'].$customerData['number_phone'];
        $customerData['telephone'] = $telephone;
        $customerData['register_complete'] = 1;
        $customerData['email'] = $this->_user->email;   
        
        $customerData['date_modified'] = date("Y-m-d H:i:s");
        
        return $customerData;
    }
    
//    public function getLocationDao()
//    {
//        if (! $this->LocationDao) {
//            $sm = $this->getServiceLocator();
//            $this->LocationDao = $sm->get('Api\Model\Dao\LocationDao');
//        }
//        return $this->locationTable;
//    }
    
    private function getCountrySelect() 
    {
        $cityDao = $this->getService('CountryDao');
        $results = $cityDao->getAllCountry();

        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->country_id] = $bankR->name;
        }

        return $result;
    }
    
    private function getStateSelect()
    {
        $stateDao = $this->getService('StateDao');
        $results = $stateDao->getAllState();
        //var_dump($country_id);die;
        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->state_id] = $bankR->name;
        }
//        var_dump($id);die;
        return $result;
        
    }
    
     public function getMunicipalitySelect()
    {
       $municipalityDao = $this->getService('MunicipalityDao');
       $results = $municipalityDao->getAllMunicipality();
       
       $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->municipality_id] = $bankR->name;
        }
        //var_dump($result);die;
        return $result;
    }
    
    private function getCitySelect() {
        $cityDao = $this->getService('CityDao');
        $results = $cityDao->getAllCity();

        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->city_id] = $bankR->name;
        }

        return $result;
    }
    
    public function getService($serviceName)
    {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);

        return $service;
    }

}
