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
              var $commandeASuppr=$(this).attr('value');
              $.ajax({

                  url: 'del.php?value=0&id='+$commandeASuppr, // Le nom du fichier indiqué dans le formulaire
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
              var $commandeASuppr=$(this).attr('value');
              $.ajax({

                  url: 'del.php?value=1&id='+$commandeASuppr, // Le nom du fichier indiqué dans le formulaire
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
// on se connecte à MySQL 
include('../secure/config.php');
include('utils.php');
$bdd=mysqli_connect($SQLhost, $SQLlogin, $SQLpass, $SQLdb) or die(mysql_error());

$today = date('d-m-Y',strtotime(date("Y-m-d") . "+1 days"));
$tab=array(array());

?> 


  <body role="document">

    <?php
      include('header.php');
    ?>

    <div class="container theme-showcase" role="main">    
      <div id="insert" class="row">
        <div id="content" class="col-md-12">
          <h2> Suppression des commandes pour le <?php echo $today; ?> </h2>
          <table id="main" class="table table-striped">
            <thead>
              <tr>
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
                <th>Supprimé</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              // On récupère tous les articles normaux. Sans les options
              $req = mysqli_query($bdd,'SELECT numorder,name,pitch,nom,taken,deleted,sum(quantity) as quantity FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN article a on a.id=oc.article WHERE date=CURDATE()+1 GROUP BY nom,numorder ORDER BY name;') or die('Erreur SQL !'.mysql_error()); 

              // remplit notre tableau
              while($data = mysqli_fetch_assoc($req)){
                $tab[$data['numorder']][$data['nom']]=$data['quantity'];
                $tab[$data['numorder']]['pitch']=$data['pitch'];
                $tab[$data['numorder']]['name']=$data['name'];
                $tab[$data['numorder']]['taken']=$data['taken'];
                $tab[$data['numorder']]['order']=$data['numorder'];
                $tab[$data['numorder']]['deleted']=$data['deleted'];
              }

              //Et là on récupère nos options. on actualise le tableau ensuite.
              $req = mysqli_query($bdd,'SELECT numorder,name,pitch, sum(quantity) as quantity, choice FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN objet obj on obj.id=oc.choice WHERE date=CURDATE()+1 GROUP BY name,numorder,choice;') or die('Erreur SQL !'.mysql_error()); 
              while($data = mysqli_fetch_assoc($req)){
                if(array_key_exists($data['choice'], $tab[$data['numorder']]))
                  $tab[$data['numorder']][$data['choice']]+=$data['quantity'];
                else
                  $tab[$data['numorder']][$data['choice']]=$data['quantity'];
              }
              //On récup aussi le total des commandes:
              $req = mysqli_query($bdd,'SELECT numorder,sum(quantity*prix) as total FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN article a on a.id=oc.article WHERE date=CURDATE()+1 GROUP BY numorder ORDER BY name;') or die('Erreur SQL !'.mysql_error()); 
              while($data = mysqli_fetch_assoc($req)){
                $tab[$data['numorder']]['total']=$data['total'];
              }

              foreach ($tab as $name => $data) {
                if ($name=="0") {
                  continue;
                }
                echo '<tr>';
                echo '<td>'.$data['name'].'</td>';
                echo '<td>'.$data['pitch'].'</td>';
                foreach ($objet as $obj) {
                  if(array_key_exists ($obj , $data))
                    echo '<td>'.$data[$obj].'</td>';
                  else
                    echo '<td>0</td>';
                }
                echo '<td>'.$data['total'].' €</td>';
                if($data['deleted']==1){
                  echo '<td style = "1"><button value="'.$data['order'].'" type="button" class="btn btn-info undel" aria-label="Right Align">Annuler Suppression</button></td></tr>';
                }
                else
                  echo '<td><button value="'.$data['order'].'" type="button" class="btn btn-warning del" aria-label="Right Align">Supprimer Commande</button></td></tr>';

              }
              echo '</tr>';

              mysqli_close($bdd); 
              ?>
              
            </tbody>
          </table>
        </div>
        </div>

    </div> <!-- /container -->