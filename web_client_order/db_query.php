<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "site_e-commerce";

$sql = json_decode(file_get_contents("php://input"))->sql;

$link = mysqli_connect($servername, $username, $password, $dbname);
$result = mysqli_query($link, $sql);

if ($result)
{
    echo "SQL command executed successfully!";
} 
else
{
    echo "Error executing SQL command: " . mysqli_error($link);
}

mysqli_close($link);
?>