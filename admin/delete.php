<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Bqbqr">

    <title>Commande Pain</title>

    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/bootstrap-theme.min.css" rel="stylesheet">

    <script src="../js/jquery-1.11.2.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $(document).on("click", ".undel", function(){
            //On recup le nom du bouton ie le numéro de la commande
            if (confirm("Annuler la suppression pour "+$(this).attr('value')+"?")) { 
              var $commandeASuppr=$(this).attr('name');
              $.ajax({

                  url: 'undel.php?id='+$commandeASuppr, // Le nom du fichier indiqué dans le formulaire
                  success: function(html) { // Je récupère la réponse du fichier PHP
                    $("#insert").load("delete.php #content");
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
                    $("#insert").load("delete.php #content");
                  },
                  error: function(html){
                    alert(html);
                  }
              });
            }
        });

    });


        </script>
  </head>

<?php
include('utils.php');

$today = date('Y-m-d',strtotime(date("Y-m-d") . "+1 days"));
$day=

// on se connecte à MySQL 
include('../secure/config.php');
$db=mysql_connect($SQLhost, $SQLlogin, $SQLpass) or die(mysql_error());

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
            <li><a href="admin.php">Admin</a></li>
            <li><a href="show.php">Commandes du jour</a></li>
            <li class="active"><a href="delete.php">Suppression</a></li>
            <li><a href="cook.php">Stocks</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container theme-showcase" role="main">    
      <div id="insert" class="row">
        <div id="content" class="col-md-12">
          <h2> Suppression des commandes pour le <?php echo "$today"; ?> </h2>
          <table id="main" class="table table-striped">
            <thead>
              <tr>
                <th>Nom</th>
                <th>Emplacement</th>
                <th>Baguettes</th>
                <th>Traditions</th>
                <th>Duchesses</th>
                <th>Croissant</th>
                <th>Pain au Chocolat</th>
                <th>Petit Déjeuner</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php 
              // on crée la requête SQL 

              $sql = 'SELECT nomclient, emplacement, baguette, tradition, croissant, pac, duchesse, petitdejeuner,id,deleted,recuperee FROM commande WHERE datestamp="'.$today.'" ORDER BY deleted,nomclient;'; 
              //echo $sql;
              // on envoie la requête 
              $req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 

              // on fait une boucle qui va faire un tour pour chaque enregistrement 
              while($data = mysql_fetch_assoc($req)) 
                  {
                  $deleted=$data['deleted'];

                  echo '<td>'.$data['nomclient'].'</td>';
                  echo '<td>'.$data['emplacement'].'</td>';
                  echo '<td>'.$data['baguette'].'</td>';
                  echo '<td>'.$data['tradition'].'</td>';
                  echo '<td>'.$data['duchesse'].'</td>';
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