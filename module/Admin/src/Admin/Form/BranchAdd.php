<?php

namespace Admin\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

/**
 * Description of Contacto
 *
 * @author Pedro
 */
class BranchAdd extends Form {

    public function __construct($name = null) {
        parent::__construct($name);

        $this->setAttribute('action', 'admin/branch/add');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
//        $this->setHydrator(new ClassMethodsHydrator(false));


        $this->add(array(
            'name' => 'branch_id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'branch_id',
            ),
        ));
        $this->add(array(
            'name' => 'branch_contact_id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'branch_contact_id',
            ),
        ));

        $this->add(array(
            'name' => 'contact',
            'attributes' => array(
                'type' => 'text',
                'id' => 'contact',
                'class' => 'gui-input',
                'placeholder' => 'Contacto',
                'required' => true,
            ),
        ));
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type' => 'text',
                'id' => 'name',
                'class' => 'gui-input',
                'placeholder' => 'Nombre',
                'required' => true,
            ),
        ));
        $this->add(array(
            'name' => 'rif',
            'attributes' => array(
                'type' => 'text',
                'id' => 'rif',
                'class' => 'gui-input',
                'placeholder' => 'Rif',
                'maxlength' => '9',
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'franchise_id',
            'type' => 'select',
            'options' => array(
                'disable_inarray_validator' => true,
            ),
        ));

        $this->add(array(
            'name' => 'address',
            'attributes' => array(
                'type' => 'text',
                'id' => 'address',
                'class' => 'gui-input',
                'placeholder' => 'Dirección Local',
                'required' => true,
                'maxlength' => 255,
            ),
        ));
        $this->add(array(
            'name' => 'address_2',
            'attributes' => array(
                'type' => 'text',
                'id' => 'address_2',
                'class' => 'gui-input',
                'maxlength' => 255,
            ),
        ));
        $this->add(array(
            'name' => 'address_tax',
            'attributes' => array(
                'type' => 'text',
                'id' => 'address_tax',
                'class' => 'gui-input',
                'placeholder' => 'Direccion Fiscal',
                'required' => true,
                'maxlength' => 255,
            ),
        ));
        $this->add(array(
            'name' => 'phone',
            'attributes' => array(
                'type' => 'text',
                'id' => 'phone',
                'class' => 'gui-input',
                'placeholder' => 'Numero Telefonico Movil',
                'required' => true,
            ),
        ));
        $this->add(array(
            'name' => 'country_id',
            'type' => 'select',
            'options' => array(
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'id' => 'country_id',
                'class' => 'form-control gui-input location-select',
                'data-location' => 'state',
                'data-select' => 'state',
                'required' => true,
                'style' => 'width: 100%',
                'multiple' => FALSE,
            ),
        ));
        $this->add(array(
            'name' => 'state_id',
            'type' => 'select',
            'options' => array(
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'id' => 'state_id',
                'class' => 'form-control gui-input location-select state',
                'data-location' => 'municipality',
                'data-select' => 'municipality',
                'required' => true,
                'style' => 'width: 100%',
                'multiple' => FALSE,
            ),
        ));
        $this->add(array(
            'name' => 'municipality_id',
            'type' => 'select',
            'options' => array(
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'id' => 'municipality_id',
                'class' => 'form-control gui-input municipality',
                'required' => true,
                'style' => 'width: 100%',
                'multiple' => FALSE,
            ),
        ));
        $this->add(array(
            'name' => 'city_id',
            'type' => 'select',
            'options' => array(
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'id' => 'city_id',
                'class' => 'form-control gui-input city',
                'style' => 'width: 100%',
                'multiple' => FALSE,
            ),
        ));
        
//        
//        $this->add(array(
//            'name' => 'town_id',
//            'type' => 'select',
//            'options' => array(
//                'disable_inarray_validator' => true,
//                'value_options' => array(
//                    '1' => 'Caracas',
//                ),
//            ),
//        ));
//        $this->add(array(
//            'name' => 'parish_id',
//            'type' => 'select',
//            'options' => array(
//                'disable_inarray_validator' => true,
//                'value_options' => array(
//                    '1' => 'Caracas',
//                ),
//            ),
//        ));
//        $this->add(array(
//            'name' => 'city_id',
//            'type' => 'select',
//            'options' => array(
//                'disable_inarray_validator' => true,
//                'value_options' => array(
//                    '1' => 'Caracas',
//                ),
//            ),
//        ));
        $this->add(array(
            'name' => 'opening_time',
            'attributes' => array(
                'type' => 'opening_time',
                'id' => 'hour',
                'class' => 'gui-input',
                'placeholder' => 'Hora de apertura',
                'required' => true,
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type' => 'email',
                'id' => 'hour',
                'class' => 'gui-input',
                'placeholder' => 'Email de Administrador',
                'required' => true,
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
            'name' => 'user',
            'attributes' => array(
                'type' => 'user',
                'id' => 'hour',
                'class' => 'gui-input',
                'placeholder' => 'Usuario Coorporativo',
                'required' => true,
            ),
        ));
        $this->add(array(
            'name' => 'email_franchise',
            'attributes' => array(
                'type' => 'email',
                'id' => 'hour',
                'class' => 'gui-input',
                'placeholder' => 'Email de Franquicia',
                'required' => true,
            ),
        ));        
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'id' => 'hour',
                'class' => 'gui-input',
                'placeholder' => 'Contraseña',
                'required' => true,
            ),
        ));
        $this->add(array(
            'name' => 'password_confirm',
            'attributes' => array(
                'type' => 'password',
                'id' => 'password_confirm',
                'class' => 'gui-input',
                'placeholder' => 'Confirme la Contraseña',
                'required' => true,
            ),
        ));
        
//        $this->add(array(
//            'name' => 'device_code',
//            'attributes' => array(
//                'type' => 'device_code',
//                'id' => 'hour',
//                'class' => 'gui-input',
//                'placeholder' => 'Clave Franquimovil',
//                'required' => true,
//            ),
//        ));
//        $this->add(array(
//            'name' => 'device_code_confirm',
//            'attributes' => array(
//                'type' => 'device_code_confirm',
//                'id' => 'hour',
//                'class' => 'gui-input',
//                'placeholder' => 'Confirmar la Clave Franquimovil',
//                'required' => true,
//            ),
//        ));
        $this->add(array(
            'name' => 'logo',
            'attributes' => array(
                'type' => 'File',
                'id' => 'logo',
                'placeholder' => 'Logo',
            ),
        ));
        $this->add(array(
            'name' => 'banner',
            'attributes' => array(
                'type' => 'File',
                'id' => 'banner',
                'placeholder' => 'Banner',
            ),
        ));
    }

}
