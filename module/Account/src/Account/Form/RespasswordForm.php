<?php

namespace Account\Form;

use Account\Form\Validator\RespasswordValidator;
use Zend\Form\Form;

/**
 * Description of Contacto
 *
 * @author @pgiacometto
 */
class RespasswordForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setAttribute('action', 'account/login/forget');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'attributes' => array(
                'id' => 'firstname',
                'class' => 'form-control input',
                'placeholder' => 'Email',
                'required' => true,
            ),
        ));

     

        $this->setInputFilter(new RespasswordValidator());
    }

}
