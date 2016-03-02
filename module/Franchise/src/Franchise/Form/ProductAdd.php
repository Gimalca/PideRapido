<?php

namespace Franchise\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

/**
 * Description of Contacto
 *
 * @author Pedro
 */
class ProductAdd extends Form {

    public function __construct($name = null) {
        parent::__construct($name);

        $this->setAttribute('action', 'franchise/products/add');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
//        $this->setHydrator(new ClassMethodsHydrator(false));


        $this->add(array(
            'name' => 'product_id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'product_id',
            ),
        ));
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type' => 'text',
                'id' => 'name',
                'class' => 'form-control',
                'placeholder' => 'Nombre del Producto',
                'required' => true,
            ),
        ));
        $this->add(array(
            'name' => 'description',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Desripction',
            ),
        ));
        $this->add(array(
            'name' => 'franchise_id',
            'type' => 'select',
            'attributes' => array(
                'class' => 'form-control',
            ),
            'options' => array(
                'disable_inarray_validator' => true,
            ),
        ));
        $this->add(array(
            'name' => 'type',
            'type' => 'select',
            'attributes' => array(
                'class' => 'form-control',
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    '1' => 'Producto Adicional',
                    '2' => 'Combo',
                    '3' => 'Postres',
                )
            ),
        ));
        $this->add(array(
            'name' => 'category_id',
            'type' => 'select',
            'attributes' => array(
                'class' => 'form-control',
            ),
            'options' => array(
                'disable_inarray_validator' => true,
            ),
        ));
        $this->add(array(
            'name' => 'sku',
            'attributes' => array(
                'type' => 'text',
                'id' => 'sku',
                'class' => 'form-control gui-input',
                'placeholder' => 'Producto SKU',
                'required' => true, 
                      
            ),
        ));
        $this->add(array(
            'name' => 'price',
            'attributes' => array(
                'type' => 'number',
                'id' => 'price',
                'class' => 'form-control',
                'maxlength' => 255,
            ),
        ));
        $this->add(array(
            'name' => 'image',
            'attributes' => array(
                'type' => 'File',
                'id' => 'image',
                'placeholder' => 'Foto',
            ),
        ));
        
        $inputOption = new Element\Select('option');
        $inputOption->setAttribute('class', 'form-control gui-input');
          
        $this->add($inputOption);

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'option_id',
            'require' => false,
            'options' => array(
                'should_create_template' => false,
                'target_element' => $inputOption
            )
        ));
        
        $inputOptionRequire = new Element\Select('option_require');
        $inputOptionRequire->setAttribute('class', ' gui-input');
        $inputOptionRequire->setValue('0');
        $inputOptionRequire->setValueOptions(array(
                    '1' => 'Si',
                    '0' => 'No',
                ));
        $this->add($inputOptionRequire);
        
         $this->add(array(
            'type' => 'Zend\Form\Element\Collection',           
            'name' => 'required',
            'options' => array(
               'should_create_template' => false,
                'target_element' => $inputOptionRequire
               
            ),
        ));
    }

}
