<?php
require_once "customerOrder.class.php";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "site_e-commerce";

$link = new mysqli($servername, $username, $password, $dbname);
if ($link->connect_error)
{
    error_reporting("Erreur avec les client_id" . $link->connect_error);
}
else
{
    $result = $link->query("SELECT COUNT(num_commande) FROM commande WHERE etat_commande = 2");
    if ($result === false)
    {
        error_reporting("Erreur avec les client_id: " . mysqli_error($link));
    }
    else
    {
        $number_of_orders = $result->fetch_assoc()["COUNT(num_commande)"];
        
        if ($number_of_orders > 0)
        {
            $order_id = array();
            $result = $link->query("SELECT num_commande FROM commande WHERE etat_commande = 2 ORDER BY date_traitement ASC");
            while($row = $result->fetch_assoc())
            {
                $order_id[] = $row["num_commande"];
            }

            $customerOrder = array();
            for ($i = 0; $i < $number_of_orders; $i++) 
            {   
                $customerOrder[$i] = new CustomerOrder();
                $customerOrder[$i]->setOrder_id($order_id[$i]);
                
                $get_client_id = $link->query("SELECT client_id FROM commande WHERE num_commande = {$customerOrder[$i]->getOrder_id()}");
                $customerOrder[$i]->setID($get_client_id->fetch_assoc()["client_id"]);

                $get_client_id = $link->query("SELECT client_id FROM commande WHERE num_commande = {$customerOrder[$i]->getOrder_id()}");
                
                $get_date_order = $link->query("SELECT date_commande FROM commande WHERE num_commande = {$customerOrder[$i]->getOrder_id()}");
                $customerOrder[$i]->setOrder_date($get_date_order->fetch_assoc()["date_commande"]);
                
                $get_username = $link->query("SELECT username FROM client WHERE id = '{$customerOrder[$i]->getOrder_id()}'");
                $customerOrder[$i]->setName($get_username->fetch_assoc()["username"]);
                
                $get_number_of_products = $link->query("SELECT COUNT(nom_produit) FROM produit WHERE id IN (SELECT produit_id FROM article WHERE num_commande = {$customerOrder[$i]->getOrder_id()})");
                $customerOrder[$i]->setTotalProducts($get_number_of_products->fetch_assoc()["COUNT(nom_produit)"]);

                $get_products_name = $link->query("SELECT nom_produit FROM produit WHERE id IN (SELECT produit_id FROM article WHERE num_commande = {$customerOrder[$i]->getOrder_id()})");

                $count = 0;
                while ($row = $get_products_name->fetch_assoc())
                {
                    $customerOrder[$i]->setProductName($count, $row["nom_produit"]);
                    $count++;
                }
                
                for($j = 0; $j < $customerOrder[$i]->getTotalProducts(); $j++)
                {   
                    $get_product_quantity = $link->query("SELECT quantite FROM article WHERE num_commande = {$customerOrder[$i]->getOrder_id()} AND produit_id IN (SELECT id FROM produit WHERE nom_produit = '{$customerOrder[$i]->getProductName($j)}')");
                    $customerOrder[$i]->setProductQuantity($j, $get_product_quantity->fetch_assoc()["quantite"]);

                    $get_product_id = $link->query("SELECT id FROM article WHERE num_commande = {$customerOrder[$i]->getOrder_id()} AND produit_id IN (SELECT id FROM produit WHERE nom_produit = '{$customerOrder[$i]->getProductName($j)}')");
                    $customerOrder[$i]->setProductId($j, $get_product_id->fetch_assoc()["id"]);
                    
                    $result = $link->query("SELECT IF(etat_stock = 1, 'true', 'false') AS non_manquant_checked FROM article WHERE id = {$customerOrder[$i]->getProductId($j)}");
                    $customerOrder[$i]->setProductAvailable($j, $result->fetch_assoc()["non_manquant_checked"]);
                }
            }
        }
    }
}
?>