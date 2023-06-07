<?php 
  require '_header.php';

  if (!isset($_SESSION['username'])) {
    // Rediriger vers la page de connexion
    header("Location: login.php");
    exit();
  }

  // Si le formulaire a été soumis, ajouter le produit au panier
  if (isset($_POST['id']) && isset($_POST['quantite'])) {
    $id = $_POST['id'];
    $quantite = $_POST['quantite'];
    $produit = $DB->query('SELECT * FROM produit WHERE id = ?', array($id))[0];

    // Si le produit est périssable, convertir la quantité en kg
    if ($produit['type'] === "périssable") {
      $quantite = $quantite / 1000;
    }

    // Ajouter le produit au panier
    if (isset($_SESSION['panier'][$id])){
      $_SESSION['panier'][$id]['quantite'] += $quantite;
    } else {
      $_SESSION['panier'][$id] = array('quantite' => $quantite, 'produit' => $produit);
    }

    // Rediriger vers la page du panier
    header("Location: panier.php");
    exit();
  }

  // Récupérer les produits de la base de données
  $produits = $DB->query('SELECT * FROM produit');
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>MarketMate</title>
    <link rel="stylesheet" type="text/css" href="home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
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
          <li class="header__item"><a class="header__link" href="panier.php"><i class="fa fa-shopping-cart"></i>Panier</a></li>
          <?php if(isset($_SESSION['username'])): ?>
            <li class="header__item"><a class="header__link" href="dashboard.php"><i class="fa fa-user">&nbsp;</i><?= ucfirst(htmlspecialchars($_SESSION['username'])) ?></a></li>

            <?php if ($_SESSION['id'] == 1): ?>
              <li class="header__item"><a class="header__link" href="orders.php">Gestion</a></li>
              <li class="header__item"><a class="header__link" href="commande.php">Traitement de Commande</a></li>
            <?php endif; ?>

          <?php else: ?>
            <li class="header__item"><a class="header__link" href="login.php"><i class="fa fa-sign-in"></i> Se connecter</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </header>

    <main class="main">
  <?php foreach ($produits as $product): ?>
  <div class="product">
    <img src="images/<?= $product->id; ?>.png" alt="<?= $product->nom_produit; ?>">
    <h3 class="product__name"><?= $product->nom_produit; ?></h3>
    <p class="product__price"><?= number_format($product->prix_unitaire,2); ?>€/<?= $product->unite; ?></p>
    <form method="POST" action="<?php if (isset($_SESSION['username'])) { echo 'add.php'; } else { echo 'login.php'; } ?>">
      <input type="hidden" name="id" value="<?= $product->id; ?>">
        <input type="number" name="quantite" min="1" max="100" value="1" onchange="updatePrice(this.value, <?= $product->prix_unitaire; ?>)">
        <p class="product__total-price"><?= number_format($product->prix_unitaire,2); ?>€</p>
      <?php
        if (isset($_SESSION['username'])) {
            // utilisateur connecté, ajouter le produit au panier
            echo '<button class="product__button" type="submit"><i class="fa fa-cart-plus"></i> Ajouter au panier</button>';
        } else {
            // utilisateur non connecté, rediriger vers la page de connexion
            echo '<button class="product__button" onclick="window.location.href=\'login.php\'"><i class="fa fa-cart-plus"></i> Connectez-vous pour ajouter au panier</button>';
        }
      ?>
    </form>
  </div>
<?php endforeach; ?>
</main>
<script>
  function updatePrice(quantity, unitPrice) {
    const priceElement = event.target.nextElementSibling;
    const totalPrice = (quantity * unitPrice).toFixed(2);
    priceElement.innerHTML = `${totalPrice}€`;
  }
</script>

<footer class="footer">
  <p class="footer__copyright">&copy; 2022 Produits frais</p>
</footer>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="app.js"></script>

</body>
</html>