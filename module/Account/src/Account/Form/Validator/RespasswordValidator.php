<?php

/**
 * Description of LoginValidator
 *
 * @author Pedro
 */

namespace Account\Form\Validator;

use Zend\InputFilter\InputFilter;

class RespasswordValidator extends InputFilter
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
    }

}
