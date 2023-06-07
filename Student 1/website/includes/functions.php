<?php
require_once('config.php');

function register_user($email, $password) {
    global $db;

    // Vérifier si l'utilisateur existe déjà
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($db, $query);
    if (mysqli_num_rows($result) > 0) {
        return 'Cet email est déjà utilisé';
    }

    // Hacher le mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insérer l'utilisateur dans la base de données
    $query = "INSERT INTO users (email, password, created_at) VALUES ('$email', '$hashed_password', NOW())";
    mysqli_query($db, $query);

    return 'Compte créé avec succès';
}

function login_user($email, $password) {
    global $db;

    // Récupérer l'utilisateur correspondant à l'email
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($db, $query);
    if (mysqli_num_rows($result) == 0) {
        return 'Email ou mot de passe incorrect';
    }
    $user = mysqli_fetch_assoc($result);

    // Vérifier le mot de passe
    if (!password_verify($password, $user['password'])) {
        return 'Email ou mot de passe incorrect';
    }

    // Démarrer la session et stocker l'ID de l'utilisateur
    session_start();
    $_SESSION['user_id'] = $user['id'];

    return 'Connexion réussie';
}

function logout_user() {
    session_start();
    unset($_SESSION['user_id']);
    session_destroy();
    header('Location: index.php');
    exit();
}

function is_user_logged_in() {
    session_start();
    return isset($_SESSION['user_id']);
}

function get_logged_in_user() {
    global $db;

    session_start();

    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($db, $query);
    $user = mysqli_fetch_assoc($result);

    return $user;
}
?>
