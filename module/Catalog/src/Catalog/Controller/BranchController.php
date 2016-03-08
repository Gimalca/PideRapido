<?php

namespace Catalog\Controller;

use Catalog\Form\BranchSearch;
use Catalog\Model\Dao\ProductDao;
use Catalog\Model\Dao\OptionsDao;
use Catalog\Model\Entity\ProductOptionValue;
use Catalog\Model\Entity\ProductOption;
use Catalog\Model\Entity\OptionValue;
use Franchise\Model\Dao\BranchDao;
use Franchise\Model\Dao\FranchiseDao;
use Catalog\Form\Product;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class BranchController extends AbstractActionController {

    public function indexAction() {
        return new ViewModel();
    }

    public function listAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id', 0);
        $branchTableGateway = $this->getService('BranchHydratingTableGateway');
        $branchDao = new BranchDao($branchTableGateway);


        $citys = $this->getCitySelect($id);
        $citys[0] = 'Todas';
        $BranchSearchForm = new BranchSearch();
        $BranchSearchForm->get('city_id')
                ->setValueOptions($citys)
                ->setValue(0);


        if($request->getPost('city_id') > 0){
            $branch = $branchDao->getByCity($id, $request->getPost('city_id'))->buffer();
        }else{
           $branch = $branchDao->getAll($id)->buffer();
        }


        $franchiseDao = new FranchiseDao($this->getService('FranchiseTableGateway'));
        $franchise = $franchiseDao->getById($id);

        $view['branch_id'] = $id;
        $view['franchise'] = $franchise;
        $view['branch'] = $branch;
        $view['form'] = $BranchSearchForm;

        //print_r($branch);die;
        return new ViewModel($view);
    }

    public function detailAction() {

        $request = $this->getRequest();
        $branchId = (int) $this->params()->fromRoute('id', 0);
        $branchTableGateway = $this->getService('BranchTableGateway');
        $branchDao = new BranchDao($branchTableGateway);
        $branch = $branchDao->getBranchFull($branchId);
        //var_dump($branch);die;
        
        if($branch->status == 0){ 
             return $this->redirect()->toRoute('catalog', array(
                                       'controller' => 'branch',
                                       'action' => 'list',
                                       'id' => $branch->franchise_id,

                           ));
        }
        
        $productTableGateway = $this->getService('ProductHasBranchHydratingTableGateway');
        $productDao = new ProductDao($productTableGateway);
        //echo $branchId;die;
        $productsCombo = $productDao->getAll();
        $productsCombo->whithProduct();
        $productsCombo->where(array(
            'branch_id' => $branchId,
            'type' => '2',
            'status' => '1'
        ));
        $combos = $productsCombo->fetchAll();
        //var_dump($combos);die;
        $products = $productDao->getAll();
        $productsCombo->whithProduct();
        $products->where(array(
            'branch_id' => $branchId,
            'type' => '1',
            'status' => '1'
        ));
        $products = $products->fetchAll();
        $postres = $productDao->getAll();
        $productsCombo->whithProduct();
        $postres->where(array(
            'branch_id' => $branchId,
            'type' => '3',
            'status' => '1'
        ));
        $productsPostres = $postres->fetchAll();
        //var_dump($productsPostres);die;
        $view['branch'] = $branch;
        $view['combos'] = $combos;
        $view['products'] = $products;
        $view['postres'] = $productsPostres;
        //var_dump($combo);die;
        return new ViewModel($view);
    }

    public function productListAction() {
        return new ViewModel();
    }

    public function optionsAction() {

        $request = $this->getRequest();

        if($request->isXmlHttpRequest() && $request->isPost()) {

            $id  = (int) $this->params()->fromRoute('id', 0);

            $productDao = new ProductDao($this->getService('ProductHydratingTableGateway'));
            $product    = $productDao->getProductFromBranch($id)->current();

            $selected = [
                'extras'  => $this->params()->fromPost('extras', []),
                'general' => $this->params()->fromPost('general', [])
            ];

            $price = 0;
           // print_r($selected);die;
            if ($this->exists($selected['extras'])) {
                $extrasPrice = $this->getPrice(array_keys($selected['extras']));
                $price += $extrasPrice;
            }

            if ($this->exists($selected['general'])) {
                $generalPrice = $this->getPrice($selected['general']);
                $price += $generalPrice;
            }

            $response = $this->getResponse();

            $json = Json::encode(array(
                'price'    => round($product->price + $price, 2),
                'selected' => $selected
            ));

            $response->setContent($json);
            return $response;
        }
    }

    public function productAction() {

        $id  = (int) $this->params()->fromRoute('id', 0);
       //var_dump([$extrasParams, $generalParams]);die;

        $OPTION_TYPE = [
            'SELECTBOX' => 'general',
            'CHECKBOX'  => 'ingrediente'
        ];

        $productDao = new ProductDao($this->getService('ProductHydratingTableGateway'));

        $product    = $productDao->getProductFromBranch($id)->current();
        
        if ($product->status_branch == 0) {
            return $this->redirect()->toRoute('catalog', array(
                        'controller' => 'branch',
                        'action' => 'list',
                        'id' => $product->franchise_id,
            ));
        }
        //print_r($product);die;
        $options = $productDao->getAll(array('product_id'))
            ->whithOptions()
            ->where(array('pr_product.product_id' => $product->product_id, 'pr_product.status' => '1') )
            ->fetchAll()
            ->toArray();
        
        $view['price'] = $product->price;
        // clasificar las opciones por tipo
        $categoryOptions = array_reduce($options, function ($category, $option) {
            $category[$option['name_type']][] = $option;
            return $category;
        }, []);

        $productForm = new Product;

        $productForm
            ->get('product_has_branch_id')
            ->setValue($product->product_has_branch_id);

        //var_dump($options->toArray());die;
        if ($this->exists($categoryOptions[$OPTION_TYPE['SELECTBOX']])) {
            $generalOptions = $this->setGeneralOptions(
                $productForm,
                $product,
                $categoryOptions[$OPTION_TYPE['SELECTBOX']]
            );
            $view['generalOptions'] = $generalOptions;
        }

        if (@$this->exists($categoryOptions[$OPTION_TYPE['CHECKBOX']])) {
            $ingredients = $this->setIngredients(
                $productForm,
                $product,
                $categoryOptions[$OPTION_TYPE['CHECKBOX']]
            );
            $view['ingredients'] = $ingredients;
        }

        //var_dump($results->toArray());die;
        //print_r($product); die;
        $view['product'] = $product;
        $view['form'] = $productForm;
        return new ViewModel($view);
    }

    private function exists($property) {
        return (
            isset($property)
            && !is_null($property)
            && !empty($property)
        );
    }

    private function getPrice($options) {

        $total = $this->getOptionsDao(new ProductOptionValue)
            ->all()
            ->columns(array(
                'price' => new \Zend\Db\Sql\Expression('COALESCE(SUM(price),0)')
            ))
            ->where([
                'product_option_value_id' => $options
            ])
            ->fetch()
            ->current();

        return $total->price;
    }

     private function getCitySelect($id) {
        $cityDao = $this->getService('CityDao');
        $results = $cityDao->getAllCity($id);
        $result = array();
        foreach ($results as $bankR) {
            //$result[] = $row->getArrayCopy();
            $result[$bankR->city_id] = $bankR->name;
        }
        return $result;
    }

    public function setIngredients($form, $product, $options) {
        $optionsIngredients = [];
        forEach($options as $option) {

            //Posibles valores de las opciones
            //Valores de opciones para el product
            //ej :Chocolate + 50$
            $productOptionValueDao = $this->getOptionsDao(new ProductOptionValue);

            $productOptionValues = $productOptionValueDao->all()
                ->optionValues()
                ->productOptions()
                ->where([
                    'pr_product_option_value.status' => 1,
                    'ov.option_id'  => $option['option_id'],
                    'po.product_id' => $product->product_id
                ])
                ->fetch()
                ->toArray();

            //var_dump($productOptionValues);die;
            if (sizeof($productOptionValues) > 0) {
                $optionsIngredients[$option['option_id']]['option'] = $option;
                $optionsIngredients[$option['option_id']]['extras'] = $productOptionValues;
            }
        }
        //var_dump($optionsIngredients);die;
        return $optionsIngredients;
    }

    public function setGeneralOptions($form, $product, $options) {
        $names = [];

        forEach($options as $option) {

            //Posibles valores de las opciones
            //Valores de opciones para el product
            //ej :Grande + 50$
            //var_dump($selected);die;
            $productOptionValues = $this->getOptionsDao(new ProductOptionValue)
                ->all()
                ->optionValues()
                ->productOptions()
                ->where([
                    'pr_product_option_value.status' => 1,
                    'ov.option_id'  => $option['option_id'],
                    'po.product_id' => $product->product_id
                ])
                ->fetch()
                ->toArray();
            //print_r($productOptionValues);die;
            if (!empty($productOptionValues) && sizeof($productOptionValues) > 0) {

                $selectOptions = array_map(function($value) {
                    return [
                        'value' => $value['product_option_value_id'],
                        'label' => $value['name'] .' ('. round($value['price'], 2) . $value['price_prefix'] . ')',
                    ];
                }, $productOptionValues);

                array_push($names, "general[{$option['option_id']}]");

                $form->addSelectBox([
                    'name'        => "general[{$option['option_id']}]",
                    'label'       => $option['name_option'],
                    'placeholder' => $option['name_option'],
                    'required'    => $option['required']
                ], $selectOptions);
            }
        }
        return $names;
    }

    private function getOptionsDao($entity) {
        $resultSet = $this->getService('HydratingResultSet');
        return new OptionsDao($entity->tableGateway($resultSet));
    }

    public function getService($serviceName) {
        $sm = $this->getServiceLocator();
        $service = $sm->get($serviceName);
        return $service;
    }
}
