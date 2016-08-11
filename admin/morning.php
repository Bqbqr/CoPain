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
    <link rel="stylesheet" type="text/css" href="../css/component.css" />
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-throttle-debounce/1.1/jquery.ba-throttle-debounce.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>

    <script type="text/javascript">

    $(document).ready(function() {
        $(document).on("click", ".redo", function(){
            //On recup le nom du bouton ie le numéro de la commande
              $.ajax({

                  url: 'redo.php?numorder='+$(this).attr('value')+"&pitch="+$(this).attr('pitch')+"&name="+$(this).attr('name'), 
                  success: function(html) {
                    alert(html);
                  },
                  error: function(html){
                    alert("!! Erreur !!\n, vérifiez votre connection internet et réessayez!");
                  }
              });
        });

        $(document).on("click", ".clicksee", function(){
            //On recup le nom du bouton ie le numéro de la commande
            $.ajax({
                url: 'taken.php?id='+$(this).attr('value'),
                success: function(html) {
                  location.reload();
                },
                error: function(html){
                  alert(html);
                }
            });
        });

        $(document).on("click", ".unrecup", function(){
            //On recup le nom du bouton ie le numéro de la commande
              $.ajax({

                  url: 'untaken.php?id='+$(this).attr('value'),
                  success: function(html) {
                    //$("#insert").load("show.php #content");
                    location.reload();
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
    // Connection to mysql
    include('../secure/config.php');
    include('utils.php');
    $bdd=mysqli_connect($SQLhost, $SQLlogin, $SQLpass, $SQLdb) or die(mysql_error());
    //Tab with all the data from the orders and the customers
    $tab=array(array());
    //List of objects solds
    $objets=array();
  ?> 


  <body role="document">
    <?php include('header.php'); ?>
    <div class="container theme-showcase" role="main">
      <div class="row">
        <div class="col-md-12">
          <h3 id="commandesofthe">Commandes du <?php echo(date_fr()); ?> </h3>
         
          <a href="javascript:window.print()" class="print" >Imprimer cette page</a>
        </div>
      </div>
            
      <div id="insert" class="row">
        <div id="content" class="col-md-12 component">
          <table id="total" class="table table-striped">
            <thead>
              <tr>
                <th>-</th>
                <?php
                  $result= mysqli_query($bdd,"SELECT nom FROM article WHERE actif=1 ORDER BY listorder;") or die(mysqli_error($bdd));
                  $i=0;
                  while($testdata=mysqli_fetch_assoc($result)){  
                    $objet[$i++]=$testdata['nom'];
                    echo '<th>'.$testdata['nom'].'</th>';
                  }
                ?>
              </tr>
            </thead>
            <tbody>
               <?php 

                //REQUETE DE LA MORT QUI TUE
                $req = mysqli_query($bdd,'select sum(qty) as total, nom FROM (
                                            SELECT sum(quantity) as qty, choice as nom FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN objet obj on obj.id=oc.choice WHERE date=CURDATE() GROUP BY name,pitch,choice
                                            UNION ALL
                                            SELECT sum(quantity) as qty, nom FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN article a on a.id=oc.article WHERE date=CURDATE() AND deleted=0 GROUP BY nom
                                          ) s GROUP BY nom;') or die('Erreur SQL !'.mysql_error());  

                while($testdata = mysqli_fetch_assoc($req)){
                  $tab['total'][$testdata['nom']]=$testdata['total'];
                }
                echo '<tr><td>Total:</td>';
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

                while($testdata = mysqli_fetch_assoc($req)){
                  $tab['restant'][$testdata['nom']]=$testdata['total'];
                }
                echo '<tr><td>Restant:</td>';
                
                foreach ($objet as $obj) {
                  if(array_key_exists("restant", $tab) && array_key_exists ($obj , $tab['restant']))
                    echo '<td>'.$tab['restant'][$obj].'</td>';
                  else
                    echo '<td>0</td>';
                }
          ?>
            </tbody>
          </table>
          <table id="main" class="table table-striped">
            <thead>
              <tr>
                <th>Etat</th>
                <th>Nom</th>
                <th>Emplacement</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              //Request to get data for tab 
              $tab=array(array());
              $result= mysqli_query($bdd,"SELECT nom FROM article WHERE actif=1 ORDER BY listorder;") or die(mysqli_error($bdd));
              $i=0;
              while($data=mysqli_fetch_assoc($result)){  
                $objet[$i++]=$data['nom'];
              }
              // On récupère tous les articles normaux. Sans les options
              $req = mysqli_query($bdd,'SELECT numorder,name,pitch,nom,taken,sum(quantity) as quantity FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN article a on a.id=oc.article WHERE date=CURDATE() AND deleted=0 GROUP BY nom,numorder ORDER BY name;') or die('Erreur SQL !'.mysql_error()); 

              // Si aucun article, on s'arrête là, aucune commande pour le jour même.
              if(mysqli_num_rows($req)){  
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
              // Listing des commandes non récupérées
              foreach ($tab as $name => $data) {
                  if ($name=="0" || $data["taken"]==1) {
                    continue;
                  }
                  echo '<tr>';
                  echo '<td><button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal'.$data['order'].'">Voir la commande</button></td>';
                  echo '<td>'.$data['name'].'</td>';
                  echo '<td>'.$data['pitch'].'</td>';
                  /* Gestion des popups */
                  echo '<!-- Modal -->';
                  echo '<div id="myModal'.$data['order'].'" class="modal fade" data-dismiss="false" role="dialog">';
                  echo '  <div class="modal-dialog">';
                  echo '    <!-- Modal content-->';
                  echo '    <div class="modal-content">';
                  echo '      <div class="modal-header">';
                  echo '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
                  echo '        <h4 class="modal-title">Commande de '.$data['name'].', '.$data['pitch'].'</h4>';
                  echo '      </div>';
                  echo '      <div class="modal-body">';
                  /* Affichage de la commande */
                  echo '<p>';
                  foreach ($objet as $obj) {
                    if(array_key_exists ($obj , $data))
                      echo $data[$obj].' '.$obj.'</br>';
                  }
                  echo 'Total: '.$data['total'].' €</p>';
                  /* Fin */
                  echo '      </div>';
                  echo '      <div class="modal-footer">';
                  echo '        <button value="'.$data['order'].'" name='.$data['name'].' pitch='.$data['pitch'].'  type="button" class="btn btn-default redo clicksee" style="float:left" data-dismiss="modal">Recommander et valider</button>';
                  echo '        <button value="'.$data['order'].'"  type="button" class="btn btn-default clicksee" data-dismiss="modal">Valider</button>';
                  echo '      </div>';
                  echo '    </div>';
                  echo '  </div>';
                  echo '</div>';
                  /* fin de gestion des popups */
                  //echo '<td><button value="'.$data['order'].'" name='.$data['name'].' pitch='.$data['pitch'].' type="button" class="btn btn-info redo"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span></button></td>';

                  echo '</tr>';

                }
                // Listing des commandes récupérées
                foreach ($tab as $name => $data) {
                  if ($name=="0" || $data["taken"]==0) {
                    continue;
                  }
                  echo '<tr>';
                  echo '<td><button type="button" class="btn" data-toggle="modal" data-target="#myModal'.$data['order'].'">Voir la commande</button></td>';
                  echo '<td>'.$data['name'].'</td>';
                  echo '<td>'.$data['pitch'].'</td>';
                  /* Gestion des popups */
                  echo '<!-- Modal -->';
                  echo '<div id="myModal'.$data['order'].'" class="modal fade" role="dialog">';
                  echo '  <div class="modal-dialog">';
                  echo '    <!-- Modal content-->';
                  echo '    <div class="modal-content">';
                  echo '      <div class="modal-header">';
                  echo '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
                  echo '        <h4 class="modal-title">Commande de '.$data['name'].','.$data['pitch'].'</h4>';
                  echo '      </div>';
                  echo '      <div class="modal-body">';
                  /* Affichage de la commande */
                  echo '<p>';
                  foreach ($objet as $obj) {
                    if(array_key_exists ($obj , $data))
                      echo $data[$obj].' '.$obj.'</br>';
                  }
                  echo 'Total: '.$data['total'].' €</p>';
                  /* Fin */
                  echo '      </div>';
                  echo '      <div class="modal-footer">';
                  echo '        <button value="'.$data['order'].'"  type="button" class="btn btn-default unrecup" data-dismiss="modal">Annuler la récupération</button>';
                  echo '      </div>';
                  echo '    </div>';
                  echo '  </div>';
                  echo '</div>';
                  /* fin de gestion des popups */
                  //echo '<td><button value="'.$data['order'].'" name='.$data['name'].' pitch='.$data['pitch'].' type="button" class="btn btn-info redo"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span></button></td>';

                  echo '</tr>';

                }
                echo '<td></td></tr>';
              }
              mysqli_close($bdd); 
              ?>
              
            </tbody>
          </table>
        </div>
        </div>

    </div> <!-- /container -->