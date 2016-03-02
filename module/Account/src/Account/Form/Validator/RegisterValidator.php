<?php

/**
 * Description of LoginValidator
 *
 * @author Pedro
 */

namespace Account\Form\Validator;

use Zend\InputFilter\InputFilter;

class RegisterValidator extends InputFilter
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
    
    public function __construct($sm)
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
            'name' => 'email',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                )
            ),
            'validators' => array(
                array(
                    'name' => 'EmailAddress',
                    'options' => array(
                        
                    )
                ),
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                       
                    )
                ),
                array(
                    'name' => 'Zend\Validator\Db\NoRecordExists',
                    'options' => array(
                        'table' => 'pr_customer',
                        'field' => 'email',
                        'adapter' => $sm->get('Zend\Db\Adapter\Adapter'),
                         
                    ),
                ),
             
            )
        ));
       
        $this->add(array(
            'name' => 'password',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                ),
                
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                      
                    )
                ),
                 array(
                    'name' => 'stringLength',
                     'options' => array(
                         'min' => 6,
                       
                     )
                )
            )
        ));
     
       
    }

}
