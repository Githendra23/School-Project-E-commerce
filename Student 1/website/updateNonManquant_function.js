function updateNonManquant(order_id)
{    
    var xhr = new XMLHttpRequest();
    var url = "db_query.php";
    var sql;

    xhr.onreadystatechange = function()
    {
      if (this.readyState == 4 && this.status == 200)
      {
        var results = JSON.parse(this.responseText);
      }
    };
    
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/json");

    sql = `UPDATE commande SET etat_commande = 3 WHERE num_commande = ${order_id}`;
    xhr.send(JSON.stringify({sql: sql}));

    window.location.reload();
}