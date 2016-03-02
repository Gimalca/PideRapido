<?php

/**
 * Description of LoginValidator
 *
 * @author Pedro
 */

namespace Account\Form\Validator;

use Zend\InputFilter\InputFilter;
use Zend\Validator\StringLength;

class ForgetValidator extends InputFilter
{
    
    public function __construct()
    {
      
        $this->add(array(
            'name' => 'password',
            'require' => true,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 4,
                        'max' => 8,
                        'messages' => array(
                            StringLength::TOO_SHORT => "El campo debe tener tener al menos 4 caracteres",
                            StringLength::TOO_LONG => "El campo debe tener un máximo de 8 caracteres",
                        )
                    )
                ),
                array(
                    'name' => 'Alnum',
                    'options' => array(
                        'allowWhiteSpace' => true
                    )
                ),
            ),
        ));

        $this->add(array(
            'name' => 'password2',
            'require' => true,
            'validators' => array(
                array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'password'
                    )
                ),
            ),
        ));
       
    }

}
