<?php 

// On commence par récupérer les champs 
if(isset($_GET['id']))      $id=$_GET['id'];
else      $id="";

// on se connecte à MySQL 
include('../secure/config.php');
$db=mysqli_connect($SQLhost, $SQLlogin, $SQLpass, $SQLdb) or die(mysqli_error($db));
// sélection de la base  
 
// on écrit la requête sql 
$sql = "UPDATE orders SET taken='1' WHERE id =".$id; 

// on insère les informations du formulaire dans la table 
mysqli_query($db,$sql) or die('Erreur SQL !'.$sql.'<br>'.mysqli_error($db)); 
mysqli_close($db);  // on ferme la connexion 
//header('Location: index.php');
echo "recup ".$id." ok";
?>