<?php
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(E_ALL);


require_once('.config.php');
require_once('SubjectService.php');


$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');

switch ($method){
    case 'GET':
        break;

//    case 'POST':
//        break;
//
//    case 'PUT':
//        break;
//
//    case 'DELETE':
//        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Invalid request method {'.$method.'}.']);
        break;

}