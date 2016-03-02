<?php

namespace Franchise\Model\Entity;

class Branch {

    public $branch_id;
    public $contact;
    public $name;
    public $rif;
    public $phone;
    public $address;
    public $address_tax;
    public $state_id;
    public $country_id;
    public $municipality_id;
    public $city_id;
    public $opening_time;
    public $franchise_id;
    public $logo;
    public $status;
    public $banner;
    public $name_franchise;

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
    
    public function getArrayCopy() {
        return get_object_vars($this);
    }
    
    
    
}