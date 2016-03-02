<?php namespace Catalog\Model\Dao;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Catalog\Model\Entity\ProductOptionValue;

class OptionsDao {

    private $tableGateway;
    private $query;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function all($where=null) {
        $this->query = $this->tableGateway->getSql()->select($where);
        return $this;
    }

    public function byId($id) {
        $this->query
            ->where(array('product_option_value_id' => $id));
        return $this;
    }

    public function columns($columns) {
        $this->query->columns($columns);
        return $this;
    }

    public function where($where) {
        $this->query->where($where);
        return $this;
    }

    public function fetch() {
        $resultSet = $this->tableGateway->selectWith($this->query);
        //var_dump($resultSet);die;
        //echo $this->query->getSqlString();die;
        return $resultSet;
    }

    public function optionValues() {
         $this->query->join(array('ov' => 'pr_option_value'),
            'ov.option_value_id = pr_product_option_value.option_value_id', '*');
        return $this;
   }

    public function productOptions() {
         $this->query->join(array('po' => 'pr_product_option'),
            'po.product_option_id = pr_product_option_value.product_option_id', '*');
        return $this;
    }

    public function delete($optionId) {
        $id = (int) $optionId;
        return $this->tableGateway->delete(array('product_option_value_id' => $id));
    }
}
