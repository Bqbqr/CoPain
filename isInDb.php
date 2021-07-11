<?php 

// On commence par récupérer les champs 
if(isset($_GET['nom']))      $nom=$_GET['nom'];
else      $nom="";

if(isset($_GET['emplacement']))      $emplacement=$_GET['emplacement'];
else      $emplacement="";

// on se connecte à MySQL 
include('secure/config.php');
$return=array();
$bdd=mysqli_connect($SQLhost, $SQLlogin, $SQLpass,$SQLdb) or die(mysqli_error($bdd));
 
$sql= "SELECT numorder,name,pitch,nom,taken,sum(quantity) as quantity FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN article a on a.id=oc.article WHERE date=CURDATE()+INTERVAL 1 DAY AND deleted=0 AND name='$nom' AND pitch='$emplacement' GROUP BY nom,pitch;";
$req = mysqli_query($bdd,$sql) or die('Erreur SQL !'.mysqli_error($bdd)); 

//Cas où la commande existe déjà
if (mysqli_num_rows($req) != 0) {
	// remplit notre tableau
	$data = mysqli_fetch_assoc($req);
	$return["numorder"]=$data["numorder"];
	do{
		$return[$data['nom']]=$data['quantity'];
	}while($data = mysqli_fetch_assoc($req));

	mysqli_close($bdd);  // on ferme la connexion
	$return["value"]="double";
	// Useless ? $return["json"] = json_encode($return);
  	echo json_encode($return);
  	return;
} 


$req= mysqli_query($bdd,"SELECT name, pitch FROM orders WHERE name='$nom' AND date=CURDATE()+INTERVAL 1 DAY AND deleted=0") or die(mysqli_error($bdd));

if (mysqli_num_rows($req) != 0) { 
	mysqli_close($bdd);  // on ferme la connexion
	$return["value"]=$nom;
	$return["json"] = json_encode($return);
  	echo json_encode($return);
  	return;
} 

$sql = "SELECT name, pitch FROM orders WHERE pitch='$emplacement' AND date=CURDATE()+INTERVAL 1 DAY AND deleted=0";  
$req=mysqli_query($bdd,$sql) or die('Erreur SQL !'.$sql.'<br>'.mysqli_error($bdd)); 

if (mysqli_num_rows($req) != 0) { 
	mysqli_close($bdd);  // on ferme la connexion 
   	$return["value"]=$emplacement;
	$return["json"] = json_encode($return);
  	echo json_encode($return);
  	return;
}

$return["value"]="true";

mysqli_close($bdd);  // on ferme la connexion 
//header('Location: index.php');
$return["value"]="true";
$return["json"] = json_encode($return);
echo json_encode($return);
return;
?>