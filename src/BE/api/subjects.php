<?php
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(E_ALL);


require_once "../.config.php";
require_once "../services/Subject.php";
$subjectObj = new Subject($conn);

$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');

switch ($method){
    case 'GET':
        if (isset($_GET["id"])) {
            $questions = $subjectObj->getByID($_GET["id"]);
        }elseif (isset($_GET["code"])){
            $questions = $subjectObj->getByCode($_GET["code"]);
        }else if(isset($_GET["name"])){
            $questions = $subjectObj->getByName($_GET["name"]);

        }else{
            $questions = $subjectObj->get();
        }

        if (!empty($questions)) {
            echo json_encode($questions);
        } else {
            http_response_code(204);
        }
        break;

    case 'POST':
        if (!isset($_POST['code']) || !isset($_POST['name'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Subject name or code missing.']);
            break;
        }
        $response = $subjectObj->add($_POST['code'], $_POST['name']);
        if (!empty($response)) {
            http_response_code(201);
            echo json_encode($response);
        } else {
            http_response_code(404);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data["id"])) {
            http_response_code(400);
            echo json_encode(['message' => 'Subject id is missing.']);
            break;
        }
        if (!isset($data['code']) || !isset($data['name'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Subject name or code missing.']);
            break;
        }
        $response = $subjectObj->update($data["id"], $data["code"], $data["name"]);
        if (!$response)
            http_response_code(400);
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data["id"])){
            http_response_code(404);
            break;
        }
        $response = $subjectObj->delete($data["id"]);
        if ($response)
            http_response_code(200);
        else
            http_response_code(400);
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Invalid request method {'.$method.'}.']);
        break;

}