<?php

namespace Application\Web\Rest;

use Application\Web\{
    Request,
    Response
};
use \Application\Entity\Orders;
use \Application\Entity\Cart;
use \Application\Entity\OrderDetail;
use Application\Database\{
    Connection,
    CartService,
    OrdersService,
    BooksService,
    OrderDetailService
};
use \Application\Helper\AppHelper;

class OrdersApi extends AbstractApi {

    const ERROR = 'ERROR';
    const ERROR_NOT_FOUND = 'ERROR: Not Found';
    const _TRUE = 'true';
    const _FALSE = 'false';
    const ID_FIELD = 'id'; // field name of primary key
    public $lastInserId = 0;
    protected $service;
    protected $helper;
    protected $books;
    protected $cart;

    public function __construct($registeredKeys, $dbparams, $tokenField = NULL)
    {

        parent::__construct($registeredKeys, $tokenField);
        $this->service = new OrdersService(
                new Connection($dbparams));

        $this->books = new BooksService(
                new Connection($dbparams));


        $this->helper = new AppHelper('UsersService', $dbparams);

        $this->orderDetail = new OrderDetailService(new Connection($dbparams));
        $this->cart = new CartService(new Connection($dbparams));
    }

    public function get(Request $request, Response $response)
    {
        $id = $response->getData() ?? 0;

        if ($id > 0) {
            $result = $this->service->
                    fetchById($id); //->entityToArray();
        } else {
            $result = [];
            $fetch = $this->service->fetchAll();

            foreach ($fetch as $row) {
                $result[] = $row;
            }
        }

        if ($result) {

            $response->setData($result);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR_NOT_FOUND]);
            $response->setStatus(Request::STATUS_200);
        }

    }

    public function put(Request $request, Response $response)
    {

        $data = json_decode(file_get_contents('php://input'), true);
        if ($data['book_id']) {
            $data['id_user'] = $this->helper->getAuthUserId();

            $cartItem = $this->service->checkCart($data['id_user'], $data['book_id']);

            $cartItem->setCount($data['count']);
        } else {
            $cartItem = Orders::arrayToEntity($data['data'], new Orders());
        }

        if ($this->service->save($cartItem)) {
            $response->setData(['success' => self::_TRUE,
                'message' => 'succes update count book'
            ]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR]);
            $response->setStatus(Request::STATUS_500);
        }
    }

    public function post(Request $request, Response $response)
    {
        $data = array();
        $reqData = $request->getData();


        $data['user_id'] = $this->helper->getAuthUserId();
        $data['payment_id'] = $reqData['payment'];

        $total = array_reduce($reqData['books'], function ($acc, $u) {

            $acc += ( (int) $u['price'] - ( (int) $u['price'] * ((int) $u['discount'] / 100)) ) * $u['count'];
            return $acc;
        }, 0);

        $user = $this->helper->getAuthUser();

        $totalOrder = round((int) $total - ((int) $total * ((int) $user->getDiscount() / 100)), 0, PHP_ROUND_HALF_DOWN);

        $data['total_order'] = $totalOrder;

        $orderData = $data;
        $order = Orders::arrayToEntity($orderData, new Orders());

        if ($this->service->save($order)) {

            $orderId = $this->service->lastInserId;

            $obj = array();
            foreach ($reqData['books'] as $item) {
                $obj['order_id'] = $orderId;
                $obj['book_id'] = $item['bookId'];
                $obj['book_price'] = $item['price'];
                $obj['title_book'] = $item['title'];
                $obj['count'] = $item['count'];

                $object = OrderDetail::arrayToEntity($obj, new OrderDetail());
                $this->orderDetail->save($object);
            }

            $carts = $this->cart->getByUserId($user->getId());

            foreach ($carts as $item) {

                $object = Cart::arrayToEntity($item, new Cart());
            }

            $this->cart->removeByUserId($user->getId());

            $response->setData(['success' => self::_TRUE, 'message' => 'succes create order!']);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR]);
            $response->setStatus(Request::STATUS_200);
        }

    }

    public function delete(Request $request, Response $response)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $response->getData() ?? 0;
        if ($data['book_id']) {
            $data['id_user'] = $this->helper->getAuthUserId();

            $cartItem = $this->service->checkCart($data['id_user'], $data['book_id']);
        } else {
            $cartItem = Orders::arrayToEntity($data['data'], new Orders());
        }

        if ($cartItem && $this->service->remove($cartItem)) {
            $response->setData(['success' => self::SUCCESS_DELETE]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR_NOT_FOUND]);
            $response->setStatus(Request::STATUS_500);
        }
    }



}
