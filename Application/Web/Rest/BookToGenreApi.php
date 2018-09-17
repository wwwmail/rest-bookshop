<?php

namespace Application\Web\Rest;

use Application\Web\{
    Request,
    Response
};
use Application\Helper\AppHelper;
use \Application\Entity\Authors;
use Application\Database\{
    Connection,
    BookToGenreService
};

class BookToGenreApi extends AbstractApi {

    const ERROR = 'ERROR';
    const _TRUE = 'true';
    const _FALSE = 'false';
    const ERROR_NOT_FOUND = 'ERROR: Not Found';
    const ID_FIELD = 'id'; // field name of primary key
    
    protected $service;
    protected $helper;

    public function __construct($registeredKeys, $dbparams, $tokenField = NULL)
    {

        $this->service = new BookToGenreService(new Connection($dbparams));
        $this->helper = new AppHelper('UsersService', $dbparams);
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

        $obj = Authors::arrayToEntity($data, new Authors());

        if ($obj = $this->service->save($obj)) {
            $response->setData(['success' => self::SUCCESS_UPDATE,
                'message' => 'success updated']);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR]);
            $response->setStatus(Request::STATUS_200);
        }
    }

    public function post(Request $request, Response $response)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $book_id = $data['book_id'];
        $array2 = $this->service->getGenresByBookId($data['book_id']);

        $array1 = $data['data'];

        $diff = $this->helper->arrayDiffRecursive($array1, $array2, true);

        if ($this->service->addGenres($book_id, $diff['new'])) {
            $response->setData(['success' => self::_TRUE]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR]);
            $response->setStatus(Request::STATUS_200);
        }
    }

    public function delete(Request $request, Response $response)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $this->service->deleteGenreForBook($data['book_id'], $data['author_id']);

        if ($this->service->deleteGenreForBook($data['book_id'], $data['author_id'])) {
            $response->setData(['success' => self::_TRUE,
                'message' => 'success delete']);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR_NOT_FOUND]);
            $response->setStatus(Request::STATUS_200);
        }
    }

}
