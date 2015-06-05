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

      $(document).on("click", "#createObject", function(){

        var name=$('#objetN').val();
        var qty=$('#objetQ').val();
        if(qty=="") qty=-1;

        if(name == '') {
            alert("Merci de donner un nom à l\'objet");
            return;
        }

        $.ajax({
          type: "GET",
          url: "addObjet.php?objetN="+name+"&objetQ="+qty,
          success: function(html) {
            $("#main_container").load("index.php #main_choices");
          },
          error: function(html){
            alert(html);
          }
        });

      });

      $(document).on("click", "#createlink", function(){


        var e = document.getElementById("linkselo");
        var obj = e.options[e.selectedIndex].value;

        var e = document.getElementById("linksela");
        var article = e.options[e.selectedIndex].value;

        var qty=$('#lienq').val();
        if(qty=="") qty=1;

        $.ajax({
          type: "GET",
          url: "addLink.php?article="+article+"&objet="+obj+"&qty="+qty,
          success: function(html) {
            //alert(html);
            $("#main_container").load("index.php #main_choices");
          },
          error: function(html){
            alert(html);
          }
        });

      });

      $(document).on("click", ".updatestock", function(){

        var name=$(this).val();
        var qty=$("#"+name).val();
        name=name.split('_').join(' ');
        if(qty=="") qty=-1;

        $.ajax({
          type: "GET",
          url: "updateStock.php?objetN="+name+"&objetQ="+qty,
          success: function(html) {
            $("#main_container").load("index.php #main_choices");
            //Debug alert(html);
          },
          error: function(html){
            alert(html);
          }
        });

      });

      $(document).on("click", "#createArticle", function(){

        var name=$('#articleN').val();
        var price=$('#articleP').val();
        var img=$('#articleImg').val();
        
        if(name == '') {
            alert("Donner un nom à l'article SVP");
            return;
        }

        if(price == '') {
            alert("Donner un prix à l'article SVP");
            return;
        }

        $.ajax({
          type: "GET",
          url: "addArticle.php?articleN="+name+"&articleP="+price+"&articleImg="+img,
          success: function(html) {
            $("#main_container").load("index.php #main_choices");
          },
          error: function(html){
            alert(html);
          }
        });
      });

      $(document).on("click", ".maj", function(){
          //Un peu complexe...
          var id=$(this).attr('field');
          var articleN=$("#"+id+"name").val();
          var articleP=$("#"+id+"p").val();
          var articleImg=$("#"+id+"img").val();
          var active;
          if(document.getElementById(id+"box").checked)
            active="1";
          else
            active="0";

          $.ajax({
            type: "GET",
            url: "updateArticle.php?id="+id+"&active="+active+"&articleN="+articleN+"&articleImg="+articleImg+"&articleP="+articleP,
            success: function(html) {
              $("#main_container").load("index.php #main_choices");
              //alert(html);
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
if(isset($_GET['date']))      $today=$_GET['date'];
else      $today=date("y-m-d");

// on se connecte à MySQL 
include('../secure/config.php');
$db=mysql_connect($SQLhost, $SQLlogin, $SQLpass) or die(mysql_error());

// on sélectionne la base 
mysql_select_db('pain',$db); 

?> 


  <body role="document">

    <?php
      include('header.php');
    ?>

    <div class="container theme-showcase" id="main_container" role="main">
      <div class="row" id="main_choices">
        <div class="col-md-6">
          <div class="btn-group-vertical" role="group">
            <h4>Stock des produits</h4>
            <span class="label label-info">-1 = Pas de limite, 0 = plus de stock</span></br>

            <?php 
              $sql = 'SELECT id,stock FROM objet;'; 
              $req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 

              // on fait une boucle qui va faire un tour pour chaque enregistrement 
              while($data = mysql_fetch_assoc($req)){
                $tmp=str_replace(' ', '_', $data['id']);
                echo '<div class="input-group">';
                echo '<input type="number" min="-1" class="form-control" id="'.$tmp.'" value="'.$data['stock'].'">';
                echo '<span class="input-group-addon labelobjet">'.$data['id'].'</span>';
                echo '<span class="input-group-btn"><button value="'.$tmp.'" class="btn btn-default updatestock" type="button">MàJ Stock!</button></span>';

                echo '</div>';
                }
              ?>

          </div>

          <h4>Ajout d'un produit</h4>

          <div class="input-group">
            <input type="text" id="objetN" class="form-control" placeholder="Nom de l'objet">
            <span class="input-group-btn">
              <button class="btn btn-default" id="createObject" type="button">Créer l'objet</button>
            </span>
          </div>
          <div class="input-group">
            <input type="number" placeholder="Quantité (Optionnel)" id="objetQ" min="-1" class="form-control">
          </div>

          <hr>


          <div class="btn-group-vertical" role="group">
            <h4>Liens produits-article</h4>

            <?php 
              $sql = 'SELECT article.nom as nom, objet, quantity FROM article, objetsInArticle WHERE article.id=objetsInArticle.article;'; 
              $req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
              //Affichage des liens produit article.
              while($data = mysql_fetch_assoc($req)){
                echo '<div class="input-group">';
                echo '<input type="number" min="-1" class="form-control" id="'.$tmp.'" value="'.$data['quantity'].'">';
                echo '<span class="input-group-addon labellink">'.$data['objet'].'</span>';
                echo '<span class="input-group-addon labellink">'.$data['nom'].'</span>';
                echo '<span class="input-group-btn"><button value="'.$data['nom'].'" class="btn btn-default updateqty" type="button">MàJ</button></span>';
                echo '</div>';
                }
              ?>

          </div>
          <h4>Ajouter une relation produit-article</h4>
          <span class="label label-info">-1 = Au choix, utiliser des décimaux pour affiner</span></br>
          <span class="label label-info">Ex: 1 baguette pour 2 petit déj = 0,5/Peti Déj</span></br>

          <select class="form-control" id="linkselo">
          <?php 
            $sql = 'SELECT id FROM objet;'; 
            $req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
            //Affichage des liens produit article.
            while($data = mysql_fetch_assoc($req)){
              echo '<option value="'.$data['id'].'">'.$data['id'].'</option>';
              }
            ?>
          </select>

          <select class="form-control" id="linksela">
          <?php 
            $sql = 'SELECT id,nom FROM article;'; 
            $req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
            //Affichage des liens produit article.
            while($data = mysql_fetch_assoc($req)){
              echo '<option value="'.$data['id'].'">'.$data['nom'].'</option>';
              }
            ?>
          </select>
          <div class="input-group">
            <input type="number" min="-1" id="lienq" class="form-control" value="1" placeholder="Quantité">
            <span class="input-group-btn">
              <button class="btn btn-default" id="createlink" type="button">Créer le lien</button>
            </span>
          </div>
        
        </div>
        <div class="col-md-6">

          <div class="btn-group-vertical" role="group">
            <h4>Articles en vente</h4>
            <span class="label label-info">N'oubliez pas d'ajouter des objets à vos articles!</span></br>

            <?php 
              $sql = 'SELECT id,nom,prix,img,actif FROM article;'; 
              $req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 

              // on fait une boucle qui va faire un tour pour chaque enregistrement 
              while($data = mysql_fetch_assoc($req)){
                echo '<div class="input-group">';
                $tmp=str_replace(' ', '_', $data['id']);
                if($data['actif']=="1")
                  echo '<span class="input-group-addon"><input class="activebox" id="'.$tmp.'box" type="checkbox" aria-label=" Actif" checked>Actif</span>';
                else
                  echo '<span class="input-group-addon"><input class="activebox" id="'.$tmp.'box" type="checkbox"  aria-label=" Actif">Actif</span>';

                echo '<span class="input-group-addon" id="sizing-addon2"><a img" class="thumbnail thumbadmin"><img src="../img/'.$data['img'].'" alt="'.$data['nom'].'"></a></span>';
                echo '<input type="text" id="'.$tmp.'name" class="form-control" value="'.$data['nom'].'" aria-describedby="sizing-addon2">';
                echo '<input type="text" id="'.$tmp.'img" class="form-control" value="'.$data['img'].'"><div class="input-group">';
                echo '<input type="text" id="'.$tmp.'p" class="form-control" value="'.$data['prix'].'" aria-describedby="sizing-addon2">';
                echo '<span class="input-group-addon" id="basic-addon2">€</span></div>';
                echo '<button field="'.$tmp.'" class="btn btn-default maj" type="button">Mettre à jour</button>';
                echo '</div><p></p>';
                }
                mysql_close(); 
              ?>

          </div>

          <h4>Ajout d'un Article</h4>

          <div class="input-group-vertical">
            <input type="text" id="articleN" class="form-control" placeholder="Nom de l'objet">
            <input type="number" step="0.05" id="articleP" class="form-control" placeholder="Prix">
            <input type="text" id="articleImg" class="form-control" placeholder="Nom de l'image">
            <span class="input-group-btn">
              <button class="btn btn-default" id="createArticle" type="button">Créer l'objet</button>
            </span>
          </div>
        </div>
      </div>
    </div> <!-- /container -->