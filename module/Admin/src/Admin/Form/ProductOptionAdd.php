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
class ProductOptionAdd extends Form {

    public function __construct($name = null) {
        parent::__construct($name);

        $this->setAttribute('action', 'admin/product/addOption');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
//        $this->setHydrator(new ClassMethodsHydrator(false));
//        $optionId = new Element\Hidden();
//        $optionId->setAttribute('class', 'form-control');
//        $optionId->setAttribute('id', 'option_id');
//
//        $this->add(array(
//            'type' => 'Zend\Form\Element\Collection',
//            'name' => 'option_id',
//            'options' => array(
//                'should_create_template' => false,
//                'target_element' => $optionId
//            )
//        ));

        $optionId = new Element\Select('option');
        $optionId->setAttribute('class', 'form-control');
        $optionId->setAttribute('id', 'option_id');
        $this->add($optionId);

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'option_id',
            'options' => array(
                'should_create_template' => false,
                'target_element' => $optionId
            )
        ));

        $pricePrefix = new Element\Select('price_prefix');
        $pricePrefix->setAttribute('class', 'form-control');
        $pricePrefix->setAttribute('id', 'price_prefix');
        $pricePrefix->setValueOptions(array(
            '1' => '+',
            '0' => '-',
        ));
        $this->add($pricePrefix);

        $price = new Element\Number();
        $price->setAttribute('class', 'form-control');
        $price->setAttribute('id', 'price');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'price',
            'options' => array(
                'should_create_template' => false,
                'target_element' => $price
            )
        ));

        $required = new Element\Checkbox();
        $required->setAttribute('class', 'form-control');
        $required->setCheckedValue("1");
        $required->setUncheckedValue("0");

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'required',
            'options' => array(
                'should_create_template' => false,
                'target_element' => $required
            )
        ));
    }

}
