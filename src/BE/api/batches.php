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
        if (!empty($_GET["question_id"])){
            $batch = $batchObj->getByQuestion($_GET["question_id"]);
        } else
            $batch = $batchObj->get();

        if (!empty($batch))
            echo json_encode($batch);
        else
            http_response_code(404);
        break;
    case 'POST':

        if (!isset($_POST["question_id"]) || !isset($_POST["name"])){
            http_response_code(400);
            echo json_encode(['message' => 'Missing question id or name.']);
            break;
        }

        $response = $batchObj->add($_POST["question_id"], $_POST["name"]);
        if ($response)
            http_response_code(201);
        else
            http_response_code(404);

        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data["id"]) && isset($data["name"])) {
            $response = $batchObj->update($data["id"], $data["name"]);
        }else if (isset($data["id"])){
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