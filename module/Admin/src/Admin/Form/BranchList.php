<?php

namespace Admin\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Description of RegisterForm
 *
 * @author Pedro
 */
class BranchList extends Form
{

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setAttribute('action', 'admin/branch/list');
        $this->setAttribute('method', 'post');


        $this->add(array(
            'name' => 'branch_id',
            'type' => 'select',
            'options' => array(
                'disable_inarray_validator' => true,
            ),
        ));
        
    }

    //put your code here
}
