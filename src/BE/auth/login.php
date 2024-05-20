<?php
require_once '../.config.php';
require_once 'help_functions.php';
header('Content-Type: application/json');
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == "POST") {


    if (!isset($_POST['login']) || !isset($_POST['password'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Chýba login, email alebo heslo.']);
        return;
    }

    // Validacia username
    if (checkEmpty($_POST['login']) === true) {
        http_response_code(422);
        echo json_encode(['message' => 'Zadajte login.']);
        return;
    } elseif (checkUsername($_POST['login']) === false) {
        http_response_code(422);
        echo json_encode(['message' => 'Login moze obsahovat iba velke, male pismena, cislice a podtrznik.']);
        return;
    }

    // Validacia password
    if (checkEmpty($_POST['password']) === true) {
        http_response_code(422);
        echo json_encode(['message' => 'Zadajte heslo.']);
        return;
    }

    $param_login = trim($_POST['login']);
    $sql = "SELECT id, login, email, password FROM users WHERE login = '$param_login';";
    $stmt = $conn->query($sql);

    if ($stmt) {
        if ($stmt->num_rows == 1) {
            // Uzivatel existuje, skontroluj heslo.
            $row = $stmt->fetch_assoc();
            $hashed_password = $row["password"];
            if (password_verify($_POST['password'], $hashed_password)) {
                // Uloz data pouzivatela do session.
                session_start();
                $_SESSION["loggedin"] = true;
                $_SESSION["login"] = $row['login'];
                $_SESSION["email"] = $row['email'];
                $_SESSION["id"] = $row['id'];
                setcookie('loggedin', true, 0, "/");
                setcookie('login', $row['login'], 0, "/");
                setcookie('email', $row['email'], 0, "/");
                setcookie('id', $row['id'], 0, "/");
                http_response_code(200);
                return;

            } else {
                http_response_code(401);
                echo json_encode(['message' => 'Nespravne meno alebo heslo.']);
                return;
            }
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Nespravne meno alebo heslo.']);
            return;
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Ups. Nieco sa pokazilo!']);
        return;
    }

    unset($stmt);


}