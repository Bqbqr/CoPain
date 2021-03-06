<?php

function date_fr()
  {
  $Jour = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi","Samedi");
  $Mois = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
  return $Jour[date("w")]." ".date("d")." ".$Mois[date("n")-1];
  }
  
function date_fr_arg($date){
	$jour = date('l',strtotime($date));
	$mois = date('F',strtotime($date));
	switch($jour) {
		case 'Monday': $jour = 'Lundi'; break;
		case 'Tuesday': $jour = 'Mardi'; break;
		case 'Wednesday': $jour = 'Mercredi'; break;
		case 'Thursday': $jour = 'Jeudi'; break;
		case 'Friday': $jour = 'Vendredi'; break;
		case 'Saturday': $jour = 'Samedi'; break;
		case 'Sunday': $jour = 'Dimanche'; break;
		default: $jour =''; break;
	}
	switch($mois) {
		case 'January': $mois = 'Janvier'; break;
		case 'February': $mois = 'Février'; break;
		case 'March': $mois = 'Mars'; break;
		case 'April': $mois = 'Avril'; break;
		case 'May': $mois = 'Mai'; break;
		case 'June': $mois = 'Juin'; break;
		case 'July': $mois = 'Juillet'; break;
		case 'August': $mois = 'Août'; break;
		case 'September': $mois = 'Septembre'; break;
		case 'October': $mois = 'Octobre'; break;
		case 'November': $mois = 'Novembre'; break;
		case 'December': $mois = 'Decembre'; break;
		default: $mois =''; break;
	}
	$jour_nb = date('d',strtotime($date));
	return $jour." ".$jour_nb." ".$mois;
}
  


?>

