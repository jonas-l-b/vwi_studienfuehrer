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
		echo $twig->render('bewertung_zu_kommentar_modal.template.html', 
							array(	'rating' => array(
													'crit1'=> $row['crit1'],
													'crit2'=> $row['crit2'],
													'crit3'=> $row['crit3'],
													'crit4'=> $row['crit4'],
													'crit5'=> $row['crit5'],
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