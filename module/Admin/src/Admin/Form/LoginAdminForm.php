<?php

namespace Admin\Form;

use Zend\Form\Form;

/**
 * Description of Contacto
 *
 * @author @pgiacometto
 */
class LoginAdminForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setAttribute('action', 'admin/login');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'attributes' => array(
                'id' => 'email',
                'class' => 'form-control input',
                'placeholder' => 'Email',
                'required' => true,
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Password',
            'name' => 'password',
            'attributes' => array(
                'id' => 'password',
                'class' => 'form-control ',
                'placeholder' => 'enter password',
                'required' => true,
            ),
        ));
//
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'rememberme',
            'attributes' => array(
                'id' => 'password',
                'value' => 1,
                'checked' => 'checked'
            ),
        ));

        // Crear y configurar el elemento confirmarPassword:

        $this->add(array(
            'name' => 'csrf',
            'type' => 'Zend\Form\Element\Csrf',
        ));
    }

}
