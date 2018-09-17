<?php

namespace Application\Web\Rest;

use Application\Web\{
    Request,
    Response
};
use \Application\Entity\Orders;
use Application\Database\{
    Connection,
    BooksService,
    OrderDetailService
};
use \Application\Helper\AppHelper;

class OrderDetailApi extends AbstractApi {

    const ERROR = 'ERROR';
    const ERROR_NOT_FOUND = 'ERROR: Not Found';
    const _TRUE = 'true';
    const _FALSE = 'false';
    const ID_FIELD = 'id'; // field name of primary key
   
    public $lastInserId = 0;
    protected $service;
    protected $helper;
    protected $books;

    public function __construct($registeredKeys, $dbparams, $tokenField = NULL)
    {

        parent::__construct($registeredKeys, $tokenField);
        $this->service = new OrderDetailService(
                new Connection($dbparams));

        $this->books = new BooksService(
                new Connection($dbparams));

        $this->helper = new AppHelper('UsersService', $dbparams);
    }

    public function get(Request $request, Response $response)
    {

        if (method_exists($this->service, $request->getFilter()) && !empty($request->getFilterData())) {

            $filter = $request->getFilter();

            $result = [];

            $fetch = $this->service->$filter($request->getFilterData());

            foreach ($fetch as $row) {
                $result[] = $row;
            }
        } else {

            $id = $response->getData() ?? 0;

            if ($id > 0) {
                $result = $this->service->
                        fetchById($id); //->entityToArray();
            } else {

                $limit = $request->getDataByKey(self::LIMIT_FIELD) ?? self::DEFAULT_LIMIT;
                $offset = $request->getDataByKey(self::OFFSET_FIELD) ?? self::DEFAULT_OFFSET;
                $result = [];

                $fetch = $this->service->fetchAll();

                foreach ($fetch as $row) {
                    $result[] = $row;
                }
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

        $data['id_user'] = $this->helper->getAuthUserId();

        $cartItem = $this->service->checkCart($data['id_user'], $data['book_id']);

        $cartItem->setCount($data['count']);


        if ($this->service->save($cartItem)) {
            $response->setData(['success' => self::_TRUE,
                'message' => 'succes update count book'
            ]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR]);
            $response->setStatus(Request::STATUS_200);
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
        // if(!$this->service->checkCart($data['id_user'], $bookId)){
        $data['total_order'] = $totalOrder;

        $orderData = $data;
        $order = Orders::arrayToEntity($orderData, new Orders());

        if ($this->service->save($order)) {

            $user = $this->helper->getAuthUser();


            $response->setData([self::_TRUE]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR]);
            $response->setStatus(Request::STATUS_500);
        }

    }

    public function delete(Request $request, Response $response)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $data['id_user'] = $this->helper->getAuthUserId();


        $cartItem = $this->service->checkCart($data['id_user'], $data['book_id']);


        if ($cartItem && $this->service->remove($cartItem)) {
            $response->setData(['success' => self::SUCCESS_DELETE]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR_NOT_FOUND]);
            $response->setStatus(Request::STATUS_500);
        }
    }


}
