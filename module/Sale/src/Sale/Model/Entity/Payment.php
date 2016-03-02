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

namespace Sale\Model\Entity;

class Payment
{
    public $order_id;
    public $customer_id;
    public $invoice_number;
    public $cod;
    public $tdc;
    public $num_confirm;
    

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
            }else{
                $this->{$attr} = null;
            }
        }
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

}
