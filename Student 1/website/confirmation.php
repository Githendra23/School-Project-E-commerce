<?php
$dsn = 'mysql:host=localhost;dbname=site_e-commerce';
$username = 'root';
$password = '';

require_once "includes/config.php";
require_once "_header.php";


try {
    $db = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
    exit;
}

$client_id = $_SESSION['id'];

// Mise à jour de l'état de la commande précédente
$update_commande = $db->prepare('UPDATE commande SET etat_commande = 1 WHERE client_id = :client_id AND etat_commande = 0');
$update_commande->execute(array('client_id' => $client_id));

// Création d'une nouvelle commande pour le client actuel
$date_commande = date('Y-m-d H:i:s');
$insert_commande = $db->prepare('INSERT INTO commande (date_commande, montant_commande, etat_commande, client_id) VALUES (:date_commande, 0, 0, :client_id)');
$insert_commande->execute(array('date_commande' => $date_commande, 'client_id' => $client_id));
$num_commande = $db->lastInsertId();


?>
<!DOCTYPE html>
<html>
<head>
    <title>Confirmation de commande</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,600,700&display=swap" rel="stylesheet">
    <style>
        /* General styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }
        body {
            background-color: #f2f2f2;
        }
        .container {
          max-width: 800px;
          margin: 0 auto;
          padding: 40px;
          text-align: center;
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
        }

        /* Check mark styles */
        .checkmark {
            display: block;
            width: 200px;
            height: 200px;
            margin: 0 auto;
            position: relative;
            transform: translateY(-30px);
        }
        .checkmark__circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 5;
            stroke-miterlimit: 10;
            stroke: #4bb71b;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        .checkmark__check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            stroke-width: 5;
            stroke-miterlimit: 10;
            stroke: #4bb71b;
            fill: none;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards,
                       fill 0.3s ease-in-out 0.8s forwards;
        }
        @keyframes stroke {
            100% {
                stroke-dashoffset: 0;
            }
        }
        @keyframes fill {
            100% {
                box-shadow: inset 0px 0px 0px 30px #4bb71b;
            }
        }

        /* Button styles */
        .btn {
            display: inline-block;
            background-color: #4bb71b;
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease-in-out;
            margin-top: 40px;
        }
        .btn:hover {
            background-color: #3b9014;
        }

        /* Additional styles */
        h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            font-weight: 400;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        .icon-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
        }
        .icon-container i {
            font-size: 80px;
            color: #4bb71b;
        }
    </style>
</head>
<body>
    <div class="container">
<div class="icon-container">
<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60">
<circle class="checkmark__circle" cx="30" cy="30" r="24" fill="none"/>
<path class="checkmark__check" fill="none" d="M22 30.5L27.5 36 38 25"/>
</svg>
</div>
<h1>Votre commande a été confirmée</h1>
<p>Merci pour votre achat. Nous avons bien reçu votre commande et nous la traitons dès maintenant. Un e-mail de confirmation vous sera envoyé très prochainement.</p>
<a href="index.php" class="btn">Retour à la page d'accueil</a>
</div>

</body>
</html>

