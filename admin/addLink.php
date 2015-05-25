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
$db=mysql_connect($SQLhost, $SQLlogin, $SQLpass) or die(mysql_error());
// sélection de la base  

mysql_select_db('pain',$db)  or die('Erreur de selection '.mysql_error()); 
 
//Vérif de la présence ou non du lien.
$sql = "SELECT * FROM objetsInArticle WHERE article='$article' AND objet='$objet'"; 


// on écrit la requête sql 
$sql = "INSERT INTO objetsInArticle (article,objet,quantity) VALUES ('$article','$objet','$qty')"; 

// on insère les informations du formulaire dans la table 
mysql_query($sql) or die('Erreur SQL !'.$sql.'<br>'.mysql_error()); 
mysql_close();  // on ferme la connexion 
//header('Location: index.php');
echo "Q".$objetQ."Objet ".$objetN." créé";
?>