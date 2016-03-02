<?php

namespace Catalog\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Description of Contacto
 *
 * @author Pedro
 */
class FranchiseSearch extends Form {

    public function __construct($name = null) {
        parent::__construct($name);

           
        
           $this->add(array(
            'name' => 'category_id',
            'type' => 'select',
            'options' => array(
                'disable_inarray_validator' => true,
            ),
        ));
       
    }

}
