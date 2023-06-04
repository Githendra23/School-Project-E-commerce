<?php
$servername = "mysql-projet-e-commerce.alwaysdata.net";
$username = "312817";
$password = "LucasRatonLaveur";
$dbname = "projet-e-commerce_bdd";

$sql = json_decode(file_get_contents("php://input"))->sql;

$link = mysqli_connect($servername, $username, $password, $dbname);
$result = mysqli_query($link, $sql);

mysqli_close($link);
?>