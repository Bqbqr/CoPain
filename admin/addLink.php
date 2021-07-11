<?php 

// On commence par récupérer les champs 
if(isset($_GET['article']))      $article=$_GET['article'];
else      $article="";

if(isset($_GET['objet']))      $objet=$_GET['objet'];
else      $objet="";

if(isset($_GET['qty']))      $qty=$_GET['qty'];
else      $qty="";

// on se connecte à MySQL 
include('../secure/config.php');
$db=mysqli_connect($SQLhost, $SQLlogin, $SQLpass, $SQLdb) or die(mysqli_error($db));
 
//Vérif de la présence ou non du lien.
$sql = "SELECT * FROM objetsInArticle WHERE article='$article' AND objet='$objet'"; 


// on écrit la requête sql 
$sql = "INSERT INTO objetsInArticle (article,objet,quantity) VALUES ('$article','$objet','$qty')"; 

// on insère les informations du formulaire dans la table 
mysqli_query($db, $sql) or die('Erreur SQL !'.$sql.'<br>'.mysqli_error($db)); 
mysqli_close($db);  // on ferme la connexion 
//header('Location: index.php');
echo "Q".$objetQ."Objet ".$objetN." créé";
?>