<?php

namespace Account\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Description of RegisterForm
 *
 * @author Pedro
 */
class RegisterForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setAttribute('action', '/register/add');
        $this->setAttribute('method', 'post');


        $this->add(array(
            'name' => 'customer_id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'customer_id',
            ),
        ));
        
       $inputProductQuantity =  new Element\Text();
       $inputProductQuantity->setAttribute('class', 'form-control gui-input');
       
       $this->add(array(
           'type' => 'Zend\Form\Element\Collection',
            'name' => 'productQuantity',
           'options' => array(
               
               'should_create_template' => false,
               'target_element' =>  $inputProductQuantity
           )
          
        ));


        $this->add(array(
            'name' => 'firstname',
            'attributes' => array(
                'type' => 'text',
                'id' => 'firstname',
                'class' => 'form-control input',
                'placeholder' => 'Nombre',
                'size' => 30,
                'required' => true,
                
            ),
        ));
        $this->add(array(
            'name' => 'lastname',
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
                
                'id' => 'firstname',
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
            $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'terms',
            'attributes' => array(
                'id' => 'terms',               
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

    //put your code here
}
