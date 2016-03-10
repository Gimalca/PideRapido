<?php

namespace Franchise\Model\Dao;

/**
 * Description of ProductTable
 *
 * @author Pedro
 */
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Franchise\Model\Entity\Branch;
use Franchise\Model\Entity\Franquimovil;
use Franchise\Model\Entity\BranchContact;

class BranchDao {

    protected $tableGateway;
    public $user_id;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getAll($id) {

        $query = $this->tableGateway->getSql()->select();

        $query->join(array('f' => 'pr_franchise'), 'pr_branch.franchise_id = f.franchise_id', array('name_franchise' => 'name')); // empty list of columns

        $query->where(array(
            
           'pr_branch.franchise_id' => $id,
            'pr_branch.status < 2',
                 
        ));
        $query->order("branch_id ASC");
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet;
    }
    public function getOperator($id) {

        $query = $this->tableGateway->getSql()->select();
        $query->where(array(
            'pr_operator.branch_id' => $id,
                // 'status' => 1,
        ));
        $query->order("operator_id ASC");
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet;
    }
    public function getOperatorSearch($id, $operator_id) {

        $query = $this->tableGateway->getSql()->select();
        $query->where(array(
            'pr_operator.branch_id' => $id,
            'pr_operator.operator_id' => $operator_id,
                // 'status' => 1,
        ));
        $query->order("operator_id ASC");
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet;
    }

    public function getLogo($id) {

        $query = $this->tableGateway->getSql()->select();

        $query->columns(array('logo'));
        $query->where(array(
            'branch_id' => $id,
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
            'branch_id' => $id,
                // 'status' => 1,
        ));
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet->current();
    }
    
     public function getByCity($id, $cityId) {
        
        $query = $this->tableGateway->getSql()->select();
        
        $query->join(array('f' => 'pr_franchise'), 
                'pr_branch.franchise_id = f.franchise_id', 
                array('name_franchise' => 'name')) ;// empty list of columns
        
        $query->where(array(
            'pr_branch.franchise_id' => $id,
            'pr_branch.city_id' => $cityId,
           // 'status' => 1,
        ));
        $query->order("branch_id ASC");
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet;
    }

    public function updateProductStatus($status, $id) {

        $id = (int) $id;
        $status = (int) $status;

        $result = $this->tableGateway->update(array('status' => $status), array('branch_id' => $id));

        return $result;
    }

    public function getBranch($id) {
        $id = (int) $id;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();

        $query->join(array('f' => 'pr_franchise'), 'pr_branch.franchise_id = f.franchise_id', array('name_franchise' => 'name')) // empty list of columns
                ->where(array('pr_branch.branch_id' => $id));

        //echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet->current();
    }
    public function getBranchSelect($id) {
        $id = (int) $id;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();

        $query->join(array('f' => 'pr_franchise'), 'pr_branch.franchise_id = f.franchise_id', array('name_franchise' => 'name')) // empty list of columns
                ->where(array('pr_branch.branch_id' => $id));

        //echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet;
    }
    
    public function getBranchByProduct($id) {
        $id = (int) $id;
//        echo $id;die;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();

        $query->join(array('p' => 'pr_product_has_branch'), 'pr_branch.branch_id = p.branch_id') // empty list of columns
                ->where(array('p.product_id' => $id));

        //echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet->current();
    }
    public function getBranchContact($id) {
        $id = (int) $id;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();

        $query->where(array('pr_branch_contact.branch_id' => $id));

        //echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet->toArray());die;

        return $resultSet->current();
    }
    public function getBranchContactByUser($user, $columns = null) {
        $sql = $this->tableGateway->getSql();

        $query = $sql->select()
                ->columns($columns)
//        $query->join(array('b' => 'pr_branch'), 'pr_branch_contact.branch_id = b.pr_branch')
                ->where(array('pr_branch_contact.user' => $user));
//        $query->where->lessThan('ob.order_status', 2);
//        echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);

//        print_r($resultSet);die;
        $row = $resultSet->current();
        return $row;
    }
    

    public function getBranchProducts($id) {
        $id = (int) $id;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();

        $query->join(array('p' => 'pr_product'), 'pr_product_has_branch.product_id = p.product_id') // empty list of columns
                ->where(array('pr_product_has_branch.branch_id' => $id, 'pr_product_has_branch.status_product_has_branch < 2'));

        //echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet;
    }

    public function getFranchise($id) {
        $id = (int) $id;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();

        $query->columns(array('franchise_id')) // empty list of columns
                ->where(array('branch_id' => $id));

        //echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet->current();
    }

    public function getBranchFull($id) {
        $id = (int) $id;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();
        $query->join(array('f' => 'pr_franchise'), 'pr_branch.franchise_id = f.franchise_id', array('name_franchise' => 'name')) // empty list of columns
                ->where(array('pr_branch.branch_id' => $id));

        //echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
        return $resultSet->current();
    }

    public function getById($id) {

        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('branch_id' => $id));
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    public function getId($id) {

        $id = (int) $id;
        $query = $this->tableGateway->getSql()->select();

        $query->columns(array('branch_id')) // empty list of columns
                ->where(array('branch_id' => $id));

        $resultSet = $this->tableGateway->selectWith($query);

        if (!$resultSet) {
            throw new \Exception("Could not find row $id");
        }

        return $resultSet->current();
    }

    public function saveBranch(Branch $branch, BranchContact $branchContact) {
//        var_dump($branch);die;
        $idData = $branch->branch_id;
        $id = (int) $idData;
        $dataBranch = $branch->getArrayCopy();
//        var_dump($branchContact);die;
//        echo $id;die;
        $dataFilter = array_filter($dataBranch);

        if ($id == 0) {
            //print_r($dataBranch);die;
            //print_r($dataFilter);

            $insert = $this->tableGateway->insert($dataFilter);
            //var_dump($insert);die;
            if ($insert) {
                $branch_id = $this->tableGateway->getLastInsertValue();
                //print_r($user_id);die;
                $branchContact->branch_id = $branch_id;
//                print_r($branchContact);die;
                return $this->saveBranchContact($branchContact);
                //print_r($branch_id);die;
            }
        } else {
            // print_r($this->getById($id));die;
            if ($this->getById($id)) {
              
                if ($dataFilter['logo'] == "no-logo.jpg") {
                    $logoBranch = $this->getLogo($id);
                    if ($logoBranch->logo != "no-logo.jpg") {
                        $dataFilter['logo'] = $logoBranch->logo;
                    }
                }

                if ($dataFilter['banner'] == "no-logo.jpg") {
                    $bannerBranch = $this->getBanner($id);
                    if ($bannerBranch->banner != "no-logo.jpg") {
                        $dataFilter['banner'] = $bannerBranch->banner;
                    }
                }

                //print_r($dataFilter);die;
                      $insert = $this->tableGateway->update($dataFilter, array('branch_id' => $id));
                      $insert = $this->updateBranchContact($branchContact);
                      return $insert;
                   
                } 
                
            } 
        }

    public function saveProductHasBranch($branchProduct) {
        //print_r($branch);die;
        $dataBranchProduct = $branchProduct->getArrayCopy();

        //    print_r($dataBranchProduct);
        $contador = 0;
        foreach ($dataBranchProduct['product_id'] as $optionValues) {
            $insertOptions = array(
                'product_id' => $dataBranchProduct['product_id'][$contador],
                'branch_id' => $dataBranchProduct['branch_id'],
            );
            $contador++;
            //print_r($insertOptions);
            $insert = $this->tableGateway->insert($insertOptions);
        }
        return $insert;
    }

    public function saveOperator($operatorData) {
        //print_r($branch);die;        
        $dataOperator = $operatorData->getArrayCopy();
//        var_dump($dataOperator);die;
        return $this->tableGateway->insert($dataOperator);
    }

    private function updateBranchContact(BranchContact $branchContact) {

//        var_dump($branchContact);die;
        $branchContactData = $branchContact->getArrayCopy();
        $id = $branchContact->branch_contact_id;
//        echo $id;die;
        //print_r($categoryDescriptionData);die;
        $table = $this->getTable('pr_branch_contact');

        $insert = $table->update($branchContactData, array('branch_contact_id' => $id));
//        var_dump($insert);die;

        return $insert;
    }
    public function updateBranchContactByBranch(BranchContact $branchContact) {

//        var_dump($branchContact);die;
        $branchContactData = $branchContact->getArrayCopy();
        $id = $branchContact->branch_id;
//        echo $id;die;
        //print_r($categoryDescriptionData);die;
        $table = $this->getTable('pr_branch_contact');

        $insert = $table->update($branchContactData, array('branch_id' => $id));
//        var_dump($insert);die;

        return $insert;
    }
    private function saveBranchContact(BranchContact $branchContact) {

        //print_r($categoryDescription);die;
        $branchContactData = $branchContact->getArrayCopy();
        //print_r($categoryDescriptionData);die;
        $table = $this->getTable('pr_branch_contact');

        $insert = $table->insert($branchContactData);
//        var_dump($insert);die;

        return $table->getLastInsertValue();
    }

    public function deleteBranch($branchId) {
        $id = (int) $branchId;
        echo $id;
//        $result = $this->tableGateway->delete(array('branch_id' => $id));
        $table = $this->getTable('pr_branch');
//        var_dump($table);die;
        $table->delete(array('branch_id' => $id));
        var_dump($table);die;

        return $result;
    }
    public function deleteOperator($id) {
        $id = (int) $id;
//        echo $id;die;
        $result = $this->tableGateway->delete(array('operator_id' => $id));
//        $table = $this->getTable('pr_branch_contact');
//        $table->delete(array('branch_id' => $id));
//        echo $result->getSqlString();die;
        return $result;
    }

    public function deleteBranchProduct($productHasBranchId) {
        $id = (int) $productHasBranchId;
        $result = $this->tableGateway->delete(array('product_has_branch_id' => $id));
//        $table = $this->getTable('pr_branch_contact');
//        $table->delete(array('branch_id' => $id));
//        var_dump($result);die;
        return $result;
    }

    public function getTable($table) {
        $adapter = $this->tableGateway->getAdapter();
        $table = new TableGateway($table, $adapter);

        return $table;
    }

    //put your code here
}
