<?php

namespace Account\Form;

use Account\Form\Validator\ForgetValidator;
use Zend\Form\Form;

/**
 * Description of Contacto
 *
 * @author @pgiacometto
 */
class ForgetForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setAttribute('action', 'account/login/forget');
        $this->setAttribute('method', 'post');

       
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'id' => 'password',
                'class' => 'form-control ',
                'placeholder' => 'Tu Password',
                'required' => true,
                
            )
        ));
        $this->add(array(
            'name' => 'password2',
            'attributes' => array(
                'type' => 'password',
                'id' => 'password',
                'class' => 'form-control ',
                'placeholder' => 'Tu Password',
                'required' => true,
                
            )
        ));
        
        $this->add(array(
            'name' => 'token',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'token',
            ),
        ));
 

        // Crear y configurar el elemento confirmarPassword:

        $this->add(array(
            'name' => 'csrf',
            'type' => 'Zend\Form\Element\Csrf',
        ));
        
        $this->setInputFilter(new ForgetValidator());
    }

}
