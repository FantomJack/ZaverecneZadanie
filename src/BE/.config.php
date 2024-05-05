<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

$hostname = 'localhost';
$username = 'xbelano';
$password = 'tajne-HESLO.7624';
$dbname = 'zaverecne';

$conn = mysqli_connect($hostname, $username, $password, $dbname);

if (!$conn){
    die('Connection failed' . mysqli_connect_error());
}