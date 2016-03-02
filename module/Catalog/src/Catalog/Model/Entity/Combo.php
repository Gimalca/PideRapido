<?php

namespace Catalog\Model\Entity;

class Combo {

    public $combo_id;
    public $image;
    public $status;
    public $name;
    public $date_added;
    public $date_modified;
    public $description;
    public $franchise_id;

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
