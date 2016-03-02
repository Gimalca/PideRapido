<?php

namespace Franchise\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Description of RegisterForm
 *
 * @author Pedro
 */
class OperatorAdd extends Form
{

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setAttribute('action', 'franchise/operator/add');
        $this->setAttribute('method', 'post');


        $this->add(array(
            'name' => 'branch_id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'operator_id',
            ),
        ));
        
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type' => 'text',
                'id' => 'name',
                'class' => 'form-control input',
                'placeholder' => 'Nombre',
                'size' => 30,
                'required' => true,
                
            ),
        ));
        $this->add(array(
            'name' => 'last_name',
            'attributes' => array(
                'type' => 'text',
                'id' => 'lastname',
                'class' => 'form-control input',
                'placeholder' => 'Apellido',
                'size' => 30,
                'required' => true,
             
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'attributes' => array(                
                'id' => 'email',
                'class' => 'form-control input',
                'placeholder' => 'Correo Electrónico',
                'required' => true,
               
            ),
        ));
        $this->add(array(
            'name' => 'phone',            
            'attributes' => array(                
                'type' => 'number',
                'id' => 'phone',
                'class' => 'form-control input',
                'placeholder' => 'Telefono',
                'required' => true,
               
            ),
        ));
       
        $this->add(array(
            'type' => 'Zend\Form\Element\Password',
            'name' => 'password',
           'attributes' => array(              
                'id' => 'password',
                'class' => 'form-control ',
                'placeholder' => 'Ingrese su Contraseña',
                'required' => true,
               
        
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Password',
            'name' => 'confirm_password',
           'attributes' => array(              
                'id' => 'confirm_password',
                'class' => 'form-control ',
                'placeholder' => 'Re-ingresa la Contraseña',
                'required' => true,
               
        
            ),
        ));

//        // Crear y configurar el elemento confirmarPassword:
//      
//         $this->add(array(
//            'name' => 'csrf',
//            'type' => 'Zend\Form\Element\Csrf',
//        ));
    }

    //put your code here
}
