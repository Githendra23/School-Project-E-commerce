<?php
require_once('includes/config.php');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);
  $confirm_password = trim($_POST['confirm_password']);

  $errors = [];

  if(empty($username)) {
    $errors[] = 'Le champ "Nom d\'utilisateur" est obligatoire';
  }

  if(empty($email)) {
    $errors[] = 'Le champ "Email" est obligatoire';
  } else {
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Le format de l\'email n\'est pas valide';
    }
  }

  if(empty($password)) {
    $errors[] = 'Le champ "Mot de passe" est obligatoire';
  } else {
    if(strlen($password) < 6) {
      $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
    }
    if($password !== $confirm_password) {
      $errors[] = 'Les mots de passe ne correspondent pas';
    }
  }

  if(empty($errors)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hashage du mot de passe
    date_default_timezone_set('UTC');
    $date_creation = date('Y-m-d H:i:s');

    $stmt = $db->prepare("INSERT INTO client (username, email, password, date_creation) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
      die('Erreur de préparation de la requête : ' . $db->error);
    }
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $date_creation);
    if (!$stmt->execute()) {
      die('Erreur d\'exécution de la requête : ' . $stmt->error);
    }
        // Récupérer l'id du client inséré
    $client_id = $stmt->insert_id;

    // Créer une nouvelle commande
    $date_commande = date('Y-m-d H:i:s');
    $montant_commande = 0;
    $etat_commande = 0;

    $stmt = $db->prepare("INSERT INTO commande (date_commande, montant_commande, etat_commande, client_id) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
      die('Erreur de préparation de la requête : ' . $db->error);
    }
    $stmt->bind_param("sdii", $date_commande, $montant_commande, $etat_commande, $client_id);
    if (!$stmt->execute()) {
      die('Erreur d\'exécution de la requête : ' . $stmt->error);
    }


    header('Location: login.php');
    exit;
  }
}
?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Créer un compte</title>
    <link rel="stylesheet" href="register.css">
  </head>
    <body>
    <header class="header">
      <div class="header__logo">
        <a href="index.php"><img class="header__img" src="images/market.png" height="50px" width="50px" object-fit="contain" alt="logo"></a>
      </div>
      <h1 class="header__title">MarketMate</h1>
      <nav class="header__nav">
        <ul class="header__list">
          <li class="header__item"><a class="header__link" href="index.php">Accueil</a></li>
          <li class="header__item"><a class="header__link" href="#produits">Produits</a></li>
          <li class="header__item"><a class="header__link" href="#promotions">Promotions</a></li>
          <li class="header__item"><a class="header__link" href="#panier">Panier</a></li>
          <?php if(isset($_SESSION['username'])): ?>
            <li class="header__item"><a class="header__link" href="dashboard.php"><i class="fa fa-user"></i> Mon compte</a></li>
            <li class="header__item"><a class="header__link" href="logout.php"><i class="fa fa-sign-out"></i> Se déconnecter</a></li>
          <?php else: ?>
            <li class="header__item"><a class="header__link" href="login.php"><i class="fa fa-sign-in"></i> Se connecter</a></li>
          <?php endif; ?>
        </ul>
      </nav>

    </header>
    <div class="container">
      <h1>Créer un compte</h1>

      <?php if(!empty($errors)): ?>
        <div class="errors">
          <?php foreach($errors as $error): ?>
            <p><?= $error ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="post" action="">
        <div class="form-group">
          <label for="username">Nom d'utilisateur</label>
          <input type="text" id="username" name="username" value="<?= $username ?? '' ?>" required>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= $email ?? '' ?>" required>
        </div>

        <div class="form-group">
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
          <label for="confirm_password">Confirmer le mot de passe</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <button type="submit">Créer le compte</button>
      </form>

      <p>Déjà un compte? <a href="login.php">Se connecter</a></p>
    </div>
  </body>
</html>
