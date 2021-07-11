<?php 

// On commence par récupérer les champs 
if(isset($_GET['objetN']))      $objetN=$_GET['objetN'];
else      $objetN="";

if(isset($_GET['objetQ']))      $objetQ=$_GET['objetQ'];
else      $objetQ="";

// on se connecte à MySQL 
include('../secure/config.php');
$db=mysqli_connect($SQLhost, $SQLlogin, $SQLpass, $SQLdb) or die(mysqli_error($db));


// on écrit la requête sql 
$sql = "UPDATE objet SET stock='".$objetQ."' WHERE id = '".$objetN."'"; 

// on insère les informations du formulaire dans la table 
mysqli_query($db, $sql) or die('Erreur SQL !'.$sql.'<br>'.mysqli_error($db)); 
mysqli_close($db);  // on ferme la connexion 
//header('Location: index.php');
echo "Q".$objetQ ."Objet ".$objetN." créé";
?>