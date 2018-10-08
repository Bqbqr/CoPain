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
          var articleOrder=$("#"+id+"o").val();
          var active;
          if(document.getElementById(id+"box").checked)
            active="1";
          else
            active="0";

          $.ajax({
            type: "GET",
            url: "updateArticle.php?id="+id+"&active="+active+"&articleN="+articleN+"&articleImg="+articleImg+"&articleP="+articleP+"&articleO="+articleOrder,
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

// on se connecte à mysqli 
include('../secure/config.php');
$db=mysqli_connect($SQLhost, $SQLlogin, $SQLpass, $SQLdb) or die(mysqli_error());

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
              $sql = 'SELECT o.id as id, oa.article AS article, stock, objet FROM article a INNER JOIN objetsInArticle oa on a.id=oa.article INNER JOIN objet o on o.id=oa.objet WHERE actif=1'; 
              $req = mysqli_query($db,$sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysqli_error()); 

              // on fait une boucle qui va faire un tour pour chaque enregistrement 
              while($data = mysqli_fetch_assoc($req)){
                $tmp=str_replace(' ', '_', $data['id']);
                echo '<div class="input-group">';
                echo '<span class="input-group-addon labelobjet">'.$data['id'].'</span>';
                echo '<input type="number" min="-1" class="form-control" id="'.$tmp.'" value="'.$data['stock'].'">';
                echo '<span class="input-group-btn"><button value="'.$tmp.'" class="btn btn-default updatestock" type="button">MàJ Stock!</button></span>';

                echo '</div>';
                }
              ?>

          </div>
          
        </div>
        <div class="col-md-6">
        </div>
      </div>
    </div> <!-- /container -->