<?php

namespace Application\Web\Rest;

use Application\Web\{
    Request,
    Response,
    Received
};
use Application\Entity\Users;
use \Application\Entity\Genres;
use Application\Database\{
    Connection,
    GenresService
};

class GenresApi extends AbstractApi {

    const ERROR = 'ERROR';
    const ERROR_NOT_FOUND = 'ERROR: Not Found';
    const SUCCESS_UPDATE = 'SUCCESS: update succeeded';
    const _TRUE = 'true';
    const _FALSE = 'false';
    const SUCCESS_DELETE = 'SUCCESS: delete succeeded';
    const ID_FIELD = 'id'; // field name of primary key
    const TOKEN_FIELD = 'token'; // field used for authentication
    const LIMIT_FIELD = 'limit';
    const OFFSET_FIELD = 'offset';
    const DEFAULT_LIMIT = 20;
    const DEFAULT_OFFSET = 0;

    protected $service;

    public function __construct($registeredKeys, $dbparams, $tokenField = NULL)
    {
//        var_dump($tokenField);
//        var_dump($registeredKeys); die;
        parent::__construct($registeredKeys, $tokenField);
        $this->service = new GenresService( new Connection($dbparams));
    }

    public function get(Request $request, Response $response)
    {
        
        if(method_exists($this->service, $request->getFilter()) && !empty($request->getFilterData())){

            $filter = $request->getFilter();

            $result = $this->service->$filter($request->getFilterData());
            
        } else {
            
            $id = $response->getData() ?? 0;
        
        if ($id > 0) {
            $result = $this->service->
                            fetchById($id);//->entityToArray();
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
            $response->setStatus(Request::STATUS_500);
        }
    }

    public function put(Request $request, Response $response)
    {
        $data = json_decode(file_get_contents('php://input'), true);
         
       $obj = Genres::arrayToEntity($data, new Genres());
       
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
         
       $obj = Genres::arrayToEntity($data, new Genres());
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
       $obj = Genres::arrayToEntity($data, new Genres());
       
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
