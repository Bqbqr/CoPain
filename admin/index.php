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

      $("#createObject").on("click" , function(){

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
            <li class="active"><a href="index.php">Admin</a></li>
            <li><a href="show.php">Commandes du jour</a></li>
            <li><a href="delete.php">Suppression</a></li>
            <li><a href="cook.php">Stocks</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container theme-showcase" id="main_container" role="main">
      <div class="row" id="main_choices">
        <div class="col-md-2">
        </div>
        <div class="col-md-4">
          <div class="input-group">
            <input type="text" id="objetN" class="form-control" placeholder="Nom de l'objet">
            <span class="input-group-btn">
              <button class="btn btn-default" id="createObject" type="button">Créer l'objet</button>
            </span>
          </div>
          <div class="input-group">
            <input type="number" placeholder="Quantité (Optionnel)" id="objetQ" min="-1" class="form-control">
          </div>
        </div>
        <div class="col-md-2">
        </div>
        <div class="col-md-4">
        </div>
      </div>
    </div> <!-- /container -->