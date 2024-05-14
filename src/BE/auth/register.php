<?php
// Konfiguracia PDO
require_once '../.config.php';
require_once 'help_functions.php';
header('Content-Type: application/json');

function userExists($conn, $login, $email) {
    // Funkcia pre kontrolu, ci pouzivatel s "login" alebo "email" existuje.
    $exist = false;

    $param_login = trim($login);
    $param_email = trim($email);

    $sql = "SELECT id FROM users WHERE login = '$param_login' OR email = '$param_email';";
    $stmt = $conn->query($sql);

    if ($stmt->num_rows >= 1) {
        $exist = true;
    }

    unset($stmt);

    return $exist;
}

function insertData($conn, $email, $login, $password)
{
    $sql = "INSERT INTO users (login, email, password) VALUES (?,?,?)";

    $hashed_password = password_hash($password, PASSWORD_ARGON2ID);

    // Bind parametrov do SQL
    $stmt =  $conn->prepare($sql);

    $stmt->bind_param("sss", $login, $email, $hashed_password);
    $stmt->execute();
    return mysqli_insert_id($conn);
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['login']) || !isset($_POST['email']) || !isset($_POST['password'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Chýba login, email alebo heslo.']);
        return;
    }
    // Validacia username
    if (empty(trim($_POST['login']))) {
        http_response_code(422);
        echo json_encode(['message' => 'Zadajte login.']);
        return;

    } elseif (checkLength($_POST['login'], 6,32) === false) {
        http_response_code(422);
        echo json_encode(['message' => 'Login musí mať min. 6 a max. 32 znakov.']);
        return;

    } elseif (checkUsername($_POST['login']) === false) {
        http_response_code(422);
        echo json_encode(['message' => 'Login moze obsahovat iba velke, male pismena, cislice a podtrznik.']);
        return;
    }

    // Kontrola pouzivatela
    if (userExists($conn, $_POST['login'], $_POST['email']) === true) {
        http_response_code(409);
        echo json_encode(['message' => 'Pouzivatel s tymto e-mailom / loginom uz existuje.']);
        return;
    }

    $response = insertData($conn, $_POST['email'], $_POST['login'], $_POST['password']);
    if ($response) {
        session_start();
        $_SESSION["loggedin"] = true;
        $_SESSION["login"] = $_POST['login'];
        $_SESSION["email"] = $_POST['email'];
        $_SESSION["id"] = $response;
        setcookie('loggedin', true, 0, "/");
        setcookie('login', $_POST['login'], 0, "/");
        setcookie('email', $_POST['email'], 0, "/");
        setcookie('id', $response, 0, "/");
        http_response_code(201);
    }else {
        http_response_code(404);
    }
}