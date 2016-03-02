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
class ClasificationAdd extends Form {

    public function __construct($name = null) {
        parent::__construct($name);

        $this->setAttribute('action', 'admin/option/addClasification');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
//        $this->setHydrator(new ClassMethodsHydrator(false));


        $optionId = new Element\Hidden();
        $optionId->setAttribute('class', 'form-control');
        $optionId->setAttribute('id', 'type_id');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'clasification_id',
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
    }

}
