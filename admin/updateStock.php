<?php 

// On commence par récupérer les champs 
if(isset($_GET['objetN']))      $objetN=$_GET['objetN'];
else      $objetN="";

if(isset($_GET['objetQ']))      $objetQ=$_GET['objetQ'];
else      $objetQ="";

// on se connecte à MySQL 
include('../secure/config.php');
$db=mysql_connect($SQLhost, $SQLlogin, $SQLpass) or die(mysql_error());
// sélection de la base  

mysql_select_db('pain',$db)  or die('Erreur de selection '.mysql_error()); 
 
// on écrit la requête sql 
$sql = "UPDATE objet SET stock='".$objetQ."' WHERE id = '".$objetN."'"; 

// on insère les informations du formulaire dans la table 
mysql_query($sql) or die('Erreur SQL !'.$sql.'<br>'.mysql_error()); 
mysql_close();  // on ferme la connexion 
//header('Location: index.php');
echo "Q".$objetQ ."Objet ".$objetN." créé";
?>