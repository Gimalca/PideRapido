<?php

namespace Api\Model\Dao;

use Zend\Db\TableGateway\TableGateway;

/**
 * Description of ProductTable
 *
 * @author Pedro
 */



class LocationDao 
{

    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function getAllCountry()
    {
        $table = $this->getTable('pr_country');
       // print_r($table);die;
        $query = $this->tableGateway->getSql()->select();

        $query->order("country_id DESC");
        //echo $query->getSqlString();die;
    
        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet);die;
    
        return $resultSet;
         
    }
    public function getAllState()
    {
        $table = $this->getTable('pr_state');
       // print_r($table);die;
        $query = $table->getSql()->select();
        $query->where(array(
            'country_id' => 1
        ));

        //$query->order("state_id DESC");
        //echo $query->getSqlString();die;
    
        $resultSet = $table->selectWith($query);
        //var_dump($resultSet);die;
    
        return $resultSet;
         
    }
    public function getAllMunicipality($id = null)
    {
        $table = $this->getTable('pr_municipality');
       // print_r($table);die;
        $query = $table->getSql()->select();
        if(isset($id)){
            $query->where(array(
                'state_id' => $id
            ));
        }

        //$query->order("state_id DESC");
        //echo $query->getSqlString();die;
    
        $resultSet = $table->selectWith($query);
        //var_dump($resultSet);die;
    
        return $resultSet;
         
    }
    
    public function getAllCity($id = null)
    {
        $table = $this->getTable('pr_city');
       // print_r($table);die;
        $query = $table->getSql()->select();
        if(isset($id)){
            $query->where(array(
                'state_id' => $id
            ));
        }

        //$query->order("state_id DESC");
        //echo $query->getSqlString();die;
    
        $resultSet = $table->selectWith($query);
        //var_dump($resultSet);die;
    
        return $resultSet;
         
    }
    
    public function getTable($table)
    {
        $adapter = $this->tableGateway->getAdapter();
        $table = new TableGateway($table, $adapter);
    
        return $table;
    }
    
}
