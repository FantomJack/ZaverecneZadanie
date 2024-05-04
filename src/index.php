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
    <script src=".config.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.3.4/axios.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.*/css/pico.min.css">
    <style>
        body{
            /*margin: 0;*/
            /*padding: 0;*/
            width: 100vw;
            height: 100vh;
        }
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }


        .box {
            margin: 0 10px; /* Adjust margin as needed */
            height:800px;
            width:600px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="box container">
            <div style="display: grid; place-content: center">
                <h1>Uzastny nadpis</h1>
                <h3>Sem vlozte vas kod</h3>
                <input type="text" name="codeInput" id="codeInput">
            </div>
        </div>
        <div id="loginFormFrame" class="box container" title="Embedded Website">
            <form id="loginForm">

                <label for="login">
                    Prihlasovacie meno:
                    <input type="text" name="login" value="" id="login" required>
                </label>
                <label for="password">
                    Heslo:
                    <input type="password" name="password" value="" id="password" required>
                </label>

                <button type="submit">Prihlasit sa</button>
                <p style="color: #dc3545" id="errmsg"></p>
                <p>Este nemate vytvorene konto? <a href="register.html">Registrujte sa tu.</a></p>
            </form>


        </div>
    </div>


<script src="scripts/cookieHandler.js"></script>
<script>

    const loggedin = getCookie('loggedin');
    console.log(document.cookie)
    if (loggedin == true){
        $("#loginFormFrame").css("display", "none");
        console.log("Prihlaseny")
    }
    else{
        $("#loginFormFrame").css("display", "flex");
        console.log("Neprihlaseny")
    }

    document.getElementById("loginForm").addEventListener("submit", function(event){
        event.preventDefault();
        let login = document.getElementById("login").value;
        let password = document.getElementById("password").value;
        postData(login, password)
    });

    function postData(login, password){
        axios.post(login_api_url,
            {login: login, password: password},
            {headers: {'Content-Type': 'multipart/form-data'}})
            .then(function (result) {
                window.location.href = main_page_url;
            })
            .catch(error => {
                if (error.response) {
                    // The request was made but no response was received
                    let errmsg = document.getElementById("errmsg");
                    errmsg.textContent = error.response.data.message;
                } else if (error.request) {
                    console.error('No response received:', error.request);
                } else {
                    console.error('Error during request setup:', error.message);
                }
            });


    }

</script>
</body>
</html>