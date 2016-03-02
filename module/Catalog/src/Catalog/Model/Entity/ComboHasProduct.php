<?php

namespace Catalog\Model\Entity;

class ComboHasProduct {

    public $combo_id;
    public $product_id;
    public $combo_has_branch_id;

    public function __construct(Array $data = array()) {
        $this->exchangeArray($data);
    }

    public function exchangeArray($data) {

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
