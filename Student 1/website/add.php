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

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['id'];
    $quantity = $_POST['quantite'];

    // Vérifie si l'utilisateur a déjà une commande en cours
    $users_id = $_SESSION['id'];
    $order_query = $pdo->prepare("SELECT * FROM commande WHERE client_id = ? AND etat_commande = 0");
    $order_query->execute([$users_id]);
    $order = $order_query->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        // Si l'utilisateur n'a pas de commande en cours, en créer une
        $now = date('Y-m-d H:i:s');
        $order_query = $pdo->prepare("INSERT INTO commande (date_commande, montant_commande, etat_commande, client_id) VALUES (?, ?, ?, ?)");
        $order_query->execute([$now, 0, 1, $users_id]);

        $order_id = $pdo->lastInsertId();
    } else {
        $order_id = $order['num_commande'];
    }

    // Vérifie si le produit est déjà dans le panier
    $cart_item_query = $pdo->prepare("SELECT * FROM article WHERE produit_id = ? AND num_commande = ? AND client_id = ?");
    $cart_item_query->execute([$product_id, $order_id, $users_id]);
    $cart_item = $cart_item_query->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        // Si le produit est déjà dans le panier, mettre à jour la quantité
        $new_quantity = $cart_item['quantite'] + $quantity;
        $total_price = $new_quantity * $cart_item['prix_unitaire'];

        $cart_item_update_query = $pdo->prepare("UPDATE article SET quantite = ?, prix_total = ? WHERE id = ?");
        $cart_item_update_query->execute([$new_quantity, $total_price, $cart_item['id']]);
    } else {
        // Sinon, ajouter le produit au panier
        $product_query = $pdo->prepare("SELECT * FROM produit WHERE id = ?");
        $product_query->execute([$product_id]);
        $product = $product_query->fetch(PDO::FETCH_ASSOC);

        $total_price = $quantity * $product['prix_unitaire'];

        $cart_item_insert_query = $pdo->prepare("INSERT INTO article (quantite, prix_total, produit_id, num_commande, client_id, etat_stock) VALUES (?, ?, ?, ?, ?, 1)");
        $cart_item_insert_query->execute([$quantity, $total_price, $product_id, $order_id, $users_id]);
    }

    // Rediriger l'utilisateur vers la page du panier
    header('Location: panier.php');
    exit();
} else {
    // Rediriger l'utilisateur vers la page d'accueil si la méthode HTTP n'est pas POST
    header('Location: index.php');
    exit();
}
?>
