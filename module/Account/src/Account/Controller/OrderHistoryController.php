<?php namespace Account\Controller;

use Sale\Model\Dao\OrderDao;
use Sale\Model\Entity\Order;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class OrderHistoryController extends AbstractActionController {

    public function indexAction() {

        $sm = $this->getServiceLocator();

        $auth = $sm->get('Account/Model/LoginAccount');
        $user = $auth->getIdentity();

        $orderTableGateway = $sm->get('OrderHydratingTableGateway');
        $orderDao = new OrderDao($orderTableGateway);

        $orders = $orderDao->getUserOrders($user->customer_id);
        
        return new ViewModel([
            'orders' => $orders
        ]);
    }

    public function detailAction() {

        $id = (int) $this->params()->fromRoute('id', 0);
        $sm = $this->getServiceLocator();

        $orderTableGateway = $sm->get('OrderHydratingTableGateway');

        $auth = $sm->get('Account/Model/LoginAccount');
        $user = $auth->getIdentity();

        $orderDao     = new OrderDao($orderTableGateway);
        $order        = $orderDao->getCustomerOrder($user, $id)->current();

        if ($order) {

            $orderProducts = $orderDao->getOrderProducts($order->order_id);
            //print_r($order->current());die;
            $total         = $orderDao->orderTotal($order->order_id);

            return new ViewModel([
                'products' => $orderProducts,
                'order' => $order,
                'total' => $total->current()
            ]);

        } else {
            return $this->redirect()->toRoute('account', array(
                'controller' => 'OrderHistory',
                'action' => 'index'
            ));
        }
    }
    
    public function statusOrderAction()
    {

        $id = (int) $this->params()->fromRoute('id', 0);

        $response = $this->getResponse();

        if ($id == 0) {
            $response->setStatusCode(400);
            $response->setContent(Json::encode(array('id' => 0)));
            return $response;
        }

        $sm = $this->getServiceLocator();
        $orderTableGateway = $sm->get('OrderHydratingTableGateway');
        $orderDao = new OrderDao($orderTableGateway);

        $update = $orderDao->updateOrder(['order_id' => $id, 'order_status' => '0', 'comment' => 'archivado']);

        if ($update) {

            $response->setStatusCode(200);
            $response->setContent(Json::encode(array(
                        'response' => $update,
                        'status' => 4,
                        'statusName' => 'archivado'
                            )
            ));
        } else {

            $response->setStatusCode(400);
            $response->setContent(Json::encode(array('response' => $update)));
        }

        return $response;
    }

}
