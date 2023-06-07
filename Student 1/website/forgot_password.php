<?php
  require 'includes/config.php';
  require 'includes/functions.php';

  if(isset($_POST['email'])){
    $email = $_POST['email'];
    $user = getUserByEmail($email);

    if(!$user){
      $error = "Cet email n'existe pas";
    }else{
      $reset_token = generateResetToken();
      updateResetToken($user['id'], $reset_token);

      // Envoyer un e-mail avec le lien pour réinitialiser le mot de passe
      $to = $user['email'];
      $subject = 'Réinitialisation du mot de passe';
      $message = "Bonjour ".$user['username'].",\r\n\r\n";
      $message .= "Nous avons bien reçu votre demande de réinitialisation de mot de passe. Veuillez cliquer sur le lien ci-dessous pour réinitialiser votre mot de passe :\r\n";
      $message .= BASE_URL."/reset_password.php?token=".$reset_token."\r\n\r\n";
      $message .= "Si vous n'avez pas fait cette demande, veuillez simplement ignorer cet e-mail.\r\n\r\n";
      $message .= "Merci,\r\n";
      $message .= "L'équipe de notre site";
      $headers = "From: noreply@monsite.com\r\n";
      mail($to, $subject, $message, $headers);

      $success = "Un e-mail contenant un lien pour réinitialiser votre mot de passe vous a été envoyé.";
    }
  }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Mot de passe oublié - Mon site</title>
</head>
<body>
  <h1>Mot de passe oublié</h1>
  <?php if(isset($error)): ?>
    <p style="color:red"><?php echo $error ?></p>
  <?php endif; ?>
  <?php if(isset($success)): ?>
    <p style="color:green"><?php echo $success ?></p>
  <?php endif; ?>
  <form method="post">
    <label for="email">Email :</label>
    <input type="email" name="email" required>
    <input type="submit" value="Envoyer">
  </form>
</body>
</html>
