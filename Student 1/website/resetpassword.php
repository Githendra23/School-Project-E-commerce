<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Récupération de mot de passe</title>
        <link rel="stylesheet" href="reset_password.css">
    </head>
    
    <body>
        <div class="container">
            <h1>Récupération de mot de passe</h1>
            <p>Veuillez saisir votre adresse email pour recevoir un lien de réinitialisation de mot de passe.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <label>Adresse email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
                    <span class="help-block"><?php echo $email_err; ?></span>
                </div>  
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Envoyer">
                </div>
            </form>
            <p><a href="login.php">Retourner à la page de connexion</a></p>
        </div>    
    </body>
</html> 