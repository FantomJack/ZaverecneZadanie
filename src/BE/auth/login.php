<?php

session_start();
$method = $_SERVER['REQUEST_METHOD'];

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
//    header("location: index.html");
    exit;
}
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
require_once 'BE/.config.php';


$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);

// ------- Pomocne funkcie -------
function checkEmpty($field) {
    // Funkcia pre kontrolu, ci je premenna po orezani bielych znakov prazdna.
    // Metoda trim() oreze a odstrani medzery, tabulatory a ine "whitespaces".
    if (empty(trim($field))) {
        return true;
    }
    return false;
}

function checkUsername($username) {
    // Funkcia pre kontrolu, ci username obsahuje iba velke, male pismena, cisla a podtrznik.
    if (!preg_match('/^[a-zA-Z0-9_]+$/', trim($username))) {
        return false;
    }
    return true;
}


// ------- ------- ------- -------

if ($method == "POST") {

    $errmsg = "";

    // Validacia username
    if (checkEmpty($_POST['login']) === true) {
        $errmsg .= "<p>Zadajte login.</p>";
    } elseif (checkUsername($_POST['login']) === false) {
        $errmsg .= "<p>Login moze obsahovat iba velke, male pismena, cislice a podtrznik.</p>";
    }

// Validacia password
    if (checkEmpty($_POST['password']) === true) {
        $errmsg .= "<p>Zadajte heslo.</p>";
    }

// TODO: Skontrolovat ci login a password su zadane (podobne ako v register.php).

    if (empty($errmsg)) {
        $sql = "SELECT email, login, password, FROM users WHERE login = :login";

        $stmt = $conn->prepare($sql);

        // TODO: Upravit SQL tak, aby mohol pouzivatel pri logine zadat login aj email.
        $stmt->bind_param("s", $_POST["login"]);


        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                // Uzivatel existuje, skontroluj heslo.
                $row = $stmt->fetch();
                $hashed_password = $row["password"];

                if (password_verify($_POST['password'], $hashed_password)) {
                    // Uloz data pouzivatela do session.
                    $_SESSION["loggedin"] = true;
                    $_SESSION["login"] = $row['login'];
                    $_SESSION["email"] = $row['email'];

                    // Presmeruj pouzivatela na zabezpecenu stranku.
//                    header("location: index.html");

                } else {
                    echo "<p style='color: #dc3545'>Nespravne meno alebo heslo.</p>";
                }
            } else {
                echo "<p style='color: #dc3545'>Nespravne meno alebo heslo.</p>";
            }
        } else {
            echo "<p style='color: #dc3545'>Ups. Nieco sa pokazilo!</p>";
        }

        unset($stmt);
        unset($pdo);
    }
}

?>

<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Zaverecne zadanie</title>
</head>
<body>
<!--<header>-->
<hgroup>
    <h1 style="font-size: 2em">Login page</h1>
</hgroup>
<!--</header>-->
<main style="padding: 0" >

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

        <label for="login">
            Prihlasovacie meno:
            <input type="text" name="login" value="" id="login" required>
        </label>
        <label for="password">
            Heslo:
            <input type="password" name="password" value="" id="password" required>
        </label>
        <label for="2fa">
            2FA kod:
            <input type="number" name="2fa" value="" id="2fa" required>
        </label>

        <button type="submit">Prihlasit sa</button>
    </form>



    <p>Este nemate vytvorene konto? <a href="register.php">Registrujte sa tu.</a></p>

</main>
</body>
</html>