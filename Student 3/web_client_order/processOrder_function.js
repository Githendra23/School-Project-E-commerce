function processOrder(order_id, totalProducts) 
{
    for (let i = 0; i < totalProducts; i++) 
    {
        var checkbox = document.getElementById(order_id + '-' + i);
        var product_id = checkbox.getAttribute("data-product-id");
        var product_quantity = checkbox.getAttribute("data-product-quantity");

        if (checkbox) 
        {
            var date = new Date().toISOString().slice(0, 19).replace('T', ' ');
            var xhr = makeRequest();
            var sql;

            if (checkbox.checked) 
            {
                sql = `UPDATE article SET etat_stock = 1 WHERE id = ${product_id} AND num_commande = ${order_id}`;
                xhr.send(JSON.stringify({sql: sql}));

                xhr = makeRequest();

                sql = `UPDATE produit SET stock = stock - ${product_quantity} WHERE id = ${product_id}`;
                xhr.send(JSON.stringify({sql: sql}));
            } 
            else 
            {     
                sql = `UPDATE article SET etat_stock = 0 WHERE id = ${product_id} AND num_commande = ${order_id}`;
                xhr.send(JSON.stringify({sql: sql}));
            }

            xhr = makeRequest();

            sql = `UPDATE commande SET etat_commande = 2, date_traitement = '${date}' WHERE num_commande = ${order_id}`;
            xhr.send(JSON.stringify({sql: sql}));
        }
    }
    setTimeout(function() {
        window.location.reload();
    }, 10);
}

function makeRequest() 
{
    var xhr = new XMLHttpRequest();
    var url = "db_query.php";

    xhr.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) 
        {
            var results = JSON.parse(this.responseText);
        }
    };
    
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/json");

    return xhr;
}
