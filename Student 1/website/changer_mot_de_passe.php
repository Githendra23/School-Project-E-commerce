<?php
require_once "includes/config.php";
session_start();

if (!isset($_SESSION['userLoggedIn'])) {
  header("Location: index.php");
  exit();
}



$userLoggedIn = $_SESSION['userLoggedIn'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $password = $_POST["password"];
  $confirmPassword = $_POST["confirmPassword"];

  $errors = array();

  if (empty($password)) {
    array_push($errors, "Le champ mot de passe est requis.");
  }

  if (empty($confirmPassword)) {
    array_push($errors, "Le champ confirmation de mot de passe est requis.");
  }

  if ($password != $confirmPassword) {
    array_push($errors, "Les mots de passe ne correspondent pas.");
  }

  if (count($errors) == 0) {
    $stmt = $db->prepare("UPDATE client SET password = ? WHERE id = ?");
    $hashPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param("si", $hashPassword, $userLoggedIn);
    $stmt->execute();
    $stmt->close();

    $_SESSION['successMessage'] = "Le mot de passe a été changé avec succès.";
    header("Location: profil.php");
    exit();
  }
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Changer le mot de passe</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include_once "header.php" ?>

  <div class="container">
    <h1>Changer le mot de passe</h1>

    <?php if (isset($_SESSION['errorMessage'])): ?>
      <p class="errorMessage"><?= $_SESSION['errorMessage'] ?></p>
      <?php unset($_SESSION['errorMessage']) ?>
    <?php endif ?>

    <?php if (isset($_SESSION['successMessage'])): ?>
      <p class="successMessage"><?= $_SESSION['successMessage'] ?></p>
      <?php unset($_SESSION['successMessage']) ?>
    <?php endif ?>

    <?php if (!empty($errors)): ?>
      <ul class="errorMessage">
        <?php foreach ($errors as $error): ?>
          <li><?= $error ?></li>
        <?php endforeach ?>
      </ul>
    <?php endif ?>

    <form action="" method="POST">
      <div class="form-group">
        <label for="password">Nouveau mot de passe :</label>
        <input type="password" name="password" id="password">
      </div>

      <div class="form-group">
        <label for="confirmPassword">Confirmer le mot de passe :</label>
        <input type="password" name="confirmPassword" id="confirmPassword">
      </div>

      <button type="submit">Changer le mot de passe</button>
    </form>
  </div>

  <?php include_once "footer.php" ?>

</body>
</html>
