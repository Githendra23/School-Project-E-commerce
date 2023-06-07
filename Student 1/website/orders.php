<?php
    require_once "process_orders.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Commande Client</title>
        <link rel="stylesheet" href="style.css">
        <script src="updateNonManquant_function.js"></script>
    </head>

    <body>
        <?php
        if ($number_of_orders == 0)
        {
            ?>

            <div class="centerPage">
                <h1>Aucun panier à traiter</h1>
            </div>

            <?php
        }
        else
        {
            ?>
            <h1 class="centerMargin">Traitement des paniers</h1>
            <?php

            $checkbox_id = 0;
            for ($i = 0; $i < $number_of_orders; $i++) 
            {
                ?>
                <table>
                    <tr>
                        <th colspan="3"><?php echo $customerOrder[$i]->getID() . ". " . $customerOrder[$i]->getName(); ?></th> 
                    </tr>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th><?php echo $customerOrder[$i]->getOrder_date(); ?></th>
                    </tr>
                    <?php
                    
                    for ($j = 0; $j < $customerOrder[$i]->getTotalProducts(); $j++)
                    {
                        $product_name = $customerOrder[$i]->getProductName($j);
                        $product_quantity = $customerOrder[$i]->getProductQuantity($j);
                        $product_id = $customerOrder[$i]->getProductId($j);
                        $order_id = $customerOrder[$i]->getOrder_id();
                        ?>
                        <tr>
                            <td><?php echo $product_name; ?></td>
                            <td><?php echo $product_quantity; ?></td>
                            <td>
                                <?php if($customerOrder[$i]->getProductAvailable($j) == "true") { ?>
                                    <a><img src="checked.png" style="width:30px; height:30px;"></a>
                                <?php } else { ?>
                                    <a><img src="cancel.png" style="width:30px; height:30px;"></a>
                                <?php } ?>
                            </td>

                        </tr>
                        <?php
                        $checkbox_id++;
                    }
                    ?>
                    </table>
                    <table style="border-color: transparent;">
                    <tr>
                        <th colspan="3" style="background-color : transparent;">
                            <button onclick="updateNonManquant(<?php echo $customerOrder[$i]->getOrder_id(); ?>)">Valider</button>
                        </th>
                    </tr>
                </table>
                
                <br> <br>
                <?php
            }
        }
        ?>
    </body>
</html>