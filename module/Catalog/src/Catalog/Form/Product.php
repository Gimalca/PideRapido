<?php namespace Catalog\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class Product extends Form {

    function __construct($name = null) {
        parent::__construct($name);

        $this->setAttribute('action', '');
        $this->setAttribute('id', 'formProductOptions');
        $this->setAttribute('method', 'POST');

        $this->add(array(
            'name' => 'csrf',
            'type' => 'Zend\Form\Element\Csrf',
        ));

        $this->add(array(
            'name' => 'product_has_branch_id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'product_has_branch_id',
            ),
        ));
    }

    public function addSelectBox($args, $values) {
        $this->add(array(
            'name' => $args['name'],
            'attributes' => [
                'class' => 'icr-select-box'
            ],
            'type' => 'Zend\Form\Element\Select',
            'required' => $args['required'] ? true : false,
            'multiple' => 'true',
            'options' => array(
                'label' => $args['label'],
                'disable_inarray_validator' => true,
                'empty_option' => 'Seleccione un '. $args['placeholder'],
                'value_options' => $values,
            ),
        ));
    }

    public function addCheckBox($args, $values) {
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => $args['name'],
            'attributes' => [
                'class' => 'icr-input'
            ],
            'options' => array(
                'label' => $args['label'],
                'use_hidden_element' => true,
                'checked_value'      => 1,
                'unchecked_value'    => 0
            )
        ));
    }
}
