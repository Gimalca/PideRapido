<?php

/*
 * To change this license header choose License Headers in Project Properties.
 * To change this template file choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Customer
 *
 * @author Pedro
 */

namespace Account\Model\Entity;

class Address
{

    public $address_id; 
    public $firstname ; 
    public $lastname ; 
    public $company ; 
    public $telephone ; 
    public $address_1 ; 
    public $address_2 ; 
    public $postcode ; 
    public $state_id ; 
    public $municipality_id ; 
    public $city_id;

    //put your code here
    public function __construct(Array $data = array())
    {
        $this->exchangeArray($data);
    }

    public function exchangeArray($data)
    {

        $attributes = array_keys($this->getArrayCopy());

        foreach ($attributes as $attr) {
            if (isset($data[$attr])) {
                $this->{$attr} = $data[$attr];
            }
        }
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

}
