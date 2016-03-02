<?php

namespace Catalog\Model\Entity;

class Category {

    public  $category_id; 
    public  $name;

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