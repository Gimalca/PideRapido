<?php

namespace Catalog\Model\Dao;

/**
 * Description of ProductTable
 *
 * @author Pedro
 */

use Catalog\Model\Entity\Clasification;

use Catalog\Model\Entity\Option;
use Catalog\Model\Entity\OptionValue;
use Catalog\Model\Entity\Type;
use Zend\Db\TableGateway\TableGateway;

class OptionDao {

    protected $tableGateway;
    public $user_id;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function saveOption(Option $option) {
//        print_r($option);die;
        $dataOption = $option->getArrayCopy();
        //echo $id;die;
        $contador = 0;
        foreach ($dataOption['name'] as $options) {
            $insertOptions = array(
                'name' => $dataOption['name'][$contador],
                'type_id' => $dataOption['type_id'][$contador],
                'clasification_id' => $dataOption['clasification_id'][$contador],
                'sort_order' => $dataOption['sort_order'][$contador],
                'status' => 1
            );
            $contador++;
//            var_dump($insertOptions);die;
            $insert = $this->tableGateway->insert($insertOptions);
        }
        return $insert;
    }

    public function saveType(Type $type) {
//        print_r($type);die;
        $dataType = $type->getArrayCopy();
//        var_dump($dataType);die;
        //echo $id;die;
        $contador = 0;
        foreach ($dataType['name'] as $types) {
            $insertOptions = array(
                'name' => $dataType['name'][$contador],
            );
            $contador++;
//            var_dump($insertOptions);die;
            $insert = $this->tableGateway->insert($insertOptions);
        }
        return $insert;
    }
    public function saveOptionValue(OptionValue $optionValue) {
//        var_dump($optionValue);die;
        $dataOptionValue = $optionValue->getArrayCopy();
        $id = $dataOptionValue['option_id'];
//        var_dump($dataOptionValue['option_id']);die;
        //echo $id;die;
        $contador = 0;
        foreach ($dataOptionValue['name'] as $optionValues) {
            $insertOptions = array(
                'option_id' => $id,
                'name' => $dataOptionValue['name'][$contador],
                'sort_order' => $dataOptionValue['sort_order'][$contador],
            );
            $contador++;
            $insert = $this->tableGateway->insert($insertOptions);
        }
        return $insert;
    }

    public function saveClasification(Clasification $clasification) {
//        print_r($type);die;
        $dataClasification = $clasification->getArrayCopy();
//        var_dump($dataClasification);die;
        //echo $id;die;
        $contador = 0;
        foreach ($dataClasification['name'] as $clasification) {
            $insertOptions = array(
                'name' => $dataClasification['name'][$contador],
            );
            $contador = $contador + 1;
//            var_dump($insertOptions);
            $insert = $this->tableGateway->insert($insertOptions);
        }
        return $insert;
    }

    public function getAllOption() {
        $query = $this->tableGateway->getSql()->select();

        $query->join(array('ot' => 'pr_option_type'),
                    'pr_option.type_id = ot.type_id',
                    array('name_type' => 'name')
            );
        $query->join(array('oc' => 'pr_option_clasification'),
                    'pr_option.clasification_id = oc.clasification_id',
                    array('name_clasification' => 'name')
            );

        $query->where(['pr_option.status' => 1]);
        $query->order("pr_option.sort_order ASC");
         

        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet->toArray());die;

        return $resultSet;
    }
    public function updateStatusOption() {
        $query = $this->tableGateway->getSql()->select();

        $query->join(array('ot' => 'pr_option_type'),
                    'pr_option.type_id = ot.type_id',
                    array('name_type' => 'name')
            );
        $query->join(array('oc' => 'pr_option_clasification'),
                    'pr_option.clasification_id = oc.clasification_id',
                    array('name_clasification' => 'name')
            );


        $query->order("pr_option.sort_order ASC");
//        echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet->toArray());die;

        return $resultSet;
    }

    public function getById($id) {
        $id = (int) $id;
        if (!$id || !is_numeric($id)) return;

        $resultSet = $this->tableGateway->select([
            'option_id' => $id
        ]);
        return $resultSet->current();
    }

    public function getOptionValue($productOptionId = Null) {

        $query = $this->tableGateway->getSql()->select();

        $query->join(array('ov' => 'pr_option_value'),
                    'pr_product_option.option_id = ov.option_id ',
                    array('*')
            );

        $query->where(array('product_option_id' => $productOptionId));
        //$query->order("sort_order DESC");
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet->toArray());die;

        return $resultSet;
    }



    public function deleteOption( $optionId)
    {
       $id = (int) $optionId;
       return $this->tableGateway->delete(array('option_id' => $id));

    }

    public function getAllType() {
        $query = $this->tableGateway->getSql()->select();
        $query->order("type_id ASC");
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet->toArray());die;

        return $resultSet;
    }
    public function getAllClasification() {
        $query = $this->tableGateway->getSql()->select();
        $query->order("clasification_id ASC");
        //echo $query->getSqlString();die;

        $resultSet = $this->tableGateway->selectWith($query);
//        var_dump($resultSet->toArray());die;

        return $resultSet;
    }


    //put your code here
}
