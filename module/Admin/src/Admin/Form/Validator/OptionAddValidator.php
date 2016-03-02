<?php

/**
 * Description of LoginValidator
 *
 * @author Pedro
 */

namespace Admin\Form\Validator;


use Zend\InputFilter\InputFilter;

class OptionAddValidator extends InputFilter {

    protected $opcionesAlnum = array(
        'allowWhiteSpace' => true,
        'messages' => array(
            'notAlnum' => "El valor no es alfanumerico"
        )
    );
    protected $opcionesAlnum2 = array(
        'allowWhiteSpace' => false,
        'messages' => array(
            'notAlnum' => "Solo numeros, letras y sin espacio "
        )
    );
    protected $opcionesAlpha2 = array(
        'allowWhiteSpace' => false,
        'messages' => array(
            'notAlpha' => "Solo numeros, letras y sin espacio "
        )
    );
    protected $filterGeneric = array(
        array(
            'name' => 'StripTags'
        ),
        array(
            'name' => 'StringTrim'
        )
    );

    public function __construct() {
        

        $this->add(array(
            'name' => 'name',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                )
            )
        ));
        $this->add(array(
            'name' => 'type',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                )
            )
        ));
        $this->add(array(
            'name' => 'clasification',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                )
            )
        ));
    }

}
