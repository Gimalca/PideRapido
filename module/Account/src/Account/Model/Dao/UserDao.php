<?php

namespace Account\Model\Dao;

/**
 * Description of CustomerDao
 *
 * @author Pedro
 */
use Zend\Db\TableGateway\TableGateway;
use Sale\Model\Entity\Customer;

class UserDao
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getUserByToken($token)
    {
        $rowset = $this->tableGateway->select(array('token' => $token));
        $row = $rowset->current();
//        if (!$row) {
//            throw new \Exception("Could not find row $token");
//        }
        return $row;
    }
    
     public function getByEmail($email, $columns = null) {
        
//        echo $email;
        $sql = $this->tableGateway->getSql();

        $query = $sql->select()
                    ->columns($columns)
                    ->where(array('pr_customer.email' => $email));
        //echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
        
//        print_r($resultSet);die;
        $row = $resultSet->current();
        return $row;
    }
    
    public function activateUser($usr_id)
    {
		$data['status'] = 1;
		$data['email_confirmed'] = 1;
		$this->tableGateway->update($data, array('customer_id' => (int)$usr_id));
    }
    

}
