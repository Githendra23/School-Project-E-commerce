<?php
  $host = 'localhost';
  $username = 'root';
  $password = '';
  $database = 'site_e-commerce';
  $db = mysqli_connect($host, $username, $password, $database);
  if (!$db) {
    die('Erreur de connexion: ' . mysqli_connect_error());
  }

  // Vérifier si l'utilisateur est connecté
  session_start();
  $userLoggedIn = false;
  if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    $userLoggedIn = $_SESSION["id"];
  }
?>