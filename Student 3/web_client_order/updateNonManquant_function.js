function updateNonManquant(order_id, start, totalProducts)
{
    for (let i = start; i < totalProducts; i++)
    {
        var checkbox = document.getElementById(i);
        var button = checkbox.classList;
        var product_id = checkbox.name;

        if (button == order_id)
        {
            var date = new Date().toISOString().slice(0, 19).replace('T', ' ');
            var xhr = makeRequest();
            var sql;

            if (checkbox.checked)
            {
                sql = `UPDATE article SET etat_stock = 1 WHERE id = ${product_id} AND num_commande = ${order_id}`;
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
    window.location.reload();
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