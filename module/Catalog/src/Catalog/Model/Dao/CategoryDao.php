<?php

namespace Catalog\Model\Dao;

/**
 * Description of ProductTable
 *
 * @author Pedro
 */

use Catalog\Model\Entity\Product;
use Exception;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class CategoryDao {

    protected $tableGateway;
    public $user_id;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }    

    public function saveProduct(Product $product) {
        //print_r($branch);die;
        $idData = $product->product_id;
        $id = (int) $idData;
        $dataProduct = $product->getArrayCopy();
        print_r($dataProduct);die;
        //echo $id;die;

        if ($id == 0) {
            //print_r($dataBranch);die;
            $dataFilter = array_filter($dataProduct);
            //print_r($dataFilter);
                    
            return $insert = $this->tableGateway->insert($dataFilter);
            //var_dump($insert);die;
        } 
    }
    public function getAll() {
        $query = $this->tableGateway->getSql()->select();

        $query->order("category_id DESC");
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet;
    }

    public function deleteBranch($branchId) {
        $id = (int) $branchId;
        $result = $this->tableGateway->delete(array('branch_id' => $id));
//        $table = $this->getTable('pr_branch_contact');
//        $table->delete(array('branch_id' => $id));

        return $result;
    }

    public function getTable($table) {
        $adapter = $this->tableGateway->getAdapter();
        $table = new TableGateway($table, $adapter);

        return $table;
    }

    //put your code here
}
