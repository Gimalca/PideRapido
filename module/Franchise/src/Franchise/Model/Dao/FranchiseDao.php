<?php

namespace Franchise\Model\Dao;

/**
 * Description of ProductTable
 *
 * @author Pedro
 */

use Exception;
use Franchise\Model\Entity\Franchise;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;

class FranchiseDao {
    
     private $query;

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
   
    public function getAll() {
        $this->query = $this->tableGateway->getSql()->select();

        $this->query->order("franchise_id DESC");
       
//        echo $query->getSqlString();die;

       return $this;
    }
    
    public function where($where){
        
      $this->query->where($where);
      
      return $this;
    }
    
    public function like($column, $word){
        
        $where = new Where();
        $like = $where->like($column, '%' . $word . '%');
        
        return $this->where($like);
    }

            public function fetchAll()
    {
        $resultSet = $this->tableGateway->selectWith($this->query);
        //var_dump($resultSet);die;

        return $resultSet->buffer();
        
    }
    public function getLogo($id) {

        $query = $this->tableGateway->getSql()->select();

        $query->columns(array('logo'));
        $query->where(array(
            'franchise_id' => $id,
                // 'status' => 1,
        ));
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet->current();
    }
    public function getBanner($id) {

        $query = $this->tableGateway->getSql()->select();

        $query->columns(array('banner'));
        $query->where(array(
            'franchise_id' => $id,
                // 'status' => 1,
        ));
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet->current();
    }
    public function getLast() {
        $query = $this->tableGateway->getSql()->select();

        $query->order("franchise_id DESC");
        $query->limit(6);
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet);die;

        return $resultSet;
    }    

    public function getAllCategory() {
        $table = $this->getTable('pr_franchise_category');
        $query = $table->getSql()->select();

        $query->order("category_id DESC");
        //echo $query->getSqlString();die;

        $resultSet = $table->selectWith($query);
        //var_dump($resultSet);die;

        return $resultSet;
    }

    public function getFranchise($id) {
        $id = (int) $id;
        // $rowset = $this->tableGateway->select(array('id' => $ide));

        $query = $this->tableGateway->getSql()->select();
        $query->where(array(
            'franchise_id' => $id
        ));
        // echo $query->getSqlString();die;

        $rowset = $this->tableGateway->selectWith($query);
//        var_dump($rowset);die;

        $row = $rowset->current();


        if (!$row) {
            throw new Exception("Could not find row $id");
        }
        return $row;
    }
    public function getFranchiseSearch($id) {
        $id = (int) $id;
        // $rowset = $this->tableGateway->select(array('id' => $ide));

        $query = $this->tableGateway->getSql()->select();
        $query->where(array(
            'franchise_id' => $id
        ));
        // echo $query->getSqlString();die;

        $rowset = $this->tableGateway->selectWith($query);
//        var_dump($rowset);die;

//        $row = $rowset->current();


//        if (!$row) {
//            throw new Exception("Could not find row $id");
//        }
        return $rowset;
    }

    public function getById($id) {

        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('franchise_id' => $id));
        $row = $rowset->current();

        if (!$row) {
            throw new Exception("Could not find row $id");
        }

        return $row;
    }

    public function saveFranchise(Franchise $franchise) {
        //print_r($franchise);die;
        $idData = $franchise->franchise_id;
        $id = (int) $idData;
        $dataFranchise = $franchise->getArrayCopy();
        //print_r($franchiseArray);die;
        // echo $id;die;
        $dataFilter = array_filter($dataFranchise);

        if ($id == 0) {
            //print_r($dataFranchise);die;
            return $this->tableGateway->insert($dataFranchise);
        } else {
            // print_r($this->getById($id));die;
            if ($this->getById($id)) {
                $logoFranchise = $this->getLogo($id);
//                var_dump($logoBranch);die;
                if ($dataFilter['logo'] == "no-logo.jpg") {
                    if($logoFranchise->logo != "no-logo.jpg"){
                      $dataFilter['logo'] = $logoFranchise->logo;  
                      return $this->tableGateway->update($dataFilter, array('franchise_id' => $id));
                    }else{
                        return $this->tableGateway->update($dataFilter, array('franchise_id' => $id));
                    }
                } else {
                    return $this->tableGateway->update($dataFilter, array('franchise_id' => $id));
                }
                
                $bannerFranchise = $this->getBanner($id);
//                var_dump($logoBranch);die;
                if ($dataFilter['banner'] == "no-logo.jpg") {
                    if($bannerFranchise->banner != "no-logo.jpg"){
                      $dataFilter['banner'] = $bannerFranchise->banner;  
                      return $this->tableGateway->update($dataFilter, array('franchise_id' => $id));
                    }else{
                        return $this->tableGateway->update($dataFilter, array('franchise_id' => $id));
                    }
                } else {
                    return $this->tableGateway->update($dataFilter, array('franchise_id' => $id));
                }
            } else {
                throw new Exception('El Formulario no Existe');
            }
        }
    }
    public function updateProductStatus($status,$id)
    {

        $id = (int) $id;
        $status = (int) $status;

        $result = $this->tableGateway->update(array('status' => $status), array('franchise_id' => $id));

        return $result;
    }

    public function deleteFranchise($franchiseId) {
        $id = (int) $franchiseId;
        return $this->tableGateway->delete(array('franchise_id' => $id));
    }

    public function getTable($table) {
        $adapter = $this->tableGateway->getAdapter();
        $table = new TableGateway($table, $adapter);

        return $table;
    }

    //put your code here
}
