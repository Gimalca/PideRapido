<?php

namespace Admin\Model\Dao;

/**
 * Description of UserDao
 *
 * @author Pedro
 */
use Admin\Model\Entity\User;
use Zend\Db\TableGateway\TableGateway;

class UserDao {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getUserByToken($token) {
        $rowset = $this->tableGateway->select(array('token' => $token));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $token");
        }
        return $row;
    }

    public function getByEmail($email, $columns = null) {

//        echo $email;
        $sql = $this->tableGateway->getSql();

        $query = $sql->select()
                ->columns($columns)
                ->where(array('pr_user.email' => $email, 'type' => 1));
//        echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);

//        var_dump($resultSet);die;
        $row = $resultSet->current();
        return $row;
    }

    public function getFranchiseByEmail($email, $columns = null) {

//        echo $email;
        $sql = $this->tableGateway->getSql();

        $query = $sql->select()
                ->columns($columns)
                ->where(array('pr_user.email' => $email, 'type' => 2));
//        echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);

//        print_r($resultSet);die;
        $row = $resultSet->current();
        return $row;
    }

}
