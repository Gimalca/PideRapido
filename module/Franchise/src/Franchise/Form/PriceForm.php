<?php

namespace Franchise\Form;

use Zend\Form\Form;

/**
 * Description of Contacto
 *
 * @author @pgiacometto
 */
class PriceForm extends Form {

    public function __construct($name = null) {
        parent::__construct($name);

        $this->setAttribute('action', 'franchise/products/price');
        $this->setAttribute('method', 'post');
        
        $this->add(array(
            'name' => 'product_option_value_id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'product_id',
            ),
        ));

        $this->add(array(
            'name' => 'price',
            'type' => 'number',
            'attributes' => array(
                'id' => 'price',
                'class' => 'form-control input',
                'placeholder' => 'Precio',
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'csrf',
            'type' => 'Zend\Form\Element\Csrf',
        ));
    }

}
