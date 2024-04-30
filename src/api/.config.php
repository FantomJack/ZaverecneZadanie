<?php
$hostname = 'mysql';
$username = '';
$password = '';
$dbname = 'zapocet';

$conn = mysqli_connect($hostname, $username, $password, $dbname);

if (!$conn){
    die('Connection failed' . mysqli_connect_error());
}
