<?php
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(E_ALL);

$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');

require_once "../.config.php";
require_once "../services/Question.php";
$questionObj = new Question($conn);

function checkYN($value)
{
    if (isset($_POST[$value])) {
        switch ($_POST[$value]){
            case True:
                $_POST[$value] = 'Y';
                break;
            case False:
                $_POST[$value] = 'N';
                break;
            case 'N':
            case 'Y':
                break;

            default:
                http_response_code(400);
                echo json_encode(['message' => 'Invalid '.$value.' value, use Y or N.']);
                break;

        }
    }
}



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
//        } else if (isset($_GET["active"])) {
//            if ($_GET["active"] == "Y") {
//
//            }else if ($_GET["active"] == "N"){
//
//            }else{
//                http_response_code(400);
//                echo json_encode(['message' => 'Invalid is_active value, use Y or N.']);
//                break;
//            }
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
            echo json_encode(['message' => 'Invalid question type.']);
            break;
        }

        if (!isset($_POST['subject_id'])) $_POST['subject_id'] = null;
        if (!isset($_POST['owner_id'])) $_POST['owner_id'] = null;
        if (!isset($_POST['closed_at'])) $_POST['closed_at'] = null;
        checkYN('is_active');
        checkYN('word_cloud');
        checkYN('many_answers');
        if (!isset($_POST['is_active'])) $_POST['is_active'] = 'Y';
        if (!isset($_POST['word_cloud'])) $_POST['word_cloud'] = 'N';
        if (!isset($_POST['many_answers'])) $_POST['many_answers'] = 'N';

        $response = $questionObj->add($_POST['subject_id'], $_POST['owner_id'],
            $_POST['text'], $_POST['type'], $_POST['closed_at'], $_POST['is_active'],
            $_POST['word_cloud'], $_POST['many_answers']);
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
            echo json_encode(['message' => 'Question id is missing.']);
            break;
        }
        if (!isset($data["close"]) && !isset($data["owner_id"]) &&
            !isset($data["text"]) && !isset($data["type"]) && !isset($data["closed_at"])){
            http_response_code(400);
            echo json_encode(['message' => 'Nothing to update.']);
            break;
        }

        if (isset($data["close"]) && strtolower($data["close"]) != "false" || isset($data["closed_at"])) {
            if (!isset($data["closed_at"])) $data["closed_at"] = "null";
            $response = $questionObj->close($data["id"],$data["closed_at"]);
            if (!$response)
                http_response_code(400);
        }
        if (isset($data["owner_id"])) {
            $response = $questionObj->changeOwner($data["id"], $data["owner_id"]);
            if (!$response)
                http_response_code(400);
        }
        if (isset($data["text"]) || isset($data["type"])) {
            if (!isset($data["text"])) $data["text"] = "";
            if (!isset($data["type"])) $data["type"] = "";

            switch ($data["type"]){
                case "OPEN":
                case "CLOSED":
                case "":
                    break;
                default:
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid type value, use OPEN or CLOSED.']);
                    return;
            }

            $response = $questionObj->update($data["id"], $data["text"], $data["type"]);
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
    default:
    http_response_code(405);
    echo json_encode(['message' => 'Invalid request method']);
    break;
}

