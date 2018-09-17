<?php

namespace Application\Web\Rest;

use Application\Web\{
    Request,
    Response,
    Received
};
use Application\Entity\Authors;
use Application\Database\{
    Connection,
    AuthorsService
};

class AuthorsApi extends AbstractApi {

    const ERROR = 'ERROR';
    const ERROR_NOT_FOUND = 'ERROR: Not Found';
    const _TRUE = 'true';
    const _FALSE = 'false';
    const ID_FIELD = 'id'; // field name of primary key

    protected $service;

    public function __construct($registeredKeys, $dbparams)
    {
        parent::__construct($registeredKeys, $tokenField);
        $this->service = new AuthorsService(new Connection($dbparams));
    }

    public function get(Request $request, Response $response)
    {
        if (method_exists($this->service, $request->getFilter()) && !empty($request->getFilterData())) {

            $filter = $request->getFilter();

            $result = $this->service->$filter($request->getFilterData());
        } else {

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
        $obj = Authors::arrayToEntity($data, new Authors());

        if ($obj = $this->service->save($obj)) {
            $response->setData(['success' => self::SUCCESS_UPDATE,
                'message' => 'success updated']);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR]);
            $response->setStatus(Request::STATUS_500);
        }
    }

    public function post(Request $request, Response $response)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $obj = Authors::arrayToEntity($data, new Authors());
        if ($this->service->save($obj)) {
            $response->setData(['success' => self::_TRUE,
                'id' => $obj->getId()]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR]);
            $response->setStatus(Request::STATUS_500);
        }
    }

    public function delete(Request $request, Response $response)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $obj = Authors::arrayToEntity($data, new Authors());
        if ($obj && $this->service->remove($obj)) {
            $response->setData(['success' => self::_TRUE,
                'message' => 'success delete']);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR_NOT_FOUND]);
            $response->setStatus(Request::STATUS_500);
        }
    }


}
