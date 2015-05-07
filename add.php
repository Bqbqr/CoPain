<?php 

// On commence par récupérer les champs 
if(isset($_GET['nom']))      $nom=$_GET['nom'];
else      $nom="";

if(isset($_GET['emplacement']))      $emplacement=$_GET['emplacement'];
else      $emplacement="";

if(isset($_GET['baguette']))      $baguette=$_GET['baguette'];
else      $baguette="";

if(isset($_GET['tradition']))      $tradition=$_GET['tradition'];
else      $tradition="";

if(isset($_GET['croissant']))      $croissant=$_GET['croissant'];
else      $croissant="";

if(isset($_GET['pac']))      $pac=$_GET['pac'];
else      $pac="";

if(isset($_GET['duchesse']))      $duchesse=$_GET['duchesse'];
else      $duchesse="";

if(isset($_GET['petitdej']))      $petitdej=$_GET['petitdej'];
else      $petitdej="";

// on se connecte à MySQL 
include('secure/config.php');
$db=mysql_connect($SQLhost, $SQLlogin, $SQLpass) or die(mysql_error());

// sélection de la base  

mysql_select_db('pain',$db)  or die('Erreur de selection '.mysql_error()); 
 
// on écrit la requête sql 
$sql = "INSERT INTO commande (nomclient, emplacement, baguette, tradition, duchesse, croissant, pac, petitdejeuner,datestamp, deleted, recuperee) VALUES('$nom','$emplacement' ,'$baguette','$tradition','$duchesse','$croissant','$pac','$petitdej',NOW(),'0','0')"; 
 
// on insère les informations du formulaire dans la table 
mysql_query($sql) or die('Erreur SQL !'.$sql.'<br>'.mysql_error()); 
mysql_close();  // on ferme la connexion 
//header('Location: index.php');
echo "OK REQUETE VALIDEE $duchesse";
?>