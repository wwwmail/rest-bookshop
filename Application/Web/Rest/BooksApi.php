<?php

namespace Application\Web\Rest;

use Application\Web\{
    Request,
    Response
};
use Application\Entity\SimpleBook;
use Application\Database\{
    Connection,
    BooksService
};

class BooksApi extends AbstractApi {

    const ERROR = 'ERROR';
    const ERROR_NOT_FOUND = 'ERROR: Not Found';
    const _TRUE = 'true';
    const _FALSE = 'false';
    const ID_FIELD = 'id'; // field name of primary key

    protected $service;

    public function __construct($registeredKeys, $dbparams, $tokenField = NULL)
    {
        parent::__construct($registeredKeys, $tokenField);
        $this->service = new BooksService(
                new Connection($dbparams));
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
                        fetchById($id); 
            } else {

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

        $book = SimpleBook::arrayToEntity($data['data'], new SimpleBook());


        if ($newCust = $this->service->save($book)) {
            $response->setData(['success' => self::SUCCESS_UPDATE]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR]);
            $response->setStatus(Request::STATUS_500);
        }
    }

    public function post(Request $request, Response $response)
    {

        $data = json_decode(file_get_contents('php://input'), true);

        $book = SimpleBook::arrayToEntity($data['data'], new SimpleBook());
        if ($this->service->save($book)) {


            $response->setData(['success' => self::_TRUE,
                'id' => $this->service->lastInsertId]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR]);
            $response->setStatus(Request::STATUS_500);
        }
    }

    public function delete(Request $request, Response $response)
    {
        $id = $request->getDataByKey(self::ID_FIELD) ?? 0;

        $obj = $this->service->fetchById($id);
        if ($obj && $this->service->remove($obj)) {
            $response->setData(['success' => self::SUCCESS_DELETE,
                'id' => $id]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR_NOT_FOUND]);
            $response->setStatus(Request::STATUS_500);
        }
    }

}
