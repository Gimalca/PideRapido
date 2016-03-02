<?php

/**
 * Description of LoginValidator
 *
 * @author Pedro
 */

namespace Account\Form\Validator;

use Zend\InputFilter\InputFilter;

class LoginValidator extends InputFilter
{
    
    public function __construct()
    {
      
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
