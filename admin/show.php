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

        $(document).on("click", ".recup", function(){
            //On recup le nom du bouton ie le numéro de la commande
              $.ajax({

                  url: 'recup.php?id='+$(this).attr('value'), // Le nom du fichier indiqué dans le formulaire
                  success: function(html) { // Je récupère la réponse du fichier PHP
                    $("#insert").load("show.php?date="+$('#dateaffiche').val()+" #content");
                  },
                  error: function(html){
                    alert(html);
                  }
              });
        });

        $(document).on("click", ".unrecup", function(){
            //On recup le nom du bouton ie le numéro de la commande
              $.ajax({

                  url: 'annulrecup.php?id='+$(this).attr('value'), // Le nom du fichier indiqué dans le formulaire
                  success: function(html) { // Je récupère la réponse du fichier PHP
                    $("#insert").load("show.php?date="+$('#dateaffiche').val()+" #content");
                  },
                  error: function(html){
                    alert(html);
                  }
              });
        });

        $('#dateaffiche').change(function(){
          $("#insert").load("show.php?date="+$(this).val()+" #content");
        });

    });


        </script>
  </head>

<?php
if(isset($_GET['date']))      $today=$_GET['date'];
else      $today=date("y-m-d");//,strtotime(date("y-m-d") . "+1 days"));

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
          <a class="navbar-brand" href="index.php">Commande Pain</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="admin.php">Admin</a></li>
            <li class="active"><a href="show.php">Commandes du jour</a></li>
            <li><a href="delete.php">Suppression</a></li>
            <li><a href="cook.php">Stocks</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container theme-showcase" role="main">
      <div class="row">
        <div class="col-md-12">
          <h3 id="commandesofthe">Commandes du</h3>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="dropdown">

            <select id="dateaffiche" class="btn btn-default dropdown-toggle" type="button" id="date" data-toggle="dropdown" aria-expanded="true">
              <?php  
              //Include des fonctions de date
              include('utils.php');
              // on crée la requête SQL 
              $njour = date('d');
              $jour = datedayfr(date('y-m-d'));
              $mois = datemonthfr(date('Y-m-d'));
              echo "<option value='".date("Y-m-d")."'> $jour $njour $mois";

              $sql = 'SELECT DISTINCT datestamp FROM commande ORDER BY datestamp DESC;'; 
              $req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
              

              // on fait une boucle qui va faire un tour pour chaque enregistrement 
              while($data = mysql_fetch_assoc($req)) 
                {
                  $njour = date('d', strtotime($data['datestamp']));
                  $jour = datedayfr($data['datestamp']);
                  $mois = datemonthfr($data['datestamp']);
                  echo "<option value='".$data['datestamp']."'> $jour $njour $mois";
                }
              ?>
            </select>
          </div>  
        </div>
      </div>      
      
      <div id="insert" class="row">
        <div id="content" class="col-md-12">
          <table id="main" class="table table-striped">
            <thead>
              <tr>
                <th></th>
                <th>Nom</th>
                <th>Emplacement</th>
                <th>Baguettes</th>
                <th>Traditions</th>
                <th>Duchesses</th>
                <th>Croissant</th>
                <th>Pain au Chocolat</th>
                <th>Petit Déjeuner</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              // on crée la requête SQL 

              $sql = 'SELECT nomclient, emplacement, baguette, tradition, croissant, pac, duchesse, petitdejeuner,id,deleted,recuperee FROM commande WHERE datestamp="'.$today.'" AND deleted!=1 ORDER BY recuperee,nomclient;'; 
              $req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 

              // on fait une boucle qui va faire un tour pour chaque enregistrement 
              while($data = mysql_fetch_assoc($req)) 
                  {
                  $deleted=$data['deleted'];
                  if($data['recuperee']==0)
                    echo '<tr><td><button value="'.$data['id'].'" type="button" class="btn btn-success recup" aria-label="Right Align">Récupérée</button></td>';
                  else
                    echo '<tr><td><button value="'.$data['id'].'" type="button" class="btn btn-warning unrecup" aria-label="Right Align">Annuler</button></td>';

                  // on affiche les informations de l'enregistrement en cours 
                  echo '<td>'.$data['nomclient'].'</td>';
                  echo '<td>'.$data['emplacement'].'</td>';
                  echo '<td>'.$data['baguette'].'</td>';
                  echo '<td>'.$data['tradition'].'</td>';
                  echo '<td>'.$data['duchesse'].'</td>';
                  echo '<td>'.$data['croissant'].'</td>';
                  echo '<td>'.$data['pac'].'</td>';
                  echo '<td>'.$data['petitdejeuner'].'</td>';
                }

              $sql = 'SELECT COUNT(*) AS total, SUM(baguette) AS total_bag, SUM(tradition) AS total_trad, SUM(duchesse) AS total_duchesse, SUM(croissant) AS total_croi, SUM(pac) AS total_pac, SUM(petitdejeuner) AS total_ptd FROM commande WHERE deleted!=1 AND datestamp="'.$today.'" ;'; 
              $req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
              $data = mysql_fetch_assoc($req);
              echo '<tr><th></th><th></th><th>Total: </th>';
              echo '<th>'.$data['total_bag'].'</th>';
              echo '<th>'.$data['total_trad'].'</th>';
              echo '<th>'.$data['total_duchesse'].'</th>';
              echo '<th>'.$data['total_croi'].'</th>';
              echo '<th>'.$data['total_pac'].'</th>';
              echo '<th>'.$data['total_ptd'].'</th></tr>';
            

              mysql_close(); 
              ?>
              
            </tbody>
          </table>
        </div>
        </div>

    </div> <!-- /container -->