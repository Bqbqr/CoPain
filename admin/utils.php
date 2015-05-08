<?php
function datedayfr($date){
	$date =strtotime($date);
	$day = date('l',$date);
	switch($day) {
	    case 'Monday': $day = 'Lundi'; break;
	    case 'Tuesday': $day = 'Mardi'; break;
	    case 'Wednesday': $day = 'Mercredi'; break;
	    case 'Thursday': $day = 'Jeudi'; break;
	    case 'Friday': $day = 'Vendredi'; break;
	    case 'Saturday': $day = 'Samedi'; break;
	    case 'Sunday': $day = 'Dimanche'; break;
	    default: $day =''; break;
	 }
    return $day;
}

function datemonthfr($date){
	$date =strtotime($date);
	$month = date('F', $date);
	switch($month) {
	    case 'January': $month = 'Janvier'; break;
	    case 'February': $month = 'Février'; break;
	    case 'March': $month = 'Mars'; break;
	    case 'April': $month = 'Avril'; break;
	    case 'May': $month = 'Mai'; break;
	    case 'June': $month = 'Juin'; break;
	    case 'July': $month = 'Juillet'; break;
	    case 'August': $month = 'Août'; break;
	    case 'September': $month = 'Septembre'; break;
	    case 'October': $month = 'Octobre'; break;
	    case 'November': $month = 'Novembre'; break;
	    case 'December': $month = 'Decembre'; break;
	    default: $month =''; break;
	 }
	 return $month;
}
?>