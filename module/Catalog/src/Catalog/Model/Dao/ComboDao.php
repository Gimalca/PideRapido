<?php

namespace Catalog\Model\Dao;

/**
 * Description of ProductTable
 *
 * @author Pedro
 */
use Catalog\Model\Entity\Combo;
use Catalog\Model\Entity\Product;
use Exception;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class ComboDao {

    protected $tableGateway;
    public $user_id;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function saveCombo(Combo $combo, $productsId) {
//        print_r($combo);die;
        $idData = $combo->combo_id;
        $id = (int) $idData;
        $dataCombo = $combo->getArrayCopy();
        //print_r($dataProduct);die;
        //echo $id;die;

        if ($id == 0) {

            $insert = $this->tableGateway->insert($dataCombo);
            if ($insert) {
                $comboId = $this->tableGateway->getLastInsertValue();
                $comboHasProduct = $this->saveComboHasProduct($comboId, $productsId);

                return $comboId;
            }
        } else {
            // print_r($this->getById($id));die;
            if ($this->getById($id)) {
                //print_r($this->getById($id));print_r($dataBranch);die;
//                $dataFilter = array_filter($dataProduct);
//                print_r($dataProduct);die;
                $update = $this->tableGateway->update($dataCombo, array('combo_id' => $id));
                if ($update) {
                    
                    $comboHasProduct = $this->saveComboHasProduct($id, $productsId);

                    return $id;
                }
            } else {
                throw new \Exception('El Formulario no Existe');
            }
        }
    }

    protected function saveComboHasProduct($comboId, $productsId) {
        $id = (int) $comboId;
//        var_dump($productsId);die;

        $table = $this->getTable('pr_product_has_combo');

        foreach ($productsId as $products) {
            $insertProducts = array(
            
                'product_id' => $products,
                'combo_id' => $id,
            );
//            print_r($products);die;
//            var_dump($insertProducts);die;
            $saved = $table->insert($insertProducts);
        }
    }

    public function getById($id) {

        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('combo_id' => $id));
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    public function updateComboStatus($status, $id) {

        $id = (int) $id;
        $status = (int) $status;

        $result = $this->tableGateway->update(array('status' => $status), array('combo_id' => $id));

        return $result;
    }

    public function getAll($id) {
        $query = $this->tableGateway->getSql()->select();
        $query->where(array(
            'franchise_id' => $id
        ));
        $query->order("combo_id ASC");
//        echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet;
    }
    public function getFranchise($id) {
        $id = (int) $id;
        
        $query = $this->tableGateway->getSql()->select();
        $query->columns(array('franchise_id'));
        $query->where(array('branch_id' => $id));

//        echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet->toArray());die;

        return $resultSet->current();
    }

    public function getCombo($id) {
        $id = (int) $id;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();

        $query->join(array('f' => 'pr_franchise'), 'pr_combo.franchise_id = f.franchise_id', array('name_franchise' => 'name')) // empty list of columns
                ->where(array('pr_combo.combo_id' => $id));

        //echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet->toArray());die;

        return $resultSet->current();
    }

    public function getComboHasProduct($id) {
        $id = (int) $id;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();
        $query->join(array('p' => 'pr_product'), 'pr_product_has_combo.product_id = p.product_id') // empty list of columns
                ->where(array('pr_product_has_combo.combo_id' => $id));

//        echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet);die;

        return $resultSet;
    }
    public function getAllCombo($id) {
        $id = (int) $id;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();
        $query->join(array('c' => 'pr_combo'), 'pr_combo_has_branch.combo_id = c.combo_id') // empty list of columns
                ->where(array('pr_combo_has_branch.branch_id' => $id));

//        echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet);die;

        return $resultSet;
    }

    public function deleteCombo($comboId) {

        $id = (int) $comboId;
        $result = $this->tableGateway->delete(array('combo_id' => $id));
//        $table = $this->getTable('pr_branch_contact');
//        $table->delete(array('branch_id' => $id));

        return $result;
    }

    public function deleteComboProduct($productId, $comboId) {

        $id = (int) $comboId;
        $idc = (int) $productId;
//        echo $id,$idc;die;
        $query = $this->tableGateway->getSql()->delete();
        $query->where(array('combo_id' => $id, 'product_id' => $idc));

//        echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->deleteWith($query);
//        $table = $this->getTable('pr_branch_contact');
//        $table->delete(array('branch_id' => $id));

        return $resultSet;
    }

    public function getTable($table) {
        $adapter = $this->tableGateway->getAdapter();
        $table = new TableGateway($table, $adapter);

        return $table;
    }

    //put your code here
}
