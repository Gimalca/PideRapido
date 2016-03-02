<?php

namespace Catalog\Model\Entity;

class Product {

    public  $product_id; 
    public  $franchise_id; 
    public  $category_id; 
    public  $name; 
    public  $description; 
    public  $sku; 
    public  $quantity; 
    public  $image; 
    public  $price; 
    public  $subtract; 
    public  $type; 
    public  $status; 
    public  $date_added; 
    public  $date_modified;

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
