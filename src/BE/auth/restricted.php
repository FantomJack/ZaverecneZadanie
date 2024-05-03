<?php

session_start();

$login_type = '';

// Ak je pouzivatel prihlaseny, ziskam data zo session, pracujem s DB etc...
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {

    $login_type = 'oauth';
    $email = $_SESSION['email'];
    $fullname = $_SESSION['fullname'];
    $name = $_SESSION['name'];
    $surname = $_SESSION['surname'];
    $id = $_SESSION['id'];

} else if (isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === true){

    $login_type = '2fa';
    $email = $_SESSION['email'];
    $fullname = $_SESSION['fullname'];
    $login = $_SESSION['login'];
    $created_at = $_SESSION['created_at'];
}else {
    // Ak pouzivatel prihlaseny nie je, presmerujem ho na hl. stranku.
    header('Location: index.php');
    exit();
}

if ($login_type == 'oauth')
    echo $id.'</p><p>Meno: ' . $name.', Priezvisko: '.$surname.'</p>';
elseif ($login_type == '2fa')
    echo $login.'</p><p>Datum registracie/vytvonia konta: <?php echo $created_at?></p>';