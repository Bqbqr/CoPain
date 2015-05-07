<?php 

// On commence par récupérer les champs 
if(isset($_GET['bag']))      $bag=$_GET['bag'];
else      $bag="0";

if(isset($_GET['trad']))      $trad=$_GET['trad'];
else      $trad="0";

if(isset($_GET['duchesse']))      $duchesse=$_GET['duchesse'];
else      $duchesse="0";

if(isset($_GET['croissant']))      $croissant=$_GET['croissant'];
else      $croissant="0";

if(isset($_GET['pac']))      $pac=$_GET['pac'];
else      $pac="0";

// on se connecte à MySQL 
include('secure/config.php');
$db=mysql_connect($SQLhost, $SQLlogin, $SQLpass) or die(mysql_error());
// sélection de la base  

mysql_select_db('pain',$db)  or die('Erreur de selection '.mysql_error()); 
 
// on écrit la requête sql 
$sql = "UPDATE commande SET baguette= baguette + ".$bag.", tradition= tradition + ".$trad.", duchesse= duchesse + ".$duchesse.", croissant= croissant + ".$croissant.", pac= pac + ".$pac." WHERE id =1"; 

// on insère les informations du formulaire dans la table 
mysql_query($sql) or die('Erreur SQL !'.$sql.'<br>'.mysql_error()); 
mysql_close();  // on ferme la connexion 
//header('Location: index.php');
echo "update cuisson +".$bag." ".$trad." ".$croissant." ".$pac;
?>