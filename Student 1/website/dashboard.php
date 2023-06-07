<?php
require_once('includes/config.php');

// Vérifier si l'utilisateur est connecté, sinon rediriger vers la page de connexion
if(!$userLoggedIn) {
  header('Location: login.php');
  exit;
}

// Récupérer les informations de l'utilisateur connecté depuis la base de données
$stmt = $db->prepare("SELECT username, email FROM client WHERE id = ?");
$stmt->bind_param("i", $userLoggedIn);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows === 0) exit('Aucun utilisateur trouvé');
$user = $result->fetch_assoc();

// Récupérer les commandes passées par l'utilisateur
$stmt = $db->prepare("SELECT * FROM commande WHERE client_id = ?");
$stmt->bind_param("i", $userLoggedIn);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <title>Panier</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="dashboard.css">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  </head>
  <body>
    <header class="header">
      <div class="header__logo">
        <a href="index.php">
          <img class="header__img" src="images/market.png" height="50px" width="50px" object-fit="contain" alt="logo">
        </a>
      </div>
      <h1 class="header__title">MarketMate</h1>
      <nav class="header__nav">
        <ul class="header__list">
          <li class="header__item"><a class="header__link" href="index.php">Accueil</a></li>
          <li class="header__item"><a class="header__link" href="#produits">Produits</a></li>
          <li class="header__item"><a class="header__link" href="#promotions">Promotions</a></li>
          <li class="header__item"><a class="header__link" href="panier.php"><i class="fa fa-shopping-cart"></i> Panier</a></li>
          <?php if(isset($_SESSION['username'])): ?>
          <li class="header__item">
            <a class="header__link" href="dashboard.php">
              <i class="fa fa-user">&nbsp;</i>
              <a href="dashboard.php" class="header__link"><?= ucfirst(htmlspecialchars($_SESSION['username'])) ?></a>
            </a>
          </li>
          <?php else: ?>
          <li class="header__item">
            <a class="header__link" href="login.php">
              <i class="fa fa-sign-in"></i> Se connecter
            </a>
          </li>
          <?php endif; ?>
        </ul>
      </nav>
    </header>
<h1 class="titre-profil">Bienvenue sur votre profil, <?= htmlspecialchars($user['username']) ?></h1>
<p class="adresse-email">Votre adresse e-mail : <?= htmlspecialchars($user['email']) ?></p>

<div class="commandes">
  <?php 
  // Récupérer les commandes avec un état de commande égal à 1
  $stmt = $db->prepare("SELECT * FROM commande WHERE client_id = ? AND etat_commande = 1");
  $stmt->bind_param("i", $userLoggedIn);
  $stmt->execute();
  $result = $stmt->get_result();
  while($row = $result->fetch_assoc()): ?>
    <div class="commande">
      <h2 class="commande-titre">Commande du <?= $row['date_commande'] ?></h2>
      <p class="commande-montant">Montant : <?= $row['montant_commande'] ?> €</p>
  <?php
  // Récupérer les articles pour cette commande
  $stmt = $db->prepare("SELECT * FROM article WHERE num_commande = ? AND client_id = ?");
  $stmt->bind_param("ii", $row['num_commande'], $userLoggedIn);
  $stmt->execute();
  $articleResult = $stmt->get_result();
  ?>

  <ul class='articles'>
    <?php while($articleRow = $articleResult->fetch_assoc()): ?>
      <?php
        // Récupérer les informations sur le produit associé à cet article
        $stmt = $db->prepare("SELECT * FROM produit WHERE id = ?");
        $stmt->bind_param("i", $articleRow['produit_id']);
        $stmt->execute();
        $produitResult = $stmt->get_result();
        $produitRow = $produitResult->fetch_assoc();
        ?>
        <li class='article'>
            <img src="<?= $produitRow['url'] ?>" alt="<?= $produitRow['nom_produit'] ?>">
            <div>
                <p><?= $produitRow['nom_produit'] ?> (<?= $articleRow['quantite'] ?> <?= $produitRow['unite'] ?>)</p>
                <p><?= $articleRow['prix_total'] ?> €</p>
            </div>
        </li>

    <?php endwhile; ?>
  </ul>
</div>
  <?php endwhile; ?>
</div>
<p class="deconnexion"><a href="logout.php">Se déconnecter</a></p>

    
</body>
</html>