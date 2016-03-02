<?php

namespace Admin\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

/**
 * Description of Contacto
 *
 * @author Pedro
 */
class OptionValueAdd extends OptionAdd {

    public function __construct($name = null) {
        parent::__construct($name);
        
        $this->setAttribute('action', 'admin/option/addOptionValue');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        
        $this->add(array(
            'name' => 'option_id',
            'type' => 'select',
            'options' => array(
                'disable_inarray_validator' => true,
            ),
        ));
        
        $sortOrder = new Element\Text();
        $sortOrder->setAttribute('class', 'form-control');
        $sortOrder->setAttribute('placeholder', 'Sort Order');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'sort_order',
            'options' => array(
                'should_create_template' => false,
                'target_element' => $sortOrder
            )
        ));
        
        $inputProduct = new Element\Text();
        $inputProduct->setAttribute('class', 'form-control');
        $inputProduct->setAttribute('placeholder', 'Nombre del Valor');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'name',
            'options' => array(
                'should_create_template' => false,
                'target_element' => $inputProduct
            )
        ));
        $inputProduct = new Element\Text();
        $inputProduct->setAttribute('class', 'form-control');
        $inputProduct->setAttribute('placeholder', 'Sort Order');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'sort_order',
            'options' => array(
                'should_create_template' => false,
                'target_element' => $inputProduct
            )
        ));
//        $inputProduct = new Element\File();
//        $inputProduct->setAttribute('class', 'form-control');
//
//        $this->add(array(
//            'type' => 'Zend\Form\Element\Collection',
//            'name' => 'image',
//            'options' => array(
//                'should_create_template' => false,
//                'target_element' => $inputProduct
//            )
//        ));
    }

}
