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
class BranchAddProduct extends Form {

    public function __construct($name = null) {
        parent::__construct($name);

        $this->setAttribute('action', 'admin/branch/addProduct');
        $this->setAttribute('method', 'post');
//        $this->setHydrator(new ClassMethodsHydrator(false));
        
        $this->add(array(
            'name' => 'branch_id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'branch_id',
            ),
        ));
        
        $clasification = new Element\Select('product');
        $clasification->setAttribute('class', 'form-control');
        $clasification->setAttribute('required', false);
        $this->add($clasification);

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'product_id',
            'options' => array(
                'should_create_template' => false,
                'target_element' => $clasification
            )
        ));
        
    }

}
