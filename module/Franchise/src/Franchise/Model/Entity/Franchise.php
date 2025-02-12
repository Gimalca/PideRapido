<?php

namespace Franchise\Model\Entity;

class Franchise {

    public $franchise_id;
    public $category_id;
    public $name;
    public $rif;
    public $telephone;
    public $address;
    public $country;
    public $state;
    public $city;
    public $status;
    public $logo;
    public $banner;
    public $date_added;

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