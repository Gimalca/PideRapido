<?php

namespace Sale\Model\Dao;

/**
 * Description of CustomerDao
 *
 * @author Pedro
 */
use Account\Model\Entity\Address;
use Sale\Model\Entity\Customer;
use Zend\Db\TableGateway\TableGateway;

class CustomerDao {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getAll() {
        $query = $this->tableGateway->getSql()->select();

        $query->order("customer_id DESC");
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet);die;

        return $resultSet;
    }

    public function getAllSimple() {
        $query = $this->tableGateway->getSql()->select();

        $query->order("customer_id ASC");
        $query->columns(array('customer_id','firstname','lastname','email','telephone','document_identity'));
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet);die;

        return $resultSet;
    }

    public function getLatest() {
        $query = $this->tableGateway->getSql()->select();

        $query->order("customer_id DESC");
        //echo $query->getSqlString();die;
        $query->limit(10);

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet);die;

        return $resultSet;
    }

    public function getById($id, $columns = null) {

        $id = (int) $id;
        $sql = $this->tableGateway->getSql();

        $query = $sql->select()
                ->where(array('pr_customer.customer_id' => $id));

        if ($columns) {
            $query->columns($columns);
        }

        $resultSet = $this->tableGateway->selectWith($query);
        $row = $resultSet->current();
        return $row;
    }

    public function getInfoCustomer($customer_id, $register_complete, $columns = null) {

        $query = $this->tableGateway->getSql()->select();
        if ($columns) {
            $query->columns($columns);
        }
        if ($register_complete == 1) {
            $query->join(array('a' => 'pr_address'), 
                    'pr_customer.address_default = a.address_id', 
                    array('address_id', 
                        'firstname_address' => 'firstname', 
                        'lastname_address' => 'lastname', 
                        'company', 
                        'telephone', 
                        'address_1', 
                        'postcode', 
                        'state_id', 
                        'municipality_id',
                        'telephone',
                        'city_id')
            );
        }
        $query->where(array(
            'pr_customer.customer_id' => $customer_id,
        ));

        //echo $query->getSqlString();die;
        $result = $this->tableGateway->selectWith($query);

        //print_r($result); die;


        return $result->current();
    }

    public function savedCustomer(Customer $customer) {

        $idData = $customer->customer_id;
        //$id = (int) $idData;

        $dataCustomer = $customer->getArrayCopy();
//        array_filter($customer);die;
//        var_dump($dataCustomer);die;
        if ($idData == 0) {
            //print_r($dataBranch);die;
            return $this->tableGateway->insert($dataCustomer);
        } else {
            if ($this->getById($idData)) {

                $dataCustomerUpdate = array_filter($dataCustomer);
                return $this->tableGateway->update($dataCustomerUpdate, array('customer_id' => $idData));
            } else {
                throw new \Exception('Id no encontrado');
            }
        }
    }

    public function saveInfoCustomer(Customer $customer, Address $address) {
        $idData = $customer->customer_id;
        $id = (int) $idData;

        //print_r($customer);die;
        $address->address_id = $customer->address_default;
        //print_r($address);
        $saveAdress = $this->saveAddress($address);
        //print_r($saveAdress);die;
        $customer->address_default = $saveAdress;
        //print_r($customer);die;
        $saveCustomer = $this->savedCustomer($customer);

        if ($saveCustomer && $saveAdress) {

            $tableChA = $this->getTable('pr_customer_has_address');

            $row = $tableChA->select(array(
                'customer_id' => $id,
                'address_id' => $saveAdress
            ));

            if ($row->count() == 0) {
                $insert = $tableChA->insert(array(
                    'customer_id' => $id,
                    'address_id' => $saveAdress
                ));

                return $insert;
            }

            return $row;
        } else {

            return false;
        }
    }

    public function saveAddress(Address $address) {
        $table = $this->getTable('pr_address');

        $idData = $address->address_id;
        $id = (int) $idData;


        $data = $address->getArrayCopy();

        if ($id == 0) {
            //print_r($dataBranch);die;

            $insert = $table->insert($data);

            return $table->getLastInsertValue();
        } else {
            //print_r($dataCustomer);die;
            $dataAddressUpdate = array_filter($data);
            //print_r($dataAddressUpdate);die;
            $table->update($dataAddressUpdate, array('address_id' => $id));
            return $id;
        }
    }

    public function getTable($table) {
        $adapter = $this->tableGateway->getAdapter();
        $table = new TableGateway($table, $adapter);

        return $table;
    }

}
