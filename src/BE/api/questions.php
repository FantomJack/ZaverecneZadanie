<?php
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(E_ALL);

$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');

require_once "../.config.php";
require_once "../services/Question.php";
$questionObj = new Question($conn);

switch ($method) {
    case 'GET':
        if (isset($_GET["qrcode"])) {
            $questions = $questionObj->getByQRCode($_GET["qrcode"]);
        } else if (isset($_GET["owner_id"])) {
            $questions = $questionObj->getByOwner($_GET["owner_id"]);
        } else if (isset($_GET["subject_id"])) {
            $questions = $questionObj->getBySubject($_GET["subject_id"]);
        } else if (isset($_GET["id"])) {
            $questions = $questionObj->getByID($_GET["id"]);
        } else {
            $questions = $questionObj->get();
        }

        if (!empty($questions)) {
            echo json_encode($questions);
        } else {
            http_response_code(404);
        }
        break;

    case 'POST':

        if (!isset($_POST['text']) || !isset($_POST['type'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Question text or type missing.']);
            break;
        }
        if ($_POST['type'] != "OPEN" && $_POST['type'] != "CLOSED") {
            http_response_code(400);
            echo json_encode(['message' => 'Question type unknown.']);
            break;
        }

        if (!isset($_POST['subject_id'])) $_POST['subject_id'] = null;
        if (!isset($_POST['owner_id'])) $_POST['owner_id'] = null;
        if (!isset($_POST['closed_at'])) $_POST['closed_at'] = null;
        if (isset($_POST['is_active'])) {
            if ($_POST['is_active'] === True) $_POST['is_active'] = 'Y';
            if ($_POST['is_active'] === False) $_POST['is_active'] = 'N';
        }
        if (!isset($_POST['is_active'])) $_POST['is_active'] = 'Y';

        $response = $questionObj->add($_POST['subject_id'], $_POST['owner_id'],
            $_POST['text'], $_POST['type'], $_POST['closed_at'], $_POST['is_active']);
        if ($response) {
            http_response_code(201);
        } else {
            http_response_code(404);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data["id"])) {
            http_response_code(400);
            echo json_encode(['message' => 'Question id is missing.']);
            break;
        }
        if (isset($data["close"])) {
            $response = $questionObj->close($data["id"]);
            if (!$response)
                http_response_code(400);
        }
        if (isset($data["owner_id"])) {
            $response = $questionObj->changeOwner($data["id"], $data["owner_id"]);
            if (!$response)
                http_response_code(400);
        }
        if (isset($data["text"]) || isset($data["type"]) || isset($data["closed_at"])) {
            if (!isset($data["text"])) $data["text"] = "";
            if (!isset($data["type"])) $data["type"] = "";
            if (!isset($data["closed_at"])) $data["closed_at"] = null;

            $response = $questionObj->update($data["id"], $data["text"], $data["type"], $data["closed_at"]);
            if (!$response)
                http_response_code(400);
        }

        break;
    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data["id"])){
            $response = $questionObj->delete($data["id"]);
        }else if(isset($data["qrcode"])){
            $response = $questionObj->deleteByQRCode($data["qrcode"]);

        }else{
            http_response_code(404);
            break;
        }
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

