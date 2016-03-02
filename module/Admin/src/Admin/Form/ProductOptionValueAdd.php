<?php

namespace Admin\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Description of Contacto
 *
 * @author Pedro
 */
class ProductOptionValueAdd extends Form {

    public function __construct($name = null) {
        parent::__construct($name);
        
        $this->setAttribute('action', 'admin/product/optionValueAdd');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        
        $this->add(array(
            'name' => 'product_option_id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'product_option_id',
            ),
        ));
        $this->add(array(
            'name' => 'product_id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'product_id',
            ),
        ));
        $this->add(array(
            'name' => 'option_id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'option_id',
            ),
        ));
        
        $optionId = new Element\Select('option_value');
        $optionId->setAttribute('class', 'form-control');
        $this->add($optionId);

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'option_value_id',
            'options' => array(
                'should_create_template' => false,
                'target_element' => $optionId
            )
        ));
        
        $sortOrder = new Element\Text();
        $sortOrder->setAttribute('class', 'form-control');
        $sortOrder->setAttribute('placeholder', '00,0 Bs.');
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'price',
            'options' => array(
                'should_create_template' => false,
                'target_element' => $sortOrder
            )
        ));
        
      
    }

}
