<?php
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(E_ALL);

$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');

require_once "../.config.php";
require_once "../services/Response.php";
$responseObj = new Response($conn);


switch ($method) {
    case 'GET':
        if (isset($_GET["question_id"])) {
            $answers = $responseObj->getCurrentAnswers($_GET["question_id"]);
        }else if (isset($_GET["batch_id"])){
            $answers = $responseObj->getBatch($_GET["batch_id"]);
        }else if (isset($_GET["id"])){
            $answers = $responseObj->getByID($_GET["id"]);
        }else{
            $answers = $responseObj->get();
        }
        if (!empty($answers)) {
            echo json_encode($answers);
        } else {
            http_response_code(404);
        }
        break;
    case 'POST':
        if (!isset($_POST["batch_id"]) || !isset($_POST["answer"])){
            http_response_code(400);
            echo json_encode(['message' => 'Missing batch id or answer text.']);
            break;
        }
        $response = $responseObj->add($_POST['batch_id'], $_POST['answer']);
        if ($response) {
            http_response_code(201);
        } else {
            http_response_code(404);
        }
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        break;
    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data["id"]) && !isset($_GET["id"])){
            http_response_code(400);
            echo json_encode(['message' => 'Missing id.']);
            break;
        }
        if (!isset($data["id"])) $data["id"] = $_GET["id"];
        $response = $responseObj->delete($data["id"]);
        if ($response)
            http_response_code(200);
        else
            http_response_code(400);
        break;
        break;
    default:
    http_response_code(405);
    echo json_encode(['message' => 'Invalid request method']);
    break;

}