<?php
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(E_ALL);

$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');

require_once "../.config.php";
require_once "../services/Batch.php";
$batchObj = new Batch($conn);

$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');

switch ($method){
    case 'GET':
        if (isset($_GET["question_id"])){
            $batch = $batchObj->existsActive($_GET["question_id"]);
            if (!empty($batch)) {
                echo json_encode($batch);
            } else {
                http_response_code(404);
            }
        } else
            http_response_code(400);
        break;

    case 'POST':
        if (isset($_POST["question_id"])){
            $response = $batchObj->add($_POST["question_id"]);
            if ($response)
                http_response_code(201);
            else
                http_response_code(404);
        } else
            http_response_code(400);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data["id"])){
            $response = $batchObj->backup($data["id"]);
        }else if (isset($data["question_id"])){
            $response = $batchObj->backupQuestion($data["question_id"]);
        }else{
            http_response_code(400);
            break;
        }
        if (!$response)
            http_response_code(400);
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data["id"])){
            $response = $batchObj->delete($data["id"]);
        }else if (isset($data["question_id"])){
            $response = $batchObj->backupQuestion($data["question_id"]);
        }else{
            http_response_code(400);
            break;
        }
        if (!$response)
            http_response_code(400);
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Invalid request method {'.$method.'}.']);
        break;

}