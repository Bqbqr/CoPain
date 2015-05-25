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

                  url: 'taken.php?id='+$(this).attr('value'), // Le nom du fichier indiqué dans le formulaire
                  success: function(html) { // Je récupère la réponse du fichier PHP
                    $("#insert").load("sho.php?date="+$('#dateaffiche').val()+" #content");
                  },
                  error: function(html){
                    alert(html);
                  }
              });
        });

        $(document).on("click", ".unrecup", function(){
            //On recup le nom du bouton ie le numéro de la commande
              $.ajax({

                  url: 'untaken.php?id='+$(this).attr('value'), // Le nom du fichier indiqué dans le formulaire
                  success: function(html) { // Je récupère la réponse du fichier PHP
                    $("#insert").load("sho.php?date="+$('#dateaffiche').val()+" #content");
                  },
                  error: function(html){
                    alert(html);
                  }
              });
        });

        $('#dateaffiche').change(function(){
          $("#insert").load("sho.php?date="+$(this).val()+" #content");
        });

    });


        </script>
  </head>

<?php

if(isset($_GET['date']))      $today=$_GET['date'];
else      $today=date("Y-m-d");//,strtotime(date("y-m-d") . "+1 days"));

// on se connecte à MySQL 
include('../secure/config.php');
$bdd=mysqli_connect($SQLhost, $SQLlogin, $SQLpass, $SQLdb) or die(mysql_error());
$tab=array(array());
$objets=array();

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
            <li><a href="index.php">Admin</a></li>
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

              $req = mysqli_query($bdd,'SELECT DISTINCT date FROM orders ORDER BY date DESC;') or die('Erreur SQL !<br>'.mysql_error()); 

              // on fait une boucle qui va faire un tour pour chaque enregistrement 
              while($data = mysqli_fetch_assoc($req)) 
                {
                  $njour = date('d', strtotime($data['date']));
                  $jour = datedayfr($data['date']);
                  $mois = datemonthfr($data['date']);
                  echo "<option value='".$data['date']."'> $jour $njour $mois";
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
                <?php
                  $result= mysqli_query($bdd,"SELECT nom FROM article WHERE actif=1 ORDER BY listorder;") or die(mysqli_error($bdd));
                  $i=0;
                  while($data=mysqli_fetch_assoc($result)){  
                    $objet[$i++]=$data['nom'];
                    echo '<th>'.$data['nom'].'</th>';
                  }
                ?>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              // On récupère tous les articles normaux. Sans les options
              $req = mysqli_query($bdd,'SELECT numorder,name,pitch,nom,taken,sum(quantity) as quantity FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN article a on a.id=oc.article WHERE date="'.$today.'" AND deleted=0 GROUP BY nom,pitch ORDER BY name;') or die('Erreur SQL !'.mysql_error()); 

              // remplit notre tableau
              while($data = mysqli_fetch_assoc($req)){
                $tab[$data['numorder']][$data['nom']]=$data['quantity'];
                $tab[$data['numorder']]['pitch']=$data['pitch'];
                $tab[$data['numorder']]['name']=$data['name'];
                $tab[$data['numorder']]['taken']=$data['taken'];
                $tab[$data['numorder']]['order']=$data['numorder'];
              }

              //Et là on récupère nos options. on actualise le tableau ensuite.
              $req = mysqli_query($bdd,'SELECT numorder,name,pitch, sum(quantity) as quantity, choice FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN objet obj on obj.id=oc.choice WHERE date="'.$today.'" GROUP BY name,pitch,choice;') or die('Erreur SQL !'.mysql_error()); 
              while($data = mysqli_fetch_assoc($req)){
                if(array_key_exists($data['choice'], $tab[$data['numorder']]))
                  $tab[$data['numorder']][$data['choice']]+=$data['quantity'];
                else
                  $tab[$data['numorder']][$data['choice']]=$data['quantity'];
              }
              //On récup aussi le total des commandes:
              $req = mysqli_query($bdd,'SELECT numorder,sum(prix) as total FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN article a on a.id=oc.article WHERE date="'.$today.'" AND deleted=0 GROUP BY numorder ORDER BY name;') or die('Erreur SQL !'.mysql_error()); 
              while($data = mysqli_fetch_assoc($req)){
                $tab[$data['numorder']]['total']=$data['total'];
              }

              foreach ($tab as $name => $data) {
                if ($name=="0") {
                  continue;
                }
                echo '<tr>';
                if($data['taken']=='0')
                  echo '<td><button value="'.$data['order'].'" type="button" class="btn btn-success recup" aria-label="Right Align">Récupérée</button></td>';
                else
                  echo '<td><button value="'.$data['order'].'" type="button" class="btn btn-warning unrecup" aria-label="Right Align">Annuler</button></td>';
                echo '<td>'.$data['name'].'</td>';
                echo '<td>'.$data['pitch'].'</td>';
                foreach ($objet as $obj) {
                  if(array_key_exists ($obj , $data))
                    echo '<td>'.$data[$obj].'</td>';
                  else
                    echo '<td>0</td>';
                }
                echo '<td>'.$data['total'].' €</td>';
                echo '</tr>';

              }

              //Faire ici la requête du total.
              echo '</tr>';
            

              mysqli_close($bdd); 
              ?>
              
            </tbody>
          </table>
        </div>
        </div>

    </div> <!-- /container -->