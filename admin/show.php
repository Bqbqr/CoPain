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
    <link rel="stylesheet" type="text/css" href="../css/print.css" media="print" />
    <link href="../css/bootstrap-theme.min.css" rel="stylesheet">

    <script src="../js/jquery-1.11.2.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>

    <script type="text/javascript">

    $(document).ready(function() {

        $(document).on("click", ".recup", function(){
            //On recup le nom du bouton ie le numéro de la commande
              $.ajax({

                  url: 'taken.php?id='+$(this).attr('value'), // Le nom du fichier indiqué dans le formulaire
                  success: function(html) {
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

                  url: 'untaken.php?id='+$(this).attr('value'), // Le nom du fichier indiqué dans le formulaire
                  success: function(html) {
                    $("#insert").load("show.php?date="+$('#dateaffiche').val()+" #content");
                  },
                  error: function(html){
                    alert(html);
                  }
              });
        });

    });


        </script>
  </head>

<?php
// on se connecte à MySQL 
include('../secure/config.php');
include('utils.php');
$bdd=mysqli_connect($SQLhost, $SQLlogin, $SQLpass, $SQLdb) or die(mysql_error());
$tab=array(array());
$objets=array();

?> 


  <body role="document">

    <?php
      include('header.php');
    ?>

    <div class="container theme-showcase" role="main">
      <div class="row">
        <div class="col-md-12">
          <h3 id="commandesofthe">Commandes du <?php echo(date_fr()); ?> </h3>
          <a href="javascript:window.print()" class="print" >Imprimer cette page</a>
        </div>
      </div>
     
      
      <div id="insert" class="row">
        <div id="content" class="col-md-12">
          <table id="main" class="table fixed_headers table-striped header-fixed">
            <thead>
              <tr>
                <th>Etat</th>
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
                <th>Prix</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              // On récupère tous les articles normaux. Sans les options
              $req = mysqli_query($bdd,'SELECT numorder,name,pitch,nom,taken,sum(quantity) as quantity FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN article a on a.id=oc.article WHERE date=CURDATE() AND deleted=0 GROUP BY nom,numorder ORDER BY name;') or die('Erreur SQL !'.mysql_error()); 

              // remplit notre tableau
              while($data = mysqli_fetch_assoc($req)){
                $tab[$data['numorder']][$data['nom']]=$data['quantity'];
                $tab[$data['numorder']]['pitch']=$data['pitch'];
                $tab[$data['numorder']]['name']=$data['name'];
                $tab[$data['numorder']]['taken']=$data['taken'];
                $tab[$data['numorder']]['order']=$data['numorder'];
              }

              //Et là on récupère nos options. on actualise le tableau ensuite.
              $req = mysqli_query($bdd,'SELECT numorder,name,pitch, sum(quantity) as quantity, choice FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN objet obj on obj.id=oc.choice WHERE date=CURDATE() GROUP BY name,numorder,choice;') or die('Erreur SQL !'.mysql_error()); 
              while($data = mysqli_fetch_assoc($req)){
                if(array_key_exists($data['choice'], $tab[$data['numorder']]))
                  $tab[$data['numorder']][$data['choice']]+=$data['quantity'];
                else
                  $tab[$data['numorder']][$data['choice']]=$data['quantity'];
              }
              //On récup aussi le total des commandes:
              $req = mysqli_query($bdd,'SELECT numorder,sum(quantity*prix) as total FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN article a on a.id=oc.article WHERE date=CURDATE() AND deleted=0 GROUP BY numorder ORDER BY name;') or die('Erreur SQL !'.mysql_error()); 
              while($data = mysqli_fetch_assoc($req)){
                $tab[$data['numorder']]['total']=$data['total'];
              }

              //Taken. C'EST PAS PROPRE DU TOUT.
              foreach ($tab as $name => $data) {
                if ($name=="0" || $data["taken"]==1) {
                  continue;
                }
                echo '<tr>';
                echo '<td><button value="'.$data['order'].'" type="button" class="btn btn-success recup" aria-label="Right Align">Vendue</button></td>';
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
              //Not taken
              foreach ($tab as $name => $data) {
                if ($name=="0"  || $data["taken"]==0) {
                  continue;
                }
                echo '<tr>';
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
              echo '</tr>';

              //REQUETE DE LA MORT QUI TUE
              $req = mysqli_query($bdd,'select sum(qty) as total, nom FROM (
                                          SELECT sum(quantity) as qty, choice as nom FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN objet obj on obj.id=oc.choice WHERE date=CURDATE() GROUP BY name,pitch,choice
                                          UNION ALL
                                          SELECT sum(quantity) as qty, nom FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN article a on a.id=oc.article WHERE date=CURDATE() AND deleted=0 GROUP BY nom
                                        ) s GROUP BY nom;') or die('Erreur SQL !'.mysql_error()); 

              while($data = mysqli_fetch_assoc($req)){
                $tab['total'][$data['nom']]=$data['total'];
              }
              echo '<tr><td></td><td></td><td>Total:</td>';
              foreach ($objet as $obj) {
                if(array_key_exists ($obj , $tab['total']))
                  echo '<td>'.$tab['total'][$obj].'</td>';
                else
                  echo '<td>0</td>';
              }
              echo '<td></td></tr>';

              //REQUETE DE LA MORT QUI TUE
              $req = mysqli_query($bdd,'select sum(qty) as total, nom FROM (
                                          SELECT sum(quantity) as qty, choice as nom FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN objet obj on obj.id=oc.choice WHERE date=CURDATE() AND taken=0 AND deleted=0 GROUP BY name,pitch,choice
                                          UNION ALL
                                          SELECT sum(quantity) as qty, nom FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN article a on a.id=oc.article WHERE date=CURDATE() AND deleted=0 AND taken=0 GROUP BY nom
                                        ) s GROUP BY nom;') or die('Erreur SQL !'.mysql_error()); 

              while($data = mysqli_fetch_assoc($req)){
                $tab['restant'][$data['nom']]=$data['total'];
              }
              echo '<tr><td></td><td></td><td>Restant:</td>';
              foreach ($objet as $obj) {
                if(array_key_exists ($obj , $tab['restant']))
                  echo '<td>'.$tab['restant'][$obj].'</td>';
                else
                  echo '<td>0</td>';
              }
              echo '<td></td></tr>';


              mysqli_close($bdd); 
              ?>
              
            </tbody>
          </table>
        </div>
        </div>

    </div> <!-- /container -->