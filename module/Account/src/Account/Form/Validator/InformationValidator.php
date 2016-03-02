<?php

/**
 * Description of LoginValidator
 *
 * @author Pedro
 */

namespace Account\Form\Validator;

use Zend\InputFilter\InputFilter;

class InformationValidator extends InputFilter
{
    
    protected $opcionesAlnum = array(
        'allowWhiteSpace' => true,
     
    );
    protected $opcionesAlnum2 = array(
        'allowWhiteSpace' => false,
       
    );
    protected $opcionesAlpha2 = array(
        'allowWhiteSpace' => false,
      
    );
    protected $filterGeneric = array(
        array(
            'name' => 'StripTags'
        ),
        array(
            'name' => 'StringTrim'
        ),
        array(
            'name' => 'StringToLower',
            'options' => array(
                'encoding' => 'UTF-8'
                ),      
        )
    );
    
    public function __construct()
    {
        $this->add(array(
            'name' => 'customer_id',          
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'Int',
                )
            ),
            'filters' => $this->filterGeneric,
        ));
        
        $this->add(array(
            'name' => 'firstname',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Alnum',
                    'options' => $this->opcionesAlnum
                )
            ),
            'filters' => $this->filterGeneric,
        ));
        $this->add(array(
            'name' => 'lastname',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Alnum',
                    'options' => $this->opcionesAlnum
                )
            ),
            'filters' => $this->filterGeneric,
        ));
        $this->add(array(
            'name' => 'company',    
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'Alnum',
                    'options' => $this->opcionesAlnum
                )
            ),
            'filters' => $this->filterGeneric,
        ));

        
         $this->add(array(
            'name' => 'document_identity',          
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'Int',
                )
            ),
            'filters' => $this->filterGeneric,
        ));
         
         
         $this->add(array(
            'name' => 'number_phone',          
            'required' => false,
           
            'filters' => $this->filterGeneric,
        ));
         $this->add(array(
            'name' => 'postcode',          
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'Int',
                )
            ),
            'filters' => $this->filterGeneric,
        ));
        $this->add(array(
            'name' => 'address_1',
            'required' => true,           
            'filters' => $this->filterGeneric,
        ));
        
        $this->add(array(
            'name' => 'newsletter',
            'required' => false,           
           
        ));
  
    }

}
