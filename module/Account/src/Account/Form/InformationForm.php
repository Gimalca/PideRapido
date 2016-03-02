<?php

namespace Account\Form;

use Zend\Form\Form;

/**
 * Description of RegisterForm
 *
 * @author Pedro
 */
class InformationForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct($name);
        
        $this->setAttribute('method', 'post');


        $this->add(array(
            'name' => 'customer_id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'customer_id',
            ),
        ));
        $this->add(array(
            'name' => 'register_complete',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'register_complete',
            ),
        ));
        $this->add(array(
            'name' => 'address_default',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'address_default',
            ),
        ));

        $this->add(array(
            'name' => 'firstname',
            'attributes' => array(
                'type' => 'text',
                'id' => 'firstname',
                'class' => 'form-control input',
                'placeholder' => 'Nombre',
                'size' => 30,
                'required' => true,
                
            ),
        ));
        $this->add(array(
            'name' => 'lastname',
            'attributes' => array(
                'type' => 'text',
                'id' => 'lastname',
                'class' => 'form-control input',
                'placeholder' => 'Apellido',
                'size' => 30,
                'required' => true,
             
            ),
        ));
        $this->add(array(
            'name' => 'company', 
            'type' => 'text',
            'attributes' => array(            
                'id' => 'company',
                'class' => 'form-control input',
                'placeholder' => 'Compañia',            
            ),
        ));
        
        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'attributes' => array(
                
                'id' => 'firstname',
                'class' => 'form-control',
                'placeholder' => 'Email',
                'required' => true,
               
            ),
        ));
        
        $this->add(array(
            'name' => 'type_document_identity',
            'type' => 'select',
            'attributes' => array(
                'value' => 'V',
                'id' => 'document_identity',
                'class' => 'form-control',
                'placeholder' => 'Rif / Cédula',       
            ),
            'options' => array(
                'disable_inarray_validator' => true,       
                'value_options' => array(
                    'V' => 'V',
                    'J' => 'J',
                )
            ),
        ));      
        
        $this->add(array(
            'name' => 'document_identity',
            'type' => 'text',
            'attributes' => array(
                'id' => 'document_identity',
                'class' => 'form-control input',
                'placeholder' => 'Cedula / Rif',
                'maxlength' => 10,
                'required' => true,           
            ),
        ));
        $this->add(array(
            'name' => 'code_phone',
            'type' => 'select',
            'attributes' => array(
                'value' => '0414',
                'id' => 'document_identity',
                'class' => 'form-control',
                'placeholder' => 'Codigo',       
            ),
            'options' => array(
                'disable_inarray_validator' => true,       
                'value_options' => array(
                    '0414' => '0414',
                    '0424' => '0424',
                    '0412' => '0412',
                    '0416' => '0416',
                    '0426' => '0426',
                    
                )
            ),
        ));      
        
        $this->add(array(
            'name' => 'number_phone',
            'type' => 'text',
            'attributes' => array(
                'id' => 'document_identity',
                'class' => 'form-control input',
                'placeholder' => 'numero',
                'maxlength' => 7,
                'required' => true,           
            ),
        ));
        
        $this->add(array(
            'name' => 'gender',
            'type' => 'select',
            'attributes' => array(
                'id' => 'gender',
                'class' => 'form-control',
                'placeholder' => 'Genero',
                       
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    'H' => 'Hombre',
                    'M' => 'Mujer',
                  
                )
            ),
        ));
        $days =  range(1,31);
        $this->add(array(
            'name' => 'day',
            'type' => 'select',
            'attributes' => array(   
                'id' => 'days',
                'class' => 'form-control',
                'placeholder' => 'Dia',
                
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value'        => '1',
                'value_options' => array_combine($days, $days)
            ),
        ));
        $this->add(array(
            'name' => 'month',
            'type' => 'select',
            'attributes' => array(      
                'id' => 'month',
                'class' => 'form-control',
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array(
                    '1' => 'Enero',
                    '2' => 'Febrero',
                    '3' => 'Marzo',
                    '4' => 'Abril',
                    '5' => 'Mayo',
                    '6' => 'Junio',
                    '7' => 'Julio',
                    '8' => 'Agosto',
                    '9' => 'Septiembre',
                    '10' => 'Octubre',
                    '11' => 'Nobiembre',
                    '12' => 'Diciembre',
                
                )
            ),
        ));
        
        $years =  range(1915,1997);
        $this->add(array(
            'name' => 'year',
            'type' => 'select',
            'attributes' => array(      
                'id' => 'year',
                'class' => 'form-control',
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => array_combine($years, $years)
            ),
        ));
        
//         $this->add(array(
//            'name' => 'state_id',
//            'type' => 'select',
//            'attributes' => array(      
//                'id' => 'state',
//                'class' => 'form-control',
//                 'required' => true,
//            ),
//            'options' => array(
//                'disable_inarray_validator' => true,
//                'value_options' => array(
//                    '1' => 'Miranda',
//                    '2' => 'Zulia',
//                )
//            ),
//        ));
         $this->add(array(
            'name' => 'country_id',
            'type' => 'select',
            'options' => array(
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'id' => 'country_id',
                'class' => 'form-control gui-input location-select',
                'data-location' => 'state',
                'data-select' => 'state',
                'required' => true,
                'style' => 'width: 100%',
                'multiple' => false,
            ),
        ));
        
        $this->add(array(
            'name' => 'state_id',
            'type' => 'select',
            'options' => array(
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'id' => 'state_id',
                'class' => 'form-control gui-input location-select state',
                'data-location' => 'municipality',
                'data-select' => 'municipality',
                'required' => true,
                'style' => 'width: 100%',
                'multiple' => false,
            ),
        ));
         
         $this->add(array(
            'name' => 'municipality_id',
            'type' => 'select',
            'options' => array(
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'id' => 'municipality_id',
                'class' => 'form-control gui-input municipality',
                'required' => true,
                'style' => 'width: 100%',
                'multiple' => false,
            ),
        ));        
         
//         $this->add(array(
//            'name' => 'municipality_id',
//            'type' => 'select',
//            'attributes' => array(      
//                'id' => 'municipality',
//                'class' => 'form-control',
//                'required' => true,  
//            ),
//            'options' => array(
//                'disable_inarray_validator' => true,
//                'value_options' => array(
//                    '1' => 'Chacao',
//                    '2' => 'Baruta',
//                    '3' => 'El Hatillo',
//                )
//            ),
//        ));
//         $this->add(array(
//            'name' => 'city_id',
//            'type' => 'select',
//            'attributes' => array(      
//                'id' => 'municipality',
//                'class' => 'form-control',
//                'required' => true,  
//            ),
//            'options' => array(
//                'disable_inarray_validator' => true,
//                'value_options' => array(
//                    '1' => 'Caracas',
//                    '2' => 'Valencia',
//                    '3' => 'Cumana',
//                )
//            ),
//        ));
         
          $this->add(array(
            'name' => 'city_id',
            'type' => 'select',
            'options' => array(
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'id' => 'city_id',
                'class' => 'form-control gui-input city',
                'style' => 'width: 100%',
                'multiple' => false,
            ),
        ));
         
         $this->add(array(
            'name' => 'address_1',
             'type' => 'textarea',
            'attributes' => array(
                'rows' => 5,
                'id' => 'address1',
                'class' => 'form-control gui-textarea',
                'placeholder' => 'Direccion',
                'required' => true,       
            ),
        )); 
         
        $this->add(array(
            'name' => 'postcode',
            'type' => 'text',
            'attributes' => array(
                'id' => 'postcode',
                'class' => 'form-control input',
                'placeholder' => 'Zona Postal',
                'maxlength' => 4,     
            ),
        ));
        
       
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'newsletter',
            'attributes' => array(
                'id' => 'newsletter',
                'value' => 1,
                'checked' => 'checked'
            ),
        ));
    }

    //put your code here
}
