<?php

namespace Sale\Model\Dao;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Json\Json;

class CartDao {

    protected $tableGateway;
    protected $cart;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

           /* [
            ['product_id' => 8, 'options' => ['2', '3', '4']],
            ['product_id' => 9, 'options' => ['2', '3', '4']],
            ['product_id' => 11, 'options' => ['2', '3', '4']],
            ['product_id' => 14, 'options' => ['2', '3', '4']],
        ]; */

    public function cart($user) {

        if (isset($user->customer_id)) {
            $id = (int) $user->customer_id;
            //consultar el carrito
            $userTable = $this->getTable('pr_customer');
            $query = $userTable->getSql()->select();
            $query->where(['customer_id'=> $id]);
            $customer = $this->tableGateway->selectWith($query)->current();
            //var_dump($customer->cart);die;
            $this->cart = Json::decode($customer->cart, true);
            return $this;
        }
    }

    public function products() {
        $products = [];

        if($this->cart){
            foreach($this->cart as $product) {
                if (is_numeric($product['product_id'])) {
                    $item = $this->productOptionsPrice(
                        $product['product_id'], $product['options'])
                        ->toArray();
                    //var_dump($product);die;
                    if (!empty($item) && is_array($item)) {
                        array_push($products, $item[0]);
                    }
                }
            }
        }
        //print_r($products);die;
        return $products;
    }

    public function productOptionsPrice($product_id, $options) {
        if (is_null($product_id) || !isset($product_id)) return;
        //var_dump($product_id);die;
        //var_dump($options);die;

        $resultSet = $this->tableGateway->select(function (Select $select) use ($product_id, $options) {

            if (empty($options)) {
                $select->columns([
                    'product_id'       => 'product_has_branch_id',
                    'branch_id',
                    'total'            => new Expression('ROUND(p.price, 2)'),
                    'cantidadopciones' => new Expression('0'),
                    'subtotalopciones' => new Expression('0')
                ]);

                $select->join(array('p' => 'pr_product'),
                    'p.product_id = pr_product_has_branch.product_id', array('name', 'precio' => 'price', 'image', 'description'));
                $select->join(array('f' => 'pr_franchise'),
                         'p.franchise_id = f.franchise_id', array('name_franchise' => 'name')); // empty list of columns

                $select->where(array(
                    'product_has_branch_id' => $product_id,
                ));
            } else {
                $select->columns([
                    'product_id'       => 'product_has_branch_id',
                    'branch_id',
                    'total'            => new Expression('ROUND(p.price + SUM(COALESCE(pov.price, 0)), 2)'),
                    'cantidadopciones' => new Expression('COUNT(pov.product_option_value_id)'),
                    'subtotalopciones' => new Expression('ROUND(SUM(COALESCE(pov.price, 0)), 2)')
                ]);

                $select->join(array('p' => 'pr_product'),
                    'p.product_id = pr_product_has_branch.product_id', array('name', 'precio' => 'price', 'image', 'description'));

                $select->join(array('pov'=> 'pr_product_option_value'),
                    'p.product_id = pov.product_id', array());

                $select->join(array('f' => 'pr_franchise'),
                         'p.franchise_id = f.franchise_id', array('name_franchise' => 'name')); // empty list of columns


                $select->where(array(
                    'product_has_branch_id' => $product_id,
                    'pov.product_option_value_id' => $options
                ));

                $select->group(array('product_has_branch_id','branch_id', 'p.product_id'));
                //echo $select->getSqlString();
            }
        });
        return $resultSet;
    }

    public function getProducts($id) {
        $id = (int) $id;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();
        $query->columns(array('branch_id','product_has_branch_id'));
        $query->join(array('p' => 'pr_product'), 'pr_product_has_branch.product_id = p.product_id',array('product_id','name','price','image'))
                ->where(array('pr_product_has_branch.product_has_branch_id' => $id));

        //echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet);die;

        return $resultSet->toArray();
    }

    public function getCombo($id) {
        $id = (int) $id;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();

        $query->join(array('c' => 'pr_combo'), 'pr_combo_has_branch.combo_id = c.combo_id')
                ->where(array('pr_combo_has_branch.combo_has_branch_id' => $id));

        //echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet);die;

        return $resultSet->toArray();
    }

    public function getUser($data) {

        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();

        $query->columns(array('email'));
        $query->where(array('email' => $data));


        //echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet);die;

        return $resultSet->toArray();
    }

    public function getCart($data) {
        $email = $data[0]['email'];
//        echo $email;die;
        // $rowset = $this->tableGateway->select(array('id' => $ide));
        $query = $this->tableGateway->getSql()->select();

        $query->columns(array('cart'));
        $query->where(array('email' => $email));

        //echo $query->getSqlString();die;
        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet);die;

        return $resultSet->toArray();
    }

    public function saveCart($cart, $data) {
        //print_r($dataFranchise);die;
        $idData = $franchise->franchise_id;
        $id = (int) $idData;
        $dataFranchise = $franchise->getArrayCopy();
        //print_r($franchiseArray);die;
        // echo $id;die;

        if ($id == 0) {
            //print_r($dataFranchise);die;
            return $this->tableGateway->insert($dataFranchise);
        } else {
            // print_r($this->getById($id));die;
            if ($this->getById($id)) {
                //print_r($dataFranchise);print_r($this->getById($id));die;
                return $this->tableGateway->update($dataFranchise, array('franchise_id' => $id));
            } else {
                throw new \Exception('El Formulario no Existe');
            }
        }
    }

    public function getTable($table) {
        $adapter = $this->tableGateway->getAdapter();
        $table = new TableGateway($table, $adapter);

        return $table;
    }

}
