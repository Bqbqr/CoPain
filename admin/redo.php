<?php 

if(isset($_GET['numorder']))      $numorder=$_GET['numorder'];
else      $numorder="";

if(isset($_GET['pitch']))      $pitch=$_GET['pitch'];
else      $pitch="";

if(isset($_GET['name']))      $name=$_GET['name'];
else      $name="";
// on se connecte à MySQL 
include('../secure/config.php');
//Create connection
if(!$bdd=mysqli_connect($SQLhost, $SQLlogin,  $SQLpass, $SQLdb)){
	echo "Erreur de connection mysql";
}


//Vérification de l'existence de la commande.
$req=mysqli_query($bdd,"SELECT numorder FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN article a on a.id=oc.article WHERE date=CURDATE()+INTERVAL 1 DAY AND deleted=0 AND name='$name' AND pitch='$pitch' GROUP BY numorder;") or die('Erreur requête' . mysqli_error($bdd));
if(mysqli_fetch_assoc($req)){
	echo "Commande pour $name déjà effectuée, annulation";
	return;
}


//Tableau d'insertion
$insert=[];

//Requête articles
$req = mysqli_query($bdd,"SELECT name,pitch,a.id as id,sum(quantity) as quantity FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN article a on a.id=oc.article WHERE date=CURDATE() AND deleted=0 AND numorder=$numorder GROUP BY nom,numorder ORDER BY name;") or die('Erreur SQL !'.mysqli_error($bdd)); 

while($data = mysqli_fetch_assoc($req)){
    $insert[$data['id']]=$data['quantity'];
    $insert['pitch']=$data['pitch'];
    $insert['name']=$data['name'];
}

//Requête options
$req = mysqli_query($bdd,"SELECT numorder,name,pitch, sum(quantity) as quantity, choice FROM orders o INNER JOIN ordercontent oc on oc.numorder=o.id INNER JOIN objet obj on obj.id=oc.choice WHERE date=CURDATE() AND numorder=$numorder GROUP BY name,numorder,choice;") or die('Erreur SQL !'.mysqli_error($bdd)); 
while($data = mysqli_fetch_assoc($req)){
	if(array_key_exists($data['choice'], $insert))
	  $insert[$data['choice']]+=$data['quantity'];
	else
	  $insert[$data['choice']]=$data['quantity'];
}


//On créé la commande avec le nom et l'emplacement
mysqli_query($bdd,"INSERT INTO orders (name,pitch,date) VALUES('$insert[name]','$insert[pitch]' ,NOW()+INTERVAL 1 DAY)") or die('erreur1' . mysqli_error($bdd));
$last = mysqli_insert_id($bdd); 

foreach ($insert as $key => $value) {
	if($key=="name" || $key=="pitch")
		continue;
	else if(is_array($value)){
		//Dans ce cas on est sur un article avec options
		foreach ($value as $kkey => $vvalue) {
		    mysqli_query($bdd,"INSERT INTO ordercontent (article,numorder, quantity, choice) VALUES('$key','$last','$vvalue','$kkey')") or die('erreur2' . mysqli_error($bdd)); 
		    //echo "$key, $last, $kkey";
		}
		
	}
	else{
		//Pas d'option, article normal
		mysqli_query($bdd,"INSERT INTO ordercontent (article,numorder, quantity, choice) VALUES('$key','$last','$value',null)") or die("Erreur No Option:" . mysqli_error($bdd));
		//echo "Ajout $key  $last  $value \n";
	
	}
}

mysqli_close($bdd);  // on ferme la connexion 
//header('Location: index.php');
echo "$name \n Commande pour le lendemain effectuée avec succès!";

?>