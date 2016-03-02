<?php

namespace Admin\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Description of Contacto
 *
 * @author Pedro
 */
class FranchiseAdd extends Form {

    public function __construct($name = null) {
        parent::__construct($name);

        $this->setAttribute('action', 'admin/franchise/add');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');


        $this->add(array(
            'name' => 'franchise_id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'franchise_id',
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
            'name' => 'address',
            'attributes' => array(
                'type' => 'text',
                'id' => 'address',
                'class' => 'gui-input',
                'placeholder' => 'Dirección',
                'required' => true,
            ),
        ));
        $this->add(array(
            'name' => 'telephone',
            'attributes' => array(
                'type' => 'text',
                'id' => 'telephone',
                'class' => 'gui-input',
                'placeholder' => 'Telefono',
                'required' => true,
            ),
        ));
        $this->add(array(
            'name' => 'country',
            'type' => 'select',
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    'VEN' => 'Venezuela',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'category_id',
            'type' => 'select',
        ));
        $this->add(array(
            'name' => 'state',
            'attributes' => array(
                'type' => 'text',
                'id' => 'name',
                'class' => 'gui-input',
                'placeholder' => 'Estado',
                'required' => true,
            ),
        ));
        $this->add(array(
            'name' => 'city',
            'attributes' => array(
                'type' => 'text',
                'id' => 'name',
                'class' => 'gui-input',
                'placeholder' => 'Municipio',
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'logo',
            'attributes' => array(
                'type' => 'File',
                'id' => 'logo',
                'placeholder' => 'Logo',
            ),
        ));
    }

}
