<?php

session_start();
setcookie('loggedin', false, time() - 3600, '/');
setcookie('login', false, time() - 3600, "/");
setcookie('email', false, time() - 3600, "/");
setcookie('id', false, time() - 3600, "/");
//setcookie('PHPSESSID', false, time() - 3600, "/");


if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_unset();
session_destroy();