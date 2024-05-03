<?php
//session_start();
//if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
//
//    echo "Prihlaseny";
//}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<!--    <iframe src="https://node18.webte.fei.stuba.sk/ZaverecneZadanie/src/register.html" width="800" height="600" title="Embedded Website"></iframe>-->
<script>
    function getCookie(name) {
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            const cookie = cookies[i].trim();
            if (cookie.startsWith(name + '=')) {
                return cookie.substring(name.length + 1);
            }
        }
        return null;
    }

    // Example usage:
    const loggedin = getCookie('loggedin');
</script>
</body>
</html>