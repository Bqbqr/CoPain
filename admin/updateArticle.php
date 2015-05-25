<?php 

// On commence par récupérer les champs 
if(isset($_GET['articleN']))      $articleN=$_GET['articleN'];
else      $articleN="";

if(isset($_GET['articleP']))      $articleP=$_GET['articleP'];
else      $articleP="";

if(isset($_GET['articleImg']))      $articleImg=$_GET['articleImg'];
else      $articleImg="";

if(isset($_GET['id']))      $id=$_GET['id'];
else      $id="";

if(isset($_GET['active']))      $active=$_GET['active'];
else      $active="";

// on se connecte à MySQL 
include('../secure/config.php');
$db=mysql_connect($SQLhost, $SQLlogin, $SQLpass) or die(mysql_error());
// sélection de la base  

mysql_select_db('pain',$db)  or die('Erreur de selection '.mysql_error()); 
 
// on écrit la requête sql 
$sql = "UPDATE article SET nom='$articleN', prix='$articleP', img='$articleImg', actif='$active' WHERE id='$id'"; 

// on insère les informations du formulaire dans la table 
mysql_query($sql) or die('Erreur SQL !'.$sql.'<br>'.mysql_error()); 
mysql_close();  // on ferme la connexion 
//header('Location: index.php');
echo "Test: ".$sql;
?>