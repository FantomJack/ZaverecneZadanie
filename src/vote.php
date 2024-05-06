<?php
// Get the full request URI
$request_uri = $_SERVER['REQUEST_URI'];

// Split the URI into parts
$uri_parts = explode('/', $request_uri);

// Get the last part of the URI, which should be your code
$code = end($uri_parts);

// Do something with the code, like display it or process it
echo "The code is: " . htmlspecialchars($code);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src=".config.js"></script>
    <link rel="stylesheet" href="styles/main.css">
    <title>Document</title>
</head>
<body>
<div id="qrcode"></div>

</body>
</html>