<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization');

header('Content-Type: application/json');
header('Accept-Language: en');
header('Accept-Charset: utf-8');
// simple REST server
include __DIR__ .  '/config/db.config.php';
include __DIR__ .  '/config/app.config.php';
// setup class autoloading
require __DIR__ . '/Application/Autoload/Loader.php';

// add current directory to the path
Application\Autoload\Loader::init(__DIR__ . '/');

// classes to use
use Application\Web\Rest\Server;
use Application\Web\Rest\CustomerApi;
use Application\Web\Rest\UsersApi;

use Application\App;

$obj = new App($dbParams);

die;
//list($a, $d, $db, $table, $path) = explode('/', $this->url, 6);
//echo Api::generateToken();
//$apiKey = include __DIR__ . '/api_key.php';
//$server = new Server(new CustomerApi(['79e9b5211bbf2458a4085707ea378129'], $dbParams, 'id'));


