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

if(isset($_GET['articleO']))      $articleO=$_GET['articleO'];
else      $articleO="";

if(isset($_GET['active']))      $active=$_GET['active'];
else      $active="";

// on se connecte à MySQL 
include('../secure/config.php');
$db=mysqli_connect($SQLhost, $SQLlogin, $SQLpass, $SQLdb) or die(mysqli_error($db));

 
// on écrit la requête sql 
$sql = "UPDATE article SET nom='$articleN', prix='$articleP', img='$articleImg', actif='$active',listorder='$articleO' WHERE id='$id'"; 

// on insère les informations du formulaire dans la table 
mysqli_query($db, $sql) or die('Erreur SQL !'.$sql.'<br>'.mysqli_error($db)); 
mysqli_close($db);  // on ferme la connexion 
//header('Location: index.php');
echo "Test: ".$sql;
?>