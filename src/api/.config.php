<?php
//$hostname = 'localhost';
//$username = 'xbelano';
//$password = 'tajne-HESLO.7624';
//$dbname = 'zaverecne';

$hostname = 'localhost';
$username = 'xbelano';
$password = 'tajne-HESLO.7624';
$dbname = 'zapocet';

$conn = mysqli_connect($hostname, $username, $password, $dbname);

if (!$conn){
    die('Connection failed' . mysqli_connect_error());
}

?>