<?php

namespace Sale\Model\Dao;

use Sale\Model\Entity\Order;
use Sale\Model\Entity\OrderBranch;
use Sale\Model\Entity\Payment;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OrderDao {

    protected $tableGateway;
    protected $query;
    public $adapter;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getAll($columns = null) {
        $this->query = $this->tableGateway->getSql()->select();

        if (!$columns) {
            $columns = array('*');
        }

        $this->query->columns($columns);



        return $this;
    }

    public function where($where) {
        $this->query->where($where);

        return $this;
    }

    public function order($order) {
        $this->query->order($order);

        return $this;
    }

    public function whithProduct($columns = null) {

        if (!$columns) {
            $columns = array('*');
        }

        $this->query->join(array(
            'op' => 'pr_order_product'
                ), 'pr_order.order_id = op.order_id', $columns); // empty list of columns
        $this->query->join(array(
            'pb' => 'pr_product_has_branch'
                ), 'op.product_has_branch_id = pb.product_has_branch_id', array('*')); // empty list of columns

        return $this;
    }

    public function whithProductDescription($columns = null) {

        if (!$columns) {
            $columns = array('*');
        }

        $this->query->join(array(
            'p' => 'pr_product'
                ), 'pb.product_id = p.product_id', $columns); // empty list of columns

        return $this;
    }

    public function whithCustomer($columns = null) {

        if (!$columns) {
            $columns = array('*');
        }

        $this->query->join(array(
            'c' => 'pr_customer'
                ), 'pr_order.customer_id = c.customer_id', $columns); // empty list of columns

        return $this;
    }

    public function savePayConfirm(Payment $payment) {
        $tablePayment = $this->getTable('pr_payment');

        $data = $payment->getArrayCopy();
        array_filter($data);
        ///print_r($data);die;
        //print_r($dataBranch);die;
        $tablePayment->insert($data);
    }

    public function getOrder($id, $columns = null) {
        $this->query = $this->tableGateway->getSql()->select();
        if (!$columns) {
            $columns = array('*');
        }
        $this->query->columns($columns);
        $this->query->where(array('order_id' => $id));
        //$this->query->order("product_id DESC");
        //echo $query->getSqlString();die;
        return $this;
    }

    public function getLatest() {
        $this->query = $this->tableGateway->getSql()->select();
        $this->query->join(array('c' => 'pr_customer'), 'pr_order.customer_id = c.customer_id');
        $this->query->order('order_id DESC');
        $this->query->limit(10);
        return $this;
    }

    public function getOrderDetail($order_id) {

        $this->query = $this->tableGateway->getSql()->select();
        $this->query->columns(array('order_id'));
        $this->query->join(array('ob' => 'pr_order_branch'), 'pr_order.order_id = ob.order_id', ['*' , 'ob_date_added' => 'date_added']);
        $this->query->join(array('c' => 'pr_customer'), 'pr_order.customer_id = c.customer_id');
        $this->query->join(array('p' => 'pr_payment'), 'pr_order.invoice_number = p.invoice_number');
        $this->query->join(array('a' => 'pr_address'), 'c.address_default = a.address_id', array('address_1'));
        $this->query->where(array('pr_order.order_id' => $order_id));
//        echo $this->query->getSqlString();die;
        return $this;
    }
    public function getOrderDispatch($order_id) {

        $this->query = $this->tableGateway->getSql()->select();
        $this->query->columns(array('order_id'));
        $this->query->join(array('ob' => 'pr_order_branch'), 'pr_order.order_id = ob.order_id', array('order_dispatch'));
        $this->query->where(array('pr_order.order_id' => $order_id));
//        echo $this->query->getSqlString();die;
        return $this;
    }

    public function getBranchOrders($branch_id) {
        $this->query = $this->tableGateway->getSql()->select();
        $this->query->columns(array('order_id'));
        $this->query->join(array('ob' => 'pr_order_branch'), 'pr_order.order_id = ob.order_id', array('subtotal_order_branch' => 'subtotal', 'order_branch_id', 'order_status_branch' => 'order_status', 'invoice_number_branch' => 'invoice_number', 'customer_id', 'date_added', 'branch_id_order' => 'branch_id',));
//        $this->query->join(array('c' => 'pr_customer'), 'pr_order.customer_id = c.customer_id');
        $this->query->where(array('ob.branch_id' => $branch_id));
        $this->query->where->greaterThan('ob.order_status', 0);
         $this->query->order('order_id DESC');
//        $this->query->limit(10);
//        echo $this->query->getSqlString();die;
        return $this;
    }

    public function getSearchBranchOrders($branch_id, $date) {
        $month = $date['month'];
        $year = $date['year'];
        $day = $date['day'];

        $sql = "SELECT `pr_order`.*, `ob`.`subtotal` AS `subtotal_order_branch`, `ob`.`order_status` AS `order_status`, `ob`.`invoice_number` AS `invoice_number`, `ob`.`customer_id` AS `customer_id`, `ob`.`date_added` AS `date_added`, `ob`.`invoice_number` AS `invoice_number_branch`, `ob`.`order_status` AS `order_status_branch`"
                . "FROM `pr_order` "
                . "INNER JOIN `pr_order_branch` AS `ob` ON `pr_order`.`order_id` = `ob`.`order_id` "
                . "WHERE `ob`.`branch_id` = ? "
                . "AND `ob`.`order_status` > 0 "
                . "AND EXTRACT(day FROM `ob`.`date_added`) = ? "
                . "AND EXTRACT(month FROM `ob`.`date_added`) = ? "
                . "AND EXTRACT(year FROM `ob`.`date_added`) = ? "
                . "ORDER BY pr_order.order_id DESC";
//        echo $sql;die;
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, [$branch_id, $day, $month, $year]);
//        var_dump($result);die;
        return $result;
    }

    public function getBranchOrdersLastest($branch_id) {
        $this->query = $this->tableGateway->getSql()->select();
        $this->query->columns(array('order_id'));
        $this->query->join(array('ob' => 'pr_order_branch'), 'pr_order.order_id = ob.order_id', array('subtotal_order_branch' => 'subtotal', 'order_branch_id', 'order_status_branch' => 'order_status', 'invoice_number_branch' => 'invoice_number', 'customer_id', 'date_added', 'branch_id_order' => 'branch_id',));
//        $this->query->join(array('c' => 'pr_customer'), 'pr_order.customer_id = c.customer_id');
        $this->query->where(array('ob.branch_id' => $branch_id));
        $this->query->limit(10);
        $this->query->where->greaterThan('ob.order_status', 0);
        $this->query->order(array('order_id DESC'));
//        echo $this->query->getSqlString();die;
        return $this;
    }

    public function getTotalOrders($branch_id) {
        $sql = "SELECT COUNT(*) "
                . "AS total "
                . "FROM pr_order "
                . "INNER JOIN pr_order_branch AS ob ON pr_order.order_id = ob.order_id "
                . "WHERE ob.branch_id = ?";
//        echo $sql;die;
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, [$branch_id]);
//        var_dump($result->current());die;
        return $result->current();
    }

    public function getBranchDailyOrders($branch_id) {
        $sql = "SELECT `pr_order`.*, `ob`.`order_branch_id`, `ob`.`subtotal` AS `subtotal_order_branch`, `ob`.`order_status` AS `order_status`, `ob`.`invoice_number` AS `invoice_number`, `ob`.`customer_id` AS `customer_id`, `ob`.`date_added` AS `date_added`"
                . "FROM `pr_order` "
                . "INNER JOIN `pr_order_branch` AS `ob` ON `pr_order`.`order_id` = `ob`.`order_id` "
                . "WHERE `ob`.`branch_id` = ? "
                . "AND `ob`.`order_status` > 0 "
                . "AND EXTRACT(day FROM `ob`.`date_added`) = EXTRACT(day FROM CURRENT_DATE)"
                . "AND EXTRACT(month FROM `ob`.`date_added`) = EXTRACT(month FROM CURRENT_DATE) "
                . "ORDER BY pr_order.order_id DESC";
//        echo $sql;die;
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, [$branch_id]);
//        var_dump($result);die;
        return $result;
    }

    public function getSearchBranchDailyOrders($branch_id, $date) {
        $month = $date['month'];
        $year = $date['year'];
        $day = $date['day'];

        $sql = "SELECT `pr_order`.*, `ob`.`order_branch_id`, `ob`.`subtotal` AS `subtotal_order_branch`, `ob`.`order_branch_id`, `ob`.`order_status` AS `order_status`, `ob`.`invoice_number` AS `invoice_number`, `ob`.`customer_id` AS `customer_id`, `ob`.`date_added` AS `date_added`"
                . "FROM `pr_order` "
                . "INNER JOIN `pr_order_branch` AS `ob` ON `pr_order`.`order_id` = `ob`.`order_id` "
                . "WHERE `ob`.`branch_id` = ? "
                . "AND `ob`.`order_status` > 0 "
                . "AND EXTRACT(day FROM `ob`.`date_added`) = ? "
                . "AND EXTRACT(month FROM `ob`.`date_added`) = ? "
                . "AND EXTRACT(year FROM `ob`.`date_added`) = ? "
                . "ORDER BY pr_order.order_id DESC";
//        echo $sql;die;
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, [$branch_id, $day, $month, $year]);
//        var_dump($result);die;
        return $result;
    }

    public function getBranchMonthlyOrders($branch_id) {
//        echo $branch_id;die;
        $sql = "SELECT `pr_order`.*, `ob`.`order_branch_id`, `ob`.`subtotal` AS `subtotal_order_branch`, `ob`.`order_status` AS `order_status`, `ob`.`invoice_number` AS `invoice_number`, `ob`.`customer_id` AS `customer_id`, `ob`.`date_added` AS `date_added`"
                . "FROM `pr_order` "
                . "INNER JOIN `pr_order_branch` AS `ob` ON `pr_order`.`order_id` = `ob`.`order_id` "
                . "WHERE `ob`.`branch_id` = ? "
                . "AND `ob`.`order_status` > 0 "
                . "AND EXTRACT(month FROM `ob`.`date_added`) = EXTRACT(month FROM CURRENT_DATE) "
                . "ORDER BY pr_order.order_id DESC";
//        echo $sql; die;
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, [$branch_id]);
//        var_dump($result);die;
        return $result;
    }

    public function getSearchBranchMonthlyOrders($branch_id, $date) {
        $month = $date['month'];
        $year = $date['year'];
//        echo $month;
//        echo $branch_id;
        $sql = "SELECT `pr_order`.*, `ob`.`subtotal` AS `subtotal_order_branch`, `ob`.`order_status` AS `order_status`, `ob`.`invoice_number` AS `invoice_number`, `ob`.`customer_id` AS `customer_id`, `ob`.`date_added` AS `date_added`"
                . "FROM `pr_order` "
                . "INNER JOIN `pr_order_branch` AS `ob` ON `pr_order`.`order_id` = `ob`.`order_id` "
                . "WHERE `ob`.`branch_id` = ? "
                . "AND `ob`.`order_status` > 0 "
                . "AND EXTRACT(month FROM `ob`.`date_added`) = ? "
                . "AND EXTRACT(year FROM `ob`.`date_added`) = ? "
                . "ORDER BY pr_order.order_id DESC";
//        echo $sql; die;
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, [$branch_id, $month, $year]);
//        var_dump($result);die;
        return $result;
    }

    public function getProductOption($order_id, $branch_id) {
//        var_dump($branch_id);die;
        $this->query = $this->tableGateway->getSql()->select();
        $this->query->columns(array('order_id'));
        $this->query->join(array('ob' => 'pr_order_branch'), 'pr_order.order_id = ob.order_id');
        $this->query->join(array('obhp' => 'pr_order_branch_has_product'), 'ob.order_branch_id = obhp.order_branch_id');
        $this->query->join(array('op' => 'pr_order_product'), 'obhp.order_product_id = op.order_product_id');
        $this->query->join(array('phb' => 'pr_product_has_branch'), 'op.product_has_branch_id = phb.product_has_branch_id');
        $this->query->join(array('p' => 'pr_product'), 'phb.product_id = p.product_id');
        $this->query->where(array('pr_order.order_id' => $order_id, 'ob.branch_id' => $branch_id));
//        echo $this->query->getSqlString();die;

        return $this;
    }
    public function getProductOptionCustomer($order_id) {
//        var_dump($branch_id);die;
        $this->query = $this->tableGateway->getSql()->select();
        $this->query->columns(array('order_id'));
        $this->query->join(array('ob' => 'pr_order_branch'), 'pr_order.order_id = ob.order_id');
        $this->query->join(array('obhp' => 'pr_order_branch_has_product'), 'ob.order_branch_id = obhp.order_branch_id');
        $this->query->join(array('op' => 'pr_order_product'), 'obhp.order_product_id = op.order_product_id');
        $this->query->join(array('phb' => 'pr_product_has_branch'), 'op.product_has_branch_id = phb.product_has_branch_id');
        $this->query->join(array('p' => 'pr_product'), 'phb.product_id = p.product_id');
        $this->query->where(array('pr_order.order_id' => $order_id));
//        echo $this->query->getSqlString();die;

        return $this;
    }

    public function getProductOptionDetail($invoicenumerBranch, $order_product_id) {
        $sql = "SELECT p.name, p.product_id, pop.order_id, pop.order_product_id, ov.name AS nameO,pov.price AS priceO, o.name AS opcion
FROM pr_order_branch ob
JOIN pr_order_branch_has_product pbhp ON pbhp.order_branch_id = ob.order_branch_id
JOIN pr_order_product pop ON pbhp.order_product_id = pop.order_product_id
JOIN pr_product_has_branch phb ON pop.product_has_branch_id = phb.product_has_branch_id
JOIN pr_product p ON phb.product_id = p.product_id
LEFT JOIN pr_order_product_has_option opho ON pop.order_product_id = opho.order_product_id
LEFT JOIN pr_product_option_value pov ON opho.product_option_value_id = pov.product_option_value_id
LEFT JOIN pr_option_value ov ON pov.option_value_id = ov.option_value_id
LEFT JOIN pr_option o ON ov.option_id = o.option_id
WHERE ob.invoice_number_branch = ? AND pop.order_product_id = ?";
//        echo $sql;
//        echo $invoicenumerBranch;die;
//        echo $order_product_id;die;
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, [$invoicenumerBranch, $order_product_id]);
//        var_dump($result);die;
        return $result;
    }
    

    public function fetchAll() {
        $resultSet = $this->tableGateway->selectWith($this->query);
//        var_dump($resultSet);die;
        return $resultSet;
    }

    public function fetchAllCurrent() {
        $resultSet = $this->tableGateway->selectWith($this->query);
//        var_dump($resultSet);die;
        return $resultSet->current();
    }

    public function saveOrder(Order $order, $products = null) {
        $idOrder = $order->order_id;
        $id = (int) $idOrder;
        $dataOrder = $order->getArrayCopy();
//        array_filter($customer);die;
        //print_r($dataOrder);die;
        if ($id == 0) {
            //print_r($dataBranch);die;
            $this->tableGateway->insert($dataOrder);
            return $this->tableGateway->lastInsertValue;
        } else {

            $update = array_filter($dataOrder);
//            print_r($update);
//            die;
            $this->tableGateway->update($update, array('order_id' => $update['order_id']));
            return $order->order_id;
        }
    }

    public function updateOrder($order) {


        $this->tableGateway->update($order, array('order_id' => $order['order_id']));
        return $order['order_id'];
    }

    public function saveProductsOrder($orderId, $products) {
        // print_r($products);die;
        $table = $this->getTable('pr_order_product');
        foreach ($products as $product) {
            //print_r($product); die;
            $data['quantity'] = 1;
            $data['price'] = $product->precio;
            $data['total'] = $product->total;
            $data['tax'] = 12;
            $data['product_has_branch_id'] = $product->product_id;
            $data['order_id'] = $orderId;
            //print_r($data); die;
            $table->insert($data);

            $orderProduct = $table->getLastInsertValue();
            if ($product->options) {

                $this->saveOrderProductOption($orderProduct, $product->options);
            }
        }
        return $table->getLastInsertValue();
    }

    public function saveOrderProductOption($orderProduct, $options) {
        $table = $this->getTable('pr_order_product_has_option');

        foreach ($options as $option) {
            $data['product_option_value_id'] = $option;
            $data['order_product_id'] = $orderProduct;

            //print_r($data);
            $table->insert($data);
        }
        return $table->getLastInsertValue();
    }

    public function saveOrderBranch(OrderBranch $order) {

        $this->tableGateway = $this->getTable('pr_order_branch');

        $idOrder = $order->order_branch_id;
        $id = (int) $idOrder;

        $dataOrder = $order->getArrayCopy();
//        array_filter($customer);die;
        //print_r($dataOrder);die;
        if ($id == 0) {
            //print_r($dataBranch);die;
            $this->tableGateway->insert($dataOrder);

            return $this->tableGateway->lastInsertValue;
        } else {

            $update = array_filter($dataOrder);
            //print_r($update);die;
            return $this->tableGateway->update($update, array('order_id' => $update['order_id']));
        }
    }

    public function saveProductsOrderBranch($orderBranch, $orderProduct) {

        $table = $this->getTable('pr_order_branch_has_product');
        // print_r($products);die;
        $data['order_branch_id'] = $orderBranch;
        $data['order_product_id'] = $orderProduct;

        $table->insert($data);


        return $table->getLastInsertValue();
    }

    public function getUserOrders($user_id) {
        $resultSet = $this->tableGateway
                ->select(function (Select $select) use ($user_id) {

            $select->columns([
                'order_id',
                'invoice_number',
                'subtotal',
                'total_payment',
                'order_status',
                'date_added',
                'date_modified',
                'sum_products' => new Expression('count(op.product_has_branch_id)'),
            ]);

            $this->joinCustomer($select);
            $this->joinOrderBranch($select, ['order_dispatch']);
            $this->joinOrderProduct($select);
            $this->joinPayment($select, ['cod', 'tdc', 'payment_date_added' => 'date_added'  ]);

            $predicate = new Predicate();

            $select->where(array(
                $predicate->equalTo('cu.customer_id', $user_id),
                $predicate->greaterThan('pr_order.order_status', 0),
            ));

            $select->group(array('order_id', 'invoice_number', 'p.cod', 'p.tdc'));
            $select->order(array('order_id DESC'));
//            echo $select->getSqlString();die;
        });
        //var_dump($resultSet->toArray());die;
        return $resultSet;
    }

    public function getOrderProducts($order_id) {
        return $this->tableGateway
                        ->select(function (Select $select) use ($order_id) {

                            $select->columns([
                                'order_id',
                                'invoice_number',
                                'customer_id',
                                'total_base',
                                'total_payment',
                                'subtotal',
                                'order_status',
                                'sum_opciones' => new Expression('ROUND(SUM(pov.price), 2)')
                            ]);

                            $this->joinOrderProduct($select, '*');
                            $this->joinProductHasBranch($select, '*');
                            $this->joinProduct($select, '*');
                            $this->joinBranch($select, array('branch_name' => 'name'));
                            $this->joinFranchise($select, array('franchise_name' => 'name'));

                            $this->joinOrderProductHasOption($select);
                            $this->joinProductOptionValue($select);
                            $this->joinOptionValue($select);

                            $this->joinOption($select, ['options' => new Expression(
                                        'group_concat(concat(`ov`.`name`,\' (\', ROUND(`pov`.`price`, 2), \')\') separator \',\')'
                            )]);

                            $select->where(array(
                                'pr_order.order_id' => $order_id,
                            ));

                            $select->group(array(
                                'invoice_number',
                                'p.product_id',
                                'op.order_product_id',
                                'b.name',
                                'f.name',
                                'pb.branch_id'
                            ));
                            //echo $select->getSqlString();die;
                        });
    }

    public function orderTotal($order_id) {
        return $this->tableGateway
                        ->select(function (Select $select) use ($order_id) {

                            $fee = 12;

                            $select->columns([
                                'sum_product_price' => new Expression('SUM(p.price)'),
                                'sum_options_price' => new Expression('COALESCE(SUM(pov.price), 0)'),
                                'fee' => new Expression("ROUND((SUM(p.price)+ COALESCE(SUM(pov.price), 0))*{$fee}/100, 2)"),
                            ]);

                            $this->joinOrderProduct($select);
                            $this->joinProductHasBranch($select);
                            $this->joinProduct($select);

                            $this->joinOrderProductHasOption($select);
                            $this->joinProductOptionValue($select);

                            $select->where(array(
                                'pr_order.order_id' => $order_id
                            ));

                            $select->group(array('pr_order.order_id', 'invoice_number'));
                            //echo $select->getSqlString();die;
                        });
    }

    public function deleteProductsOrder($orderId) {

        $table = $this->getTable('pr_order_product');
        $result = $table->delete(array('order_id' => $orderId));

        return $result;
    }

    public function getCustomerOrder($customer, $order_id) {
       return $this->tableGateway
                        ->select(function (Select $select) use ($customer, $order_id) {
                            
                            
                            $this->joinCustomer($select);
                            $this->joinPayment($select, ['tdc', 'payment_date_added'  =>  'date_added' ]);
                            
                            $select->where(array(
                                'cu.customer_id' => $customer->customer_id,
                                'pr_order.order_id' => $order_id
                            ));
                        });
    }

    private function joinPayment(Select $select, $columns = []) {
        $select->join(array('p' => 'pr_payment'), 'p.order_id = pr_order.order_id', $columns, $select::JOIN_LEFT);
    }

    private function joinOption(Select $select, $columns = []) {
        $select->join(array('o' => 'pr_option'), 'o.option_id = ov.option_id', $columns, $select::JOIN_LEFT);
    }

    private function joinOptionValue(Select $select, $columns = []) {
        $select->join(array('ov' => 'pr_option_value'), 'ov.option_value_id = pov.option_value_id', $columns, $select::JOIN_LEFT);
    }

    private function joinProductOptionValue(Select $select, $columns = []) {
        $select->join(array('pov' => 'pr_product_option_value'), 'pov.product_option_value_id = opo.product_option_value_id', $columns, $select::JOIN_LEFT);
    }

    private function joinOrderProductHasOption(Select $select, $columns = []) {
        $select->join(array('opo' => 'pr_order_product_has_option'), 'opo.order_product_id = op.order_product_id', $columns, $select::JOIN_LEFT);
    }

    private function joinProduct(Select $select, $columns = []) {
        $select->join(array('p' => 'pr_product'), 'p.product_id = pb.product_id', $columns);
    }

    private function joinProductHasBranch(Select $select, $columns = []) {
        $select->join(array('pb' => 'pr_product_has_branch'), 'pb.product_has_branch_id = op.product_has_branch_id', $columns);
    }

    private function joinOrderProduct(Select $select, $columns = []) {
        $select->join(array('op' => 'pr_order_product'), 'op.order_id = pr_order.order_id', $columns);
    }

    private function joinCustomer(Select $select, $columns = []) {
        $select->join(array('cu' => 'pr_customer'), 'cu.customer_id = pr_order.customer_id', $columns);
    }
    private function joinOrderBranch(Select $select, $columns = []) {
        $select->join(array('ob' => 'pr_order_branch'), 'pr_order.order_id = ob.order_id', $columns);
    }

    private function joinBranch(Select $select, $columns = []) {
        $select->join(array('b' => 'pr_branch'), 'b.branch_id = pb.branch_id', $columns);
    }

    private function joinFranchise(Select $select, $columns = []) {
        $select->join(array('f' => 'pr_franchise'), 'f.franchise_id = b.franchise_id', $columns);
    }

    public function getTable($table) {
        $adapter = $this->tableGateway->getAdapter();
        $table = new TableGateway($table, $adapter);
        return $table;
    }

}
