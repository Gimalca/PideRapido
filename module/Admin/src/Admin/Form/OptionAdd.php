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
class OptionAdd extends Form {

    public function __construct($name = null) {
        parent::__construct($name);

        $this->setAttribute('action', 'admin/option/addOption');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
//        $this->setHydrator(new ClassMethodsHydrator(false));


        $optionId = new Element\Hidden();
        $optionId->setAttribute('class', 'form-control');
        $optionId->setAttribute('id', 'option_id');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'option_id',
            'options' => array(
                'should_create_template' => false,
                'target_element' => $optionId
            )
        ));
        $name = new Element\Text();
        $name->setAttribute('class', 'form-control');
        $name->setAttribute('id', 'name');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'name',
            'options' => array(
                'should_create_template' => false,
                'target_element' => $name
            )
        ));
        $type = new Element\Select('type');
        $type->setAttribute('class', 'form-control');
        $type->setAttribute('required', false);
        $this->add($type);

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'type_id',
            'options' => array(
                'should_create_template' => false,
                'target_element' => $type
            )
        ));
        $clasification = new Element\Select('clasification');
        $clasification->setAttribute('class', 'form-control');
        $clasification->setAttribute('required', false);
        $this->add($clasification);

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'clasification_id',
            'options' => array(
                'should_create_template' => false,
                'target_element' => $clasification
            )
        ));
        
        $sort_order = new Element\Text();
        $sort_order->setAttribute('class', 'form-control');
        $name->setAttribute('id', 'sort_order');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'sort_order',
            'options' => array(
                'should_create_template' => false,
                'target_element' => $sort_order
            )
        ));
    }

}
