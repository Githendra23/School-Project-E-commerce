<?php
    require_once('includes/config.php');

    // Détruire la session et rediriger l'utilisateur vers la page de connexion
    session_start();
    session_destroy();
    header('Location: login.php');
    exit;
?>