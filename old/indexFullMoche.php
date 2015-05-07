<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Bqbqr">

    <title>Commande Pain</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">

    <script src="js/jquery-1.11.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        // Lorsque je soumets le formulaire

        $('#commande').on('submit', function(e) {
            e.preventDefault(); // J'empêche le comportement par défaut du navigateur, c-à-d de soumettre le formulaire
     
            var $this = $(this); // L'objet jQuery du formulaire
     
            // Je récupère les valeurs
            var $nom = $('#nom').val();

            // Je vérifie une première fois pour ne pas lancer la requête HTTP
            // si je sais que mon PHP renverra une erreur
            if($nom == '') {
                alert('Les champs doivent êtres remplis');
            } else {
                // Envoi de la requête HTTP en mode asynchrone
                $.ajax({
                    url: $this.attr('action'), // Le nom du fichier indiqué dans le formulaire
                    type: $this.attr('method'), // La méthode indiquée dans le formulaire (get ou post)
                    data: $this.serialize(), // Je sérialise les données (j'envoie toutes les valeurs présentes dans le formulaire)
                    success: function(html) { // Je récupère la réponse du fichier PHP
                      //alert(html);
                      $('#validation').show();
                      $('#validation').delay(5000).fadeOut(1000);
                      $('#commande')[0].reset();
                      $("#insert").load("index.php #content");
                    }
                });

            }
        });

        $(document).on("click", ".undel", function(){
            //On recup le nom du bouton ie le numéro de la commande
            if (confirm("Annuler la suppression pour "+$(this).attr('value')+"?")) { 
              var $commandeASuppr=$(this).attr('name');
              $.ajax({

                  url: 'undel.php?id='+$commandeASuppr, // Le nom du fichier indiqué dans le formulaire
                  success: function(html) { // Je récupère la réponse du fichier PHP
                    $("#insert").load("index.php #content");
                  },
                  error: function(html){
                    alert(html);
                  }
              });
            }
        });

        $(document).on("click", ".del", function(){
            //On recup le nom du bouton ie le numéro de la commande
            if (confirm("Supprimer la commande pour "+$(this).attr('value')+"?")) { 
              var $commandeASuppr=$(this).attr('name');
              $.ajax({

                  url: 'del.php?id='+$commandeASuppr, // Le nom du fichier indiqué dans le formulaire
                  success: function(html) { // Je récupère la réponse du fichier PHP
                    $("#insert").load("index.php #content");
                  },
                  error: function(html){
                    alert(html);
                  }
              });
            }
        });


        $('.qtyplus').click(function(e){
            e.preventDefault();
            // Get the field name
            fieldName = $(this).attr('field');
            // Get its current value
            var currentVal = parseInt($('input[name='+fieldName+']').val());
            // If is not undefined
            if (!isNaN(currentVal)) {
                // Increment
                $('input[name='+fieldName+']').val(currentVal + 1);
            } else {
                // Otherwise put a 0 there
                $('input[name='+fieldName+']').val(0);
            }
        });
        $(".qtyminus").click(function(e) {
            e.preventDefault();
            // Get the field name
            fieldName = $(this).attr('field');
            // Get its current value
            var currentVal = parseInt($('input[name='+fieldName+']').val());
            // If it isn't undefined or its greater than 0
            if (!isNaN(currentVal) && currentVal > 0) {
                // Decrement one
                $('input[name='+fieldName+']').val(currentVal - 1);
            } else {
                // Otherwise put a 0 there
                $('input[name='+fieldName+']').val(0);
            }
        });
    });


        </script>
  </head>

<?php 
// on se connecte à MySQL 
$db = mysql_connect('localhost', 'adminpain', 'rockmyroot'); 

// on sélectionne la base 
mysql_select_db('pain',$db); 

?> 


  <body role="document">

    <!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand">Commande Pain</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="index.php">Liste</a></li>
            <li><a href="admin.php">Admin</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container theme-showcase" role="main">

      <!-- Main jumbotron for a primary marketing message or call to action -->
      <div class="jumbotron">
        <h1>Commande pain, page de test.</h1>
        <p>Pain disponible dès 8h </br>Bread available from 8am</p>
      </div>
      
        <h2> Nouvelle commande - Place an Order</h2>
          <div class="row">
            <div class="col-md-12">
              <form id='commande' method='POST' action='action.php'>
              <table class="table table-striped">
                <tbody>
                  <thead>
                    <tr>
                      <th>Nom</th>
                      <th>Baguettes</th>
                      <th>Traditions</th>
                      <th>Croissant</th>
                      <th>Pain au Chocolat</th>
                      <th>Petit Déjeuner</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tr>
                    <td><input name="nom" id="nom" type="text" class="form-control" placeholder="Nom/Name"></td>
                    <td>
                      <input type="button" value="-" class="btn btn-default qtyminus" field="baguette" />
                      <input name="baguette" type="text" name="quantityb" value="0" class="qty" readonly/>
                      <input type="button" value="+" class="btn btn-default qtyplus" field="baguette" />
                    </td>
                    <td>
                      <input type="button" value="-" class="btn btn-default qtyminus" field="tradition" />
                      <input name="tradition" type="text" name="quantityt" value="0" class="qty" readonly />
                      <input type="button" value="+" class="btn btn-default qtyplus" field="tradition" />
                    </td>
                    <td>
                      <input type="button" value="-" class="btn btn-default qtyminus" field="croissant" />
                      <input name="croissant" type="text" name="quantityc" value="0" class="qty" readonly/>
                      <input type="button" value="+" class="btn btn-default qtyplus" field="croissant" />
                    </td>
                    <td>
                      <input type="button" value="-" class="btn btn-default qtyminus" field="pac" />
                      <input name="pac" type="text" name="quantityp" value="0" class="qty" readonly/>
                      <input type="button" value="+" class="btn btn-default qtyplus" field="pac" />
                    </td>
                    <td>
                      <input type="button" value="-" class="btn btn-default qtyminus" field="petitdej" />
                      <input name="petitdej" type="text" name="quantityd" value="0" class="qty" readonly/>
                      <input type="button" value="+" class="btn btn-default qtyplus" field="petitdej" />
                    </td>
                    <td>
                      <button type="submit" class="btn btn-default btn-lg">Passer la Commande</button>
                    </td>
                  </tr>
                </tbody>
              </table>
              </form>
            </div>
          </div>
      


      <div class="row">
        <div class="col-md-12">
          <div id="validation" class="alert alert-success" role="alert" style="display:none;">
            <p> Commande validée avec succés! Order done! </p>
          </div>
        </div>
      </div>
      <div id="insert" class="row">
        <div id="content" class="col-md-12">
          <table id="main"class="table table-striped">
            <thead>
              <tr>
                <th>Nom</th>
                <th>Baguettes</th>
                <th>Traditions</th>
                <th>Croissant</th>
                <th>Pain au Chocolat</th>
                <th>Petit Déjeuner</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php  
              // on crée la requête SQL 
              $sql = 'SELECT nomclient, baguette, tradition, croissant, pac, petitdejeuner,id,deleted FROM commande WHERE datestamp=curdate() ORDER BY id DESC;
'; 

              // on envoie la requête 
              $req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 

              // on fait une boucle qui va faire un tour pour chaque enregistrement 
              while($data = mysql_fetch_assoc($req)) 
                  {

                  $deleted=$data['deleted'];

                  // on affiche les informations de l'enregistrement en cours 
                  if($deleted==1)
                    echo '<tr><td>'.$data['nomclient'].'<br><span class="label label-warning">Commande supprimée</span></td>';
                  else
                    echo '<tr><td>'.$data['nomclient'].'</td>';
                  echo '<td>'.$data['baguette'].'</td>';
                  echo '<td>'.$data['tradition'].'</td>';
                  echo '<td>'.$data['croissant'].'</td>';
                  echo '<td>'.$data['pac'].'</td>';
                  echo '<td>'.$data['petitdejeuner'].'</td>';
                  if($deleted==1){
                    echo '<td style = "1"><button value="'.$data['nomclient'].'" name="'.$data['id'].'" type="button" class="btn btn-info undel" aria-label="Right Align">Annuler Suppression</button></td></tr>';
                    echo '</del>';
                  }
                  else
                    echo '<td><button value="'.$data['nomclient'].'" name="'.$data['id'].'" type="button" class="btn btn-warning del" aria-label="Right Align">Supprimer Commande</button></td></tr>';
                  } 

              // on ferme la connexion à mysql 
              mysql_close(); 
              ?>
              
            </tbody>
          </table>
        </div>
        </div>

    </div> <!-- /container -->