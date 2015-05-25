<?php 

$parameters = $_REQUEST['parameters'];

// on se connecte à MySQL 
include('secure/config.php');
//Create connection
if(!$bdd=mysqli_connect($SQLhost, $SQLlogin,  $SQLpass, $SQLdb)){
	echo "Erreur de connection mysql";
}
//On créé la commande avec le nom et l'emplacement
mysqli_query($bdd,"INSERT INTO orders (name,pitch,date) VALUES('$parameters[name]','$parameters[pitch]' ,NOW()+INTERVAL 1 DAY)") or die(mysqli_error($bdd));
$last = mysqli_insert_id($bdd); 

foreach ($parameters as $key => $value) {
	if($key=="name" || $key=="pitch")
		continue;
	else if(is_array($value)){
		//Dans ce cas on est sur un article avec options
		foreach ($value as $kkey => $vvalue) {
		    mysqli_query($bdd,"INSERT INTO ordercontent (article,numorder, quantity, choice) VALUES('$key','$last','$vvalue','$kkey')") or die(mysqli_error($bdd)); 
		    echo "$key, $last, $kkey";
		}
		
	}
	else{
		//Pas d'option, article normal
		mysqli_query($bdd,"INSERT INTO ordercontent (article,numorder, quantity, choice) VALUES('$key','$last','$value',null)") or die(mysqli_error($bdd));
		echo "$key  $last  $value \n";
	
	}
}

mysqli_close($bdd);  // on ferme la connexion 
//header('Location: index.php');
echo "OK REQUETE VALIDEE";
?>