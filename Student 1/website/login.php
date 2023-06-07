<?php
// Inclure le fichier de configuration et de connexion à la base de données
require_once('includes/config.php');

// Initialiser les variables pour stocker les informations d'identification de l'utilisateur
$username = "";
$password = "";
$username_err = "";
$password_err = "";

// Traitement du formulaire de connexion lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Valider le nom d'utilisateur
    if (!isset($_POST["username"]) || strlen(trim($_POST["username"])) == 0) {
        $username_err = "Veuillez entrer votre nom d'utilisateur.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Valider le mot de passe
    if (!isset($_POST["password"]) || strlen(trim($_POST["password"])) == 0) {
        $password_err = "Veuillez entrer votre mot de passe.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Vérifier les erreurs de saisie avant de continuer
    if (empty($username_err) && empty($password_err)) {
        // Préparer une requête SELECT pour récupérer l'utilisateur correspondant aux informations d'identification fournies
        $sql = "SELECT id, username, password FROM client WHERE username = ?";

        if ($stmt = $db->prepare($sql)) {
            // Définir les paramètres et lier les variables à la requête préparée en tant que paramètres
            $param_username = $username;
            $stmt->bind_param("s", $param_username);

            // Exécuter la requête préparée
            if ($stmt->execute()) {
                // Stocker le résultat
                $stmt->store_result();

                // Vérifier si le nom d'utilisateur existe, si oui alors vérifier le mot de passe
                if ($stmt->num_rows == 1) {
                    // Lier les résultats de la requête à des variables
                    $stmt->bind_result($id, $username, $password_db);
                    if ($stmt->fetch()) {
                        // Vérifier si le mot de passe saisi correspond au mot de passe dans la base de données
                        if (password_verify($password, $password_db)) {
                            // Le mot de passe est correct, alors commencer une session
                            session_start();

                            // Stocker les données de l'utilisateur dans des variables de session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            // Rediriger l'utilisateur vers la page de tableau de bord
                            echo "<script>window.location.replace('index.php');</script>";
                        } else {
                            // Afficher un message d'erreur si le mot de passe est incorrect
                            $password_err = "Le mot de passe que vous avez entré n'est pas valide.";
                        }
                    }
                } else {
                    // Afficher un message d'erreur si le nom d'utilisateur n

                    // Afficher un message d'erreur si le nom d'utilisateur n'existe pas
                    $username_err = "Aucun compte trouvé avec ce nom d'utilisateur.";
                }
            } else {
                echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
            }

            // Fermer la déclaration préparée
            $stmt->close();
        }
    }

    // Fermer la connexion à la base de données
    $db->close();
}
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Connexion</title>
        <link rel="stylesheet" href="log.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
        <script>
            window.addEventListener("load", function() {
            document.querySelector(".container").classList.add("loaded");
            });
            const togglePassword = document.querySelector('.toggle-password');
                const passwordInput = document.querySelector('input[name="password"]');
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });   
    </script>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        $(document).ready(function(){
            $('.toggle-password').click(function(){
                $(this).toggleClass('fa-eye fa-eye-slash');
                var input = $(this).parent().find('input');
                if(input.attr('type') == 'password') {
                    input.attr('type', 'text');
                } else {
                    input.attr('type', 'password');
                }
            });
        });
    </script>

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
            <?php else: ?>
            <li class="header__item"><a class="header__link" href="login.php"><i class="fa fa-sign-in"></i> Se connecter</a></li>
            <?php endif; ?>
        </ul>
        </nav>

    </header>
        <div class="container">
    <h1>Connexion</h1>
    <p>Veuillez remplir ce formulaire pour vous connecter.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
            <label>Nom d'utilisateur ou adresse e-mail</label>
            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>">
            <span class="help-block"><?php echo $username_err; ?></span>
        </div>    
        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
            <label>Mot de passe</label>
            <div class="password-wrapper">
                <input type="password" name="password" class="form-control">
                <span class="toggle-password"><i class="fa fa-eye"></i></span>
            </div>
            <span class="help-block"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Se connecter">
        </div>
        <div class="form-group">
            <a href="forgot_password.php">Mot de passe oublié?</a>
        </div>
        <p>Vous n'avez pas de compte? <a href="register.php">S'inscrire maintenant</a>.</p>
    </form>
</div>
</body>
</html>