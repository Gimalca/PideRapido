<?php

namespace Admin\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Form\Element\Collection;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

/**
 * Description of Contacto
 *
 * @author Pedro
 */
class ComboAdd extends Form {

    public function __construct($name = null) {
        parent::__construct($name);

        $this->setAttribute('action', 'admin/combo/add');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
//        $this->setHydrator(new ClassMethodsHydrator(false));


        $this->add(array(
            'name' => 'combo_id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'combo_id',
            ),
        ));
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type' => 'text',
                'id' => 'name',
                'class' => 'form-control',
                'placeholder' => 'Nombre del Combo',
                'required' => true,
            ),
        ));
        $this->add(array(
            'name' => 'description',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Desripction',
            ),
        ));
        $this->add(array(
            'name' => 'franchise_id',
            'type' => 'select',
            'attributes' => array(
                'class' => 'form-control',
            ),
            'options' => array(
                'disable_inarray_validator' => true,
            ),
        ));

        $inputProduct = new Element\Select('selectpro');
        $inputProduct->setAttribute('class', 'form-control gui-input');
        $this->add($inputProduct);

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'product_id',
            'options' => array(
                'should_create_template' => false,
                'target_element' => $inputProduct
            )
        ));

        $this->add(array(
            'name' => 'image',
            'attributes' => array(
                'type' => 'File',
                'id' => 'image',
                'placeholder' => 'Foto',
            ),
        ));
    }

}
