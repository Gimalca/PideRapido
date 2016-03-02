<?php

namespace Catalog\Model\Entity;

class ProductOptionValue {

    public $product_id;
    public $product_option_id;
    public $option_id;
    public $option_value_id;
    public $price;
    public $price_prefix;
    public $product_option_value_id;
    public $status;
    

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

    public function tableGateway($prototype) {
        return $prototype('pr_product_option_value', $this);
    }

}
