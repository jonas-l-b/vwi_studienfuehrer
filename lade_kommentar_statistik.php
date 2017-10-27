<?php

/*
*		Dieses Skript liefert die Statistiken zu einem Kommentar
*/


include "sessionsStart.php";
include "connect.php";



if (isset($_GET['kommentar'])){

	
	$kommentarID = $_GET['kommentar'];
	$statement1 = $con->prepare("SELECT * FROM ratings WHERE ID = ?");
	$statement1->bind_param('s', $kommentarID);
	$statement1->execute();
	$users = $statement1->get_result();
	if($row = mysqli_fetch_assoc($users)){
		echo $twig->render('bewertungen.template.html', 
							array(	'rating' => array(
													'crit1'=> round($row['crit1']/7*100),
													'crit2'=> round($row['crit2']/7*100),
													'crit3'=> round($row['crit3']/7*100),
													'crit4'=> round($row['crit4']/7*100),
													'crit5'=> round($row['crit5']/7*100),
													)
								));
	}else{
		echo "<script> alert('Ein Fehler ist aufgetreten.');</script>";
	}
	$statement1->close();
}else{
	exit;
}

?>