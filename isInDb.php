<?php 

// On commence par récupérer les champs 
if(isset($_GET['nom']))      $nom=$_GET['nom'];
else      $nom="";

if(isset($_GET['emplacement']))      $emplacement=$_GET['emplacement'];
else      $emplacement="";

// on se connecte à MySQL 
include('secure/config.php');
$db=mysql_connect($SQLhost, $SQLlogin, $SQLpass) or die(mysql_error());

mysql_select_db('pain',$db)  or die('Erreur de selection '.mysql_error()); 
 
// on écrit la requête sql 

$sql = "SELECT name, pitch FROM orders WHERE name='$nom' AND date=CURDATE()+INTERVAL 1 DAY"; 
$req=mysql_query($sql) or die('Erreur SQL !'.$sql.'<br>'.mysql_error()); 

if (mysql_num_rows($req) != 0) { 
	mysql_close();  // on ferme la connexion
	$return["value"]=$nom;
	$return["json"] = json_encode($return);
  	echo json_encode($return);
  	return;
} 

$sql = "SELECT name, pitch FROM orders WHERE pitch='$emplacement' AND date=CURDATE()+INTERVAL 1 DAY";  
$req=mysql_query($sql) or die('Erreur SQL !'.$sql.'<br>'.mysql_error()); 

if (mysql_num_rows($req) != 0) { 
	mysql_close();  // on ferme la connexion 
   	$return["value"]=$emplacement;
	$return["json"] = json_encode($return);
  	echo json_encode($return);
  	return;
}

$return["value"]="true";

mysql_close();  // on ferme la connexion 
//header('Location: index.php');
$return["value"]="true";
$return["json"] = json_encode($return);
echo json_encode($return);
return;
?>