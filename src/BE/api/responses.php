<?php
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(E_ALL);

$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');

require_once "../.config.php";
require_once "../services/Response.php";
require_once "../services/Batch.php";
$responseObj = new Response($conn);
$batchObj = new Batch($conn);


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
        if (!isset($_POST["batch_id"]) && !isset($_POST["question_id"])){
            http_response_code(400);
            echo json_encode(['message' => 'Missing batch id or question_id.']);
            break;
        }
        if (!isset($_POST["answer"])){
            http_response_code(400);
            echo json_encode(['message' => 'Missing answer.']);
            break;
        }

        if (!isset($_POST["batch_id"]))
            $_POST["batch_id"] = $batchObj->existsActive($_POST["question_id"]);
        if ($_POST["batch_id"] == null)
            $_POST["batch_id"] = $batchObj->add($_POST["question_id"], "New batch")["id"];

        if (is_array($_POST['answer'])) {
            $response = [];
            foreach ($_POST['answer'] as $answer) {
                $r = $responseObj->add($_POST['batch_id'], $answer);
                $response[] = $r;
            }
        } else {
            $response = $responseObj->add($_POST['batch_id'], $_POST['answer']);

        }

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
            echo json_encode(['message' => 'Response id is missing.']);
            break;
        }
        if(!isset($data["action"])){
            http_response_code(400);
            echo json_encode(['message' => 'Action is missing.']);
            break;
        }
        switch ($data["action"]){
            case "vote":
                if (is_array($data["id"])) {
                    foreach ($data["id"] as $id) {
                        $response = $responseObj->vote($id);
                    }
                } else {
                    $response = $responseObj->vote($data["id"]);

                }
                break;
            case "update":
                if (!isset($data["answer"])) {
                    http_response_code(400);
                    echo json_encode(['message' => 'Answer text is missing.']);
                    break;
                }
                $response = $responseObj->update($data["id"], $data["answer"]);
                break;
            case "zero":
            case "zero_out":
                if (is_array($data["id"])) {
                    foreach ($data["id"] as $id) {
                        $response = $responseObj->zero($id);
                    }
                } else {
                    $response = $responseObj->zero($data["id"]);
                }
                break;
            default:
                http_response_code(400);
                echo json_encode(['message' => 'Invalid action.']);
                break;
        }

        if (!$response)
            http_response_code(400);
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
    default:
    http_response_code(405);
    echo json_encode(['message' => 'Invalid request method']);
    break;

}