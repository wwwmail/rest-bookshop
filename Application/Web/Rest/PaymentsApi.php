<?php

namespace Application\Web\Rest;

use Application\Web\{
    Request,
    Response,
    Received
};
use \Application\Entity\Payments;
use Application\Database\{
    Connection,
    CartService,
    UsersService,
    PaymentsService
};
use \Application\Helper\AppHelper;

class PaymentsApi extends AbstractApi {

    const ERROR = 'ERROR';
    const ERROR_NOT_FOUND = 'ERROR: Not Found';
    const _TRUE = 'true';
    const _FALSE = 'false';
    const ID_FIELD = 'id'; // field name of primary key
    protected $service;
    protected $helper;

    public function __construct($registeredKeys, $dbparams, $tokenField = NULL)
    {
        parent::__construct($registeredKeys, $tokenField);
        $this->service = new PaymentsService(
                new Connection($dbparams));
        
        $this->helper = new AppHelper('UsersService', $dbparams);
    }

    public function get(Request $request, Response $response)
    {
        $id = $response->getData() ?? 0;

        $user = $this->helper->getAuthUser();
        if ($id > 0) {
            $result = $this->service->
                    fetchById($id); //->entityToArray();
        } else {

            $result = [];

            $userId = $this->helper->getAuthUserId();

            $fetch = $this->service->fetchAll($userId);

            foreach ($fetch as $row) {
                $result[] = $row;
            }
        }

        if ($result) {

            $response->setData($result);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR_NOT_FOUND]);
            $response->setStatus(Request::STATUS_500);
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
                //'auth' => $token,
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
        $reqData = $request->getData();

        $bookId = $reqData['book_id'];

        $data['id_user'] = $this->helper->getAuthUserId();

        if (!$this->service->checkCart($data['id_user'], $bookId)) {

            $cartData = array_merge($reqData, $data);
            $cart = Cart::arrayToEntity($cartData, new Cart());

            if ($this->service->save($cart)) {
                $response->setData(['success' => self::_TRUE,
                    'message' => 'add to cart successfully'
                ]);
                $response->setStatus(Request::STATUS_200);
            } else {
                $response->setData([self::ERROR]);
                $response->setStatus(Request::STATUS_500);
            }
        } else {
            $response->setData(['success' => self::_FALSE,
                'message' => 'book Already added'
            ]);
            $response->setStatus(Request::STATUS_200);
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
