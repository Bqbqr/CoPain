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
        $(document).on("click", ".glyphicon-unchecked", function(){
            //On recup le nom du bouton ie le numéro de la commande
              $.ajax({

                  url: 'recup.php?id='+$(this).attr('value'), // Le nom du fichier indiqué dans le formulaire
                  success: function(html) { // Je récupère la réponse du fichier PHP
                    $("#insert").load("admin.php?date="+$('#dateaffiche').val()+" #content");
                    $(".jumbotron").load("admin.php?date="+$('#dateaffiche').val()+" #resume");
                  },
                  error: function(html){
                    alert(html);
                  }
              });
        });

        $(document).on("click", ".glyphicon-check", function(){
            //On recup le nom du bouton ie le numéro de la commande
              $.ajax({

                  url: 'annulrecup.php?id='+$(this).attr('value'), // Le nom du fichier indiqué dans le formulaire
                  success: function(html) { // Je récupère la réponse du fichier PHP
                    $("#insert").load("admin.php?date="+$('#dateaffiche').val()+" #content");
                    $(".jumbotron").load("admin.php?date="+$('#dateaffiche').val()+" #resume");
                  },
                  error: function(html){
                    alert(html);
                  }
              });
        });

        $(document).on("click", "#valid", function(){
            //On recup le nom du bouton ie le numéro de la commande
            var test=$("#pac").val();

              $.ajax({
                  url: 'updateCuisson.php?bag='+$('#bag').val()+'&trad='+$('#trad').val()+'&duchesse='+$('#duchesse').val()+'&croissant='+$('#croissant').val()+'&pac='+$('#pac').val(), // Le nom du fichier indiqué dans le formulaire
                  success: function(html) { // Je récupère la réponse du fichier PHP
                    $("#insert").load("admin.php?date="+$('#dateaffiche').val()+" #content");
                    $(".jumbotron").load("admin.php?date="+$('#dateaffiche').val()+" #resume");
                    // Debug alert(html);

                  },
                  error: function(html){
                    alert(html);
                  }
              });
        });

        $(document).on("click", "#raz", function(){
            //On recup le nom du bouton ie le numéro de la commande
              $.ajax({

                  url: 'init.php', // Le nom du fichier indiqué dans le formulaire
                  success: function(html) { // Je récupère la réponse du fichier PHP
                    $("#insert").load("admin.php?date="+$('#dateaffiche').val()+" #content");
                    $(".jumbotron").load("admin.php?date="+$('#dateaffiche').val()+" #resume");
                    // Debug alert(html);

                  },
                  error: function(html){
                    alert(html);
                  }
              });
        });


        $('#dateaffiche').change(function(){
          $("#insert").load("admin.php?date="+$(this).val()+" #content");
          $(".jumbotron").load("admin.php?date="+$(this).value()+" #resume");
        });

        $(document).on("click", ".qtyplus", function(){
            // Get the field name
            fieldName = $(this).attr('field');
            // Get its current value
            var currentVal = parseInt($('input[name='+fieldName+']').val());
            // If is not undefined
            if (!isNaN(currentVal)) {
                // Increment
                $('input[name='+fieldName+']').val(currentVal + parseInt($(this).attr('inc')) );
            } else {
                // Otherwise put a 0 there
                $('input[name='+fieldName+']').val(0);
            }
        });

        $(document).on("click", ".qtyminus", function(){
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
        $(document).on("click", ".undel", function(){
            //On recup le nom du bouton ie le numéro de la commande
            if (confirm("Annuler la suppression pour "+$(this).attr('value')+"?")) { 
              var $commandeASuppr=$(this).attr('name');
              $.ajax({

                  url: 'undel.php?id='+$commandeASuppr, // Le nom du fichier indiqué dans le formulaire
                  success: function(html) { // Je récupère la réponse du fichier PHP
                    $(".jumbotron").load("admin.php?date="+$('#dateaffiche').val()+" #resume");
                    $("#insert").load("admin.php?date="+$('#dateaffiche').val()+" #content");
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
                    $(".jumbotron").load("admin.php?date="+$('#dateaffiche').val()+" #resume");
                    $("#insert").load("admin.php?date="+$('#dateaffiche').val()+" #content");
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
if(isset($_GET['date']))      $today=$_GET['date'];
else      $today=date("y-m-d");

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
            <li class="active"><a href="admin.php">Admin</a></li>
            <li><a href="show.php">Commandes du jour</a></li>
            <li><a href="delete.php">Suppression</a></li>
            <li><a href="cook.php">Stocks</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container theme-showcase" role="main">
      <div class="jumbotron">
        <div id="resume"  class="row">
          <div class="col-md-12">

          <table class="table table-striped">
            <thead>
              <tr>
                <th></th>
                <th>Total</th>
                <th>Clients Restants</th>
                <th>Stock</th>
                <th>Restant à cuire</th>
                <th>Nouvelle fournée</th>
              </tr>
            </thead>
            <tr>
              <td>Commandes</td>
              <?php
                $sql1 = 'SELECT COUNT(*) AS total, SUM(baguette) AS total_bag, SUM(tradition) AS total_trad, SUM(duchesse) AS total_duchesse, SUM(croissant) AS total_croi, SUM(pac) AS total_pac, SUM(petitdejeuner) AS total_ptd FROM commande WHERE deleted!=1 AND datestamp="'.$today.'" ;'; 
                $req1 = mysql_query($sql1) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
                $data1 = mysql_fetch_assoc($req1);

                //Total qu'il reste
                $sql2 = 'SELECT COUNT(*) AS total, SUM(baguette) AS total_bag, SUM(tradition) AS total_trad, SUM(croissant) AS total_croi, SUM(duchesse) AS total_duchesse, SUM(pac) AS total_pac, SUM(petitdejeuner) AS total_ptd FROM commande WHERE datestamp="'.$today.'" AND deleted!=1 AND recuperee != 1;'; 
                $req2 = mysql_query($sql2) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
                $data2 = mysql_fetch_assoc($req2);

                //Total qu'il reste
                $sql3 = 'SELECT baguette,tradition,duchesse,croissant,pac FROM commande WHERE id=1;'; 
                $req3 = mysql_query($sql3) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
                $data3 = mysql_fetch_assoc($req3);

                echo '<td>'.$data1['total'].'</td>';
                echo '<td>'.$data2['total'].'</td>';
                echo '<td></td><td></td><td></td></tr><tr>';

                //Baguettes
                echo '<td>Baguettes</td>';
                echo '<td>'.$data1['total_bag'].'</td>';
                echo '<td>'.$data2['total_bag'].'</td>';
                echo '<th>'.($data2['total_bag']-$data1['total_bag']+$data3['baguette']).'</th>';
                echo '<th>'.($data1['total_bag']-$data3['baguette']).'</th>';
                echo '<td>
                      <input type="button" value="-" class="btn btn-default qtyminus" field="bag" />
                      <input name="bag" type="text" id="bag" value="0" class="qty" readonly/>
                      <input type="button" value="+" inc="1" class="btn btn-default qtyplus" field="bag" />
                      <input type="button" value="+6" inc="6" class="btn btn-default qtyplus" field="bag" />
                    </td>';
                echo '</tr><tr>';

                echo '<td>Traditions</td>';
                echo '<td>'.$data1['total_trad'].'</td>';
                echo '<td>'.$data2['total_trad'].'</td>';
                echo '<th>'.($data2['total_trad']-$data1['total_trad']+$data3['tradition']).'</th>';
                echo '<th>'.($data1['total_trad']-$data3['tradition']).'</th>';
                echo '<td>
                      <input type="button" value="-" class="btn btn-default qtyminus" field="trad" />
                      <input name="trad" type="text" id="trad" value="0" class="qty" readonly/>
                      <input type="button" value="+" inc="1" class="btn btn-default qtyplus" field="trad" />
                      <input type="button" value="+6" inc="6" class="btn btn-default qtyplus" field="trad" />
                    </td>';
                echo '</tr><tr>';

                echo '<td>Duchesses</td>';
                echo '<td>'.$data1['total_duchesse'].'</td>';
                echo '<td>'.$data2['total_duchesse'].'</td>';
                echo '<th>'.($data2['total_duchesse']-$data1['total_duchesse']+$data3['duchesse']).'</th>';
                echo '<th>'.($data1['total_duchesse']-$data3['duchesse']).'</th>';
                echo '<td>
                      <input type="button" value="-" class="btn btn-default qtyminus" field="duchesse" />
                      <input name="duchesse" type="text" id="duchesse" value="0" class="qty" readonly/>
                      <input type="button" value="+" inc="1" class="btn btn-default qtyplus" field="duchesse" />
                      <input type="button" value="+6" inc="6" class="btn btn-default qtyplus" field="duchesse" />
                    </td>';
                echo '</tr><tr>';

                //Croissants
                echo '<td>Croissants</td>';
                echo '<td>'.$data1['total_croi'].'</td>';
                echo '<td>'.$data2['total_croi'].'</td>';
                echo '<th>'.($data2['total_croi']-$data1['total_croi']+$data3['croissant']).'</th>';
                echo '<th>'.($data1['total_croi']-$data3['croissant']).'</th>';
                echo '<td>
                      <input type="button" value="-" class="btn btn-default qtyminus" field="croissant" />
                      <input name="croissant" type="text" id="croissant" value="0" class="qty" readonly/>
                      <input type="button" value="+" inc="1" class="btn btn-default qtyplus" field="croissant" />
                      <input type="button" value="+6" inc="6" class="btn btn-default qtyplus" field="croissant" />
                    </td>';
                echo '</tr><tr>';

                //PainAuChoc
                echo '<td>PainAuChoc</td>';
                echo '<td>'.$data1['total_pac'].'</td>';
                echo '<td>'.$data2['total_pac'].'</td>';
                echo '<th>'.($data2['total_pac']-$data1['total_pac']+$data3['pac']).'</th>';
                echo '<th>'.($data1['total_pac']-$data3['pac']).'</th>';
                echo '<td>
                      <input type="button" value="-" class="btn btn-default qtyminus" field="pac" />
                      <input name="pac" type="text" id="pac" value="0" class="qty" readonly/>
                      <input type="button" value="+" inc="1" class="btn btn-default qtyplus" field="pac" />
                      <input type="button" value="+6" inc="6" class="btn btn-default qtyplus" field="pac" />
                    </td>';
                echo '</tr><tr>';

                //PetitDéj
                echo '<td>PetitDéj</td>';
                echo '<td>'.$data1['total_ptd'].'</td>';
                echo '<td>'.$data2['total_ptd'].'</td>';
                echo '<td></td><td></td></tr>';

              ?>
          </table>
          <input type="button" value="Valider" id="valid" class="btn btn-default"/>
          <input type="button" value="Réinitialiser" id="raz" class="btn btn-default"/>
          </div>
        </div>
      </div>

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
              // on crée la requête SQL 
              $sql = 'SELECT DISTINCT datestamp FROM commande /* WHERE datestamp != curdate()*/ ORDER BY datestamp DESC;'; 
              $req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 

              // on fait une boucle qui va faire un tour pour chaque enregistrement 
              while($data = mysql_fetch_assoc($req)) 
                {
                  echo '<option>'.$data['datestamp'];
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
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php 
              // on crée la requête SQL 

              $sql = 'SELECT nomclient, emplacement, baguette, tradition, croissant, pac, duchesse, petitdejeuner,id,deleted,recuperee FROM commande WHERE datestamp="'.$today.'" ORDER BY recuperee,nomclient;'; 
              //echo $sql;
              // on envoie la requête 
              $req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 

              // on fait une boucle qui va faire un tour pour chaque enregistrement 
              while($data = mysql_fetch_assoc($req)) 
                  {
                  $deleted=$data['deleted'];
                  if($data['recuperee']==1)
                    echo '<tr><td><span value="'.$data['id'].'"class="glyphicon glyphicon-check" style="color:red;" aria-hidden="true"></span></td>';
                  else
                    echo '<tr><td><span value="'.$data['id'].'"class="glyphicon glyphicon-unchecked" style="color:green;" aria-hidden="true"></span></td>';

                  // on affiche les informations de l'enregistrement en cours 
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