<?php
$dsn = 'mysql:host=localhost;dbname=site_e-commerce';
$username = 'root';
$password = '';

require_once "includes/config.php";
require_once "_header.php";

try {
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
}

function getProduitById($produit_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM produit WHERE id = :produit_id");
    $stmt->bindParam(":produit_id", $produit_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Vérifie si l'utilisateur est connecté, sinon redirige vers la page de connexion
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Récupérer l'id de l'utilisateur connecté
$client_id = $_SESSION["id"];



// Récupérer les informations du panier
$sql = "SELECT c.num_commande, c.date_commande, p.id AS produit_id, p.nom_produit, p.prix_unitaire, a.quantite, p.url, (p.prix_unitaire * a.quantite) as prix_total
        FROM commande c
        JOIN article a ON c.num_commande = a.num_commande AND c.client_id = a.client_id
        JOIN produit p ON a.produit_id = p.id
        WHERE c.client_id = :client_id AND c.etat_commande = 0";

if($stmt = $pdo->prepare($sql)){
    $stmt->bindParam(":client_id", $client_id, PDO::PARAM_INT);
    
    if($stmt->execute()){
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }
}

// Calculer le montant total de la commande
$total = 0;
foreach($rows as $row){ 
    $prix_total = $row["prix_total"];
    $total += $prix_total;
}

// Envoyer le montant total à la table commande
$sql = "UPDATE commande SET montant_commande = :total WHERE client_id = :client_id AND etat_commande = 0";
if($stmt = $pdo->prepare($sql)){
    $stmt->bindParam(":total", $total, PDO::PARAM_INT);
    $stmt->bindParam(":client_id", $client_id, PDO::PARAM_INT);
    if(!$stmt->execute()){
        echo "Oops! Something went wrong. Please try again later.";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panier</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="panier.css">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="./main.css">
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
          <li class="header__item"><a class="header__link" href="panier.php"><i class="fa fa-shopping-cart"></i> Panier</a></li>
          <?php if(isset($_SESSION['username'])): ?>
            <li class="header__item"><a class="header__link" href="dashboard.php"><i class="fa fa-user">&nbsp;</i><a href="dashboard.php" class="header__link"><?= ucfirst(htmlspecialchars($_SESSION['username'])) ?></a></li>
      <?php else: ?>
        <li class="header__item"><a class="header__link" href="login.php"><i class="fa fa-sign-in"></i> Se connecter</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>

<div class="wrapper">
    <h2>Panier</h2>
    <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="text-align: center;">Images</th>
                    <th>Nom du produit</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                foreach($rows as $row){ 
                    $prix_total = $row["prix_total"];
                    $total += $prix_total;
                    $produit = getProduitById($row["produit_id"]);
                ?>
                    <tr>
                        <td style="text-align: center;"><img src="<?php echo $produit["url"]; ?>" width="50" height="50" align-items="center"></td>
                        <td><?php echo $produit["nom_produit"]; ?></td>
                        <td><?php echo $produit["prix_unitaire"]."€"; ?></td>
                        <td><?php echo $row["quantite"]; ?></td>
                        <td><?php echo $prix_total."€"; ?></td>
                        <td style="text-align: center;"><a href="supprimer_article.php"><img src="images/del.png" alt="Supprimer l'article" width="50" height="50" align-items="center"></a></td>
                    </tr>
                <?php } ?>
                <thead>
                    <tr>
                        <td bgcolor="white" colspan="4"></td>
                        <td colspan="1"><strong>Total</strong></td>
                        <td><strong><?php echo $total."€"; ?></strong></td>
                    </tr>
                </thead>
            </tbody>
        </table>
        <div class="button-container" align="right">
            <button class="order" onclick="setTimeout(function(){ window.location.href = 'confirmation.php'; }, 8000);">
            <span class="default">Confirmer Commande</span>
            <span class="success">Commande effectuée
                <svg viewbox="0 0 12 10">
                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                </svg>
            </span>
            <div class="box"></div>
            <div class="truck">
                <div class="back"></div>
                <div class="front">
                    <div class="window"></div>
                </div>
                <div class="light top"></div>
                <div class="light bottom"></div>
            </div>
            <div class="lines"></div>
        </button>


        </div>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
        <script src="./main.js"></script>
    </div> 
    
</body>
</html>
    