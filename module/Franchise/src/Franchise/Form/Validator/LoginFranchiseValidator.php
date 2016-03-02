<?php

/**
 * Description of LoginValidator
 *
 * @author Pedro
 */

namespace Franchise\Form\Validator;

use Zend\InputFilter\InputFilter;

class LoginFranchiseValidator extends InputFilter
{
    
    public function __construct()
    {
      
        $this->add(array(
            'name' => 'user',
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
                    'name' => 'NotEmpty',
                    'options' => array(
                       
                    )
                ),
             
            )
        ));
        $this->add(array(
            'name' => 'email',
            'required' => false,
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
                    'name' => 'NotEmpty',
                    'options' => array(
                       
                    )
                ),
             
            )
        ));
        $this->add(array(
           'name' => 'rememberme',
            'required' => false,
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
                         'min' => 5,
                       
                     )
                )
            )
        ));
     
       
    }

}
