<?php
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(E_ALL);

$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');

require_once "../.config.php";
require_once "../services/Question.php";
require_once "../services/Batch.php";
require_once "../services/Response.php";
$questionObj = new Question($conn);
$batchObj = new Batch($conn);
$responseObj = new Response($conn);

switch ($method){
    case 'GET':
        if (!isset($_GET["qrcode"])){
            http_response_code(400);
            echo json_encode(['message' => 'No qrcode']);
            break;
        }

        $question = $questionObj->getByQRCode($_GET["qrcode"]);
//        echo json_encode(['question' => $question]);
        $responses = [];
        if ($question["type"] == "CLOSED"){
            $batch_id = $batchObj->existsActive($question["id"]);
            if ($batch_id != null){
                $responses = $responseObj->getBatch($batch_id);
                if (empty($responses)){
                    http_response_code(404);
                    break;
                }
            }
        }
        echo json_encode(['question' => $question, 'responses' => $responses]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Invalid request method']);
        break;
}

