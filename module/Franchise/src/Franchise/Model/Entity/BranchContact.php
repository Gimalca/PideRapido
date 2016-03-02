<?php

namespace Franchise\Model\Entity;

class BranchContact {

    public $branch_contact_id;
    public $email;
    public $user;
    public $email_franchise;
    public $password;
    public $branch_id;
    public $salt;

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