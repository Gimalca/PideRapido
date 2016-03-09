<?php

namespace Catalog\Model\Dao;

/**
 * Description of ProductTable
 *
 * @author Pedro
 */
use Catalog\Model\Entity\Product;
use Catalog\Model\Entity\ProductOption;
use Catalog\Model\Entity\ProductOptionValue;
use Exception;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class ProductDao {

    protected $tableGateway;
    public $user_id;
    protected $query;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getImage($id) {

        $query = $this->tableGateway->getSql()->select();

        $query->columns(array('image'));
        $query->where(array(
            'product_id' => $id,
                // 'status' => 1,
        ));
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet->current();
    }

    public function saveProduct(Product $product, ProductOption $productOption = null) {
        //print_r($branch);die;
        $idData = $product->product_id;
        $id = (int) $idData;
        $dataProduct = $product->getArrayCopy();
        //print_r($dataProduct);die;
        //echo $id;die;
        $dataFilter = array_filter($dataProduct);
        //print_r($dataProduct);die;
        //echo $id;die;
        if ($id == 0) {
            $insert = $this->tableGateway->insert($dataFilter);

            $this->saveProductOption($productOption, $this->tableGateway->lastInsertValue);
            return $insert;
        } else {
            // print_r($this->getById($id));die;
            if ($this->getById($id)) {
                $imageProduct = $this->getImage($id);
//                var_dump($logoBranch);die;
                if ($dataFilter['image'] == "no-logo.jpg") {
                    //print_r($productOption);die;
                    if ($imageProduct->image != "no-logo.jpg") {
                        $dataFilter['image'] = $imageProduct->image;
                        $insert = $this->tableGateway->update($dataFilter, array('product_id' => $id));
                        
                    } else {
                        $insert = $this->tableGateway->update($dataFilter, array('product_id' => $id));
                       
                    }
                } else {
                    $insert = $this->tableGateway->update($dataFilter, array('product_id' => $id));
                   
                }
                
                if($insert){
                   
                    $this->saveProductOption($productOption, $id);
                }
                
                return $insert;
            } else {
                throw new \Exception('El Formulario no Existe');
            }
        }
    }

    public function updatePrice(ProductOptionValue $product) {
//        var_dump($product);die;
        $idData = $product->product_option_value_id;
        $id = (int) $idData;
//        echo $id;die;
        $dataProduct = $product->getArrayCopy();
        $dataFilter = array_filter($dataProduct);
//        var_dump($dataFilter);die;
        $insert = $this->tableGateway->update($dataFilter, array('product_option_value_id' => $id));

        return $insert;
    }

    public function saveProductOption(ProductOption $productOption, $productId) {
        //print_r($branch);die;
        $id = (int) $productId;
        $dataProduct = $productOption->getArrayCopy();

        //echo $id;die;
        $tableProductOption = $this->getTable('pr_product_option');

        if ($id) {
            //print_r($dataBranch);die;
            $contador = 0;
            foreach ($dataProduct['option_id'] as $option) {

                $insertOptions = array(
                    'product_id' => $id,
                    'option_id' => $option,
                    'required' => $dataProduct['required'][$contador],
                    //'status' => 1
                );
                $contador++;

                if ($option != 0) {
                    $tableProductOption->insert($insertOptions);
                }
            }

            return $tableProductOption->lastInsertValue;
        } else {
            // print_r($this->getById($id));die;
            if ($this->getById($id)) {
                //print_r($this->getById($id));print_r($dataBranch);die;
//                $dataFilter = array_filter($dataProduct);
//                print_r($dataProduct);die;
                return $this->tableGateway->update($dataProduct, array('product_id' => $id));
            } else {
                throw new \Exception('El Formulario no Existe');
            }
        }
    }

    public function saveProductOptionValue($data) {

        $tableProductOptionValue = $this->getTable('pr_product_option_value');

        //print_r($dataBranch);die;
        $contador = 0;
        foreach ($data['option_value_id'] as $value) {

            $insertOptions = array(
                'product_option_id' => $data['product_option_id'],
                'product_id' => $data['product_id'],
                'option_id' => $data['option_id'],
                'option_value_id' => $value,
                'price' => $data['price'][$contador],
                'status' => 1
            );
            $contador++;

            if ($value != 0) {
                // print_r($insertOptions);
                $tableProductOptionValue->insert($insertOptions);
            }
        }

        return 1;
    }

    public function getById($id) {

        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('product_id' => $id));
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    private function getOptionValue($optionId) {

        //print_r($categoryDescription);die;
        //print_r($categoryDescriptionData);die;
        $table = $this->getTable('pr_product_option');

        $query = $table->getSql()->select();

        $query->join(array(
            'o' => 'pr_option'
                ), 'o.option_id = pr_product_option.option_id');
        $query->join(array(
            'ov' => 'pr_option_value'
                ), "pr_product_option.option_id = ov.option_id");
        $query->where(array(
            'pr_product_option.option_id' => $optionId
        ));

        $resultSet = $table->selectWith($query);
        var_dump($resultSet);

        return $resultSet;
    }

    public function updateProductStatus($status, $id) {

        $id = (int) $id;
        $status = (int) $status;

        $result = $this->tableGateway->update(array('status' => $status), array('product_id' => $id));

        return $result;
    }
    public function updateProductHasBranchStatus($status, $id) {

        $id = (int) $id;
        $status = (int) $status;
        $table = $this->getTable('pr_product_has_branch');

        $result = $table->update(array('status_product_has_branch' => $status), array('product_has_branch_id' => $id));

        return $result;
    }

    public function updateOptionStatus($status, $id) {

        $id = (int) $id;
        $status = (int) $status;
        $table = $this->getTable('pr_product_option_value');

        $result = $table->update(array('status' => $status), array('product_option_value_id' => $id));

        return $result;
    }
    public function updateOptionGeneralStatus($status, $id) {

        $id = (int) $id;
        $status = (int) $status;
        $table = $this->getTable('pr_option');

        $result = $table->update(array('status' => $status), array('option_id' => $id));

        return $result;
    }

    public function getAll($columns = null) {

        $this->query = $this->tableGateway->getSql()->select();

        if (!$columns) {
            $columns = array('*');
        }
        $this->query->columns($columns);
        //$this->query->order("product_id DESC");

        return $this;
    }

    public function where(Array $where) {
        $this->query->where($where);
        return $this;
    }

    public function whithFranchise($columns = null) {

        if (!$columns) {
            $columns = array('*');
        }

        $this->query->join(array(
            'f' => 'pr_franchise',
                ), 'pr_product.franchise_id = f.franchise_id', $columns); // empty list of columns

        return $this;
    }
    
    public function whithBranch($columns = null) {

        if (!$columns) {
            $columns = array('*');
        }

        $this->query->join(array(
            'b' => 'pr_branch',
                ), 'pr_product_has_branch.branch_id = b.branch_id', $columns); // empty list of columns

        return $this;
    }


    public function whithOptions() {

        $this->query->join(array('po' => 'pr_product_option'), 'pr_product.product_id = po.product_id', array('product_option_id',
            'required',)
        ); // empty list of columns
        $this->query->join(array('o' => 'pr_option'), 'po.option_id = o.option_id', array('option_id',
            'name_option' => 'name',
            'sort_order',
            'status')
        ); // empty list of columns
        $this->query->join(array('ot' => 'pr_option_type'), 'o.type_id = ot.type_id', array('name_type' => 'name',
            'type_id')
        ); // empty list of columns
        $this->where(['o.status' => '1']);
        $this->query->order('o.sort_order');

        return $this;
    }

    public function whithOptionsValue() {

        $this->query->join(array('po' => 'pr_product_option'), 'pr_product.product_id = po.product_id', array('product_option_id')
        ); // empty list of columns
        $this->query->join(array('o' => 'pr_option'), 'po.option_id = o.option_id', array('option_id',
            'name_option' => 'name',
            'status')
        );
        $this->query->join(array('pov' => 'pr_product_option_value'), 'po.product_option_id = pov.product_option_id', array('price', 'status_option_product' => 'status', 'product_option_value_id'));

        $this->query->join(array('ov' => 'pr_option_value'), 'pov.option_value_id = ov.option_value_id', array(
            'name_option_value' => 'name')
        ); // empty list of columns
        return $this;
    }

    public function whithProduct($colums = null) {

        if (!$colums) {
            $colums = '*';
        }

        $this->query->join(array('pr_product' => 'pr_product'), 'pr_product_has_branch.product_id = pr_product.product_id', $colums); // empty list of columns
       
        return $this;
    }

    public function whithOptionValue() {

        $this->query->join(array('pov' => 'pr_product_option_value'), 'pr_product.product_id = pov.product_id', array('*')); // empty list of columns

        $this->query->join(array('ov' => 'pr_option_value'), 'pov.option_value_id = ov.option_value_id', array('*')); // empty list of columns

        return $this;
    }

    public function whithLimit($int) {
        $this->query->limit($int);

        return $this;
    }

    public function fetchAll() {
//        echo $this->query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($this->query);
        //var_dump($resultSet->toArray());die;

        return $resultSet;
    }

    public function fetchAllArray() {

        $resultSet = $this->tableGateway->selectWith($this->query);
        //var_dump($resultSet->toArray());die;

        return $resultSet->toArray();
    }

    public function getAllCombo($id) {
        $query = $this->tableGateway->getSql()->select();
        $query->where(array(
            'franchise_id' => $id,
            'category_id' => 1
        ));
        $query->order("category_id ASC");
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet;
    }

    public function getAllBranchProduct($id) {

        $query = $this->tableGateway->getSql()->select();
        $query->order("product_id DESC");
        $query->where(array('branch_id' => $id));
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
        //var_dump($resultSet->toArray());die;

        return $resultSet;
    }

    public function getProduct($id) {
        $id = (int) $id;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();

        $query->join(array('f' => 'pr_franchise'), 'pr_product.franchise_id = f.franchise_id', array('name_franchise' => 'name')) // empty list of columns
                ->where(array('pr_product.product_id' => $id));
        $query->join(array('c' => 'pr_product_category'), 'pr_product.category_id = c.category_id', array('name_category' => 'name')) // empty list of columns
                ->where(array('pr_product.product_id' => $id));

        //echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet->toArray());die;

        return $resultSet->current();
    }

    public function deleteProduct($productId) {
        $id = (int) $productId;
        $result = $this->tableGateway->delete(array('product_id' => $id));
//        $table = $this->getTable('pr_branch_contact');
//        $table->delete(array('branch_id' => $id));

        return $result;
    }

    public function getProductHasBranch($id) {
        $id = (int) $id;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();

        $query->join(array('phb' => 'pr_product_has_branch'), 'pr_product.product_id = phb.product_id', array('product_has_branch_id')); // empty list of columns
        //       ->where(array('phb.branch_id' => $id));
        $query->join(array('b' => 'pr_branch'), 'b.branch_id = phb.branch_id', array('name_branch' => 'name', 'branch_id')); // empty list of columns
        $query->join(array('f' => 'pr_franchise'), 'pr_product.franchise_id = f.franchise_id', array('name_franchise' => 'name')) // empty list of columns
                ->where(array('phb.product_has_branch_id' => $id));

        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet->toArray());die;

        return $resultSet;
    }

    public function getProductsBranch($id) {
        $id = (int) $id;
//         echo $id;die;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();

        $query->join(array('phb' => 'pr_product_has_branch'), 'pr_product.product_id = phb.product_id', array('product_has_branch_id')) // empty list of columns
                ->where(array('phb.branch_id' => $id));
        $query->join(array('b' => 'pr_branch'), 'b.branch_id = phb.branch_id', array('name_branch' => 'name', 'branch_id')); // empty list of columns
        $query->join(array('f' => 'pr_franchise'), 'pr_product.franchise_id = f.franchise_id', array('name_franchise' => 'name')); // empty list of columns
//                ->where(array('phb.product_has_branch_id' => $id));

        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet->toArray());die;

        return $resultSet;
    }
    public function getProductsBranchSearch($id) {
        $id = (int) $id;
//         echo $id;die;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();

        $query->join(array('phb' => 'pr_product_has_branch'), 'pr_product.product_id = phb.product_id', array('product_has_branch_id')) // empty list of columns
                ->where(array('pr_product.product_id' => $id));
        $query->join(array('b' => 'pr_branch'), 'b.branch_id = phb.branch_id', array('name_branch' => 'name', 'branch_id')); // empty list of columns
        $query->join(array('f' => 'pr_franchise'), 'pr_product.franchise_id = f.franchise_id', array('name_franchise' => 'name')); // empty list of columns
//                ->where(array('phb.product_has_branch_id' => $id));

        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet->toArray());die;

        return $resultSet;
    }

    public function getProductFromBranch($id) {
        $id = (int) $id;
        $query = $this->tableGateway->getSql()->select();
        $this->joinProductHasBranch($query);
        $this->joinFranchise($query);
        $this->joinBranch($query);
        $query->where(array('phb.product_has_branch_id' => $id));
        $resultSet = $this->tableGateway->selectWith($query);
        return $resultSet;
    }

    private function joinProductHasBranch($select) {
        $select->join(
                array('phb' => 'pr_product_has_branch'), 'pr_product.product_id = phb.product_id', array('product_has_branch_id')
        );
    }

    private function joinFranchise($select) {
        $select->join(
                array('f' => 'pr_franchise'), 'pr_product.franchise_id = f.franchise_id', array('name_franchise' => 'name')
        );
    }

    private function joinBranch($select) {
        $select->join(
                array('b' => 'pr_branch'), 'b.branch_id = phb.branch_id', array('name_branch' => 'name', 'branch_id', 'status_branch' => 'status')
        );
    }
    
    public function getSqlString()
    {
        echo  $this->query->getSqlString();die;
    }

    public function getTable($table) {
        $adapter = $this->tableGateway->getAdapter();
        $table = new TableGateway($table, $adapter);

        return $table;
    }

    //put your code here
}
