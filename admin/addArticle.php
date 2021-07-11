<?php 

// On commence par récupérer les champs 
if(isset($_GET['articleN']))      $articleN=$_GET['articleN'];
else      $articleN="";

if(isset($_GET['articleP']))      $articleP=$_GET['articleP'];
else      $articleP="";

if(isset($_GET['articleImg']))      $articleImg=$_GET['articleImg'];
else      $articleImg="";

// on se connecte à MySQL 
include('../secure/config.php');
$db=mysqli_connect($SQLhost, $SQLlogin, $SQLpass, $SQLdb) or die(mysqli_error($db));
 
// on écrit la requête sql 
$sql = "INSERT INTO article (nom,prix,img,actif) VALUES ('$articleN','$articleP','$articleImg','0')"; 

// on insère les informations du formulaire dans la table 
mysqli_query($db, $sql) or die('Erreur SQL !'.$sql.'<br>'.mysqli_error($db)); 
mysqli_close($db);  // on ferme la connexion 
//header('Location: index.php');
echo "Q".$objetQ."Objet ".$objetN." créé";
?>