<?php

/*
*		Dieses Skript liefert das Bewertungsform unbefüllt oder befüllt
*/


include "sessionsStart.php";
include "connect.php";



if (isset($_GET['subject'])&&isset($_GET['filling'])){

	
	$subject = $_GET['subject'];
	/*$statement1 = $con->prepare("SELECT * FROM ratings WHERE ID = ?");
	$statement1->bind_param('s', $kommentarID);
	$statement1->execute();
	$users = $statement1->get_result();
	if($row = mysqli_fetch_assoc($users)){*/
		echo $twig->render('bewerten.template.html', 
							array(	'subject' => $subject,
								));
	/*}else{
		echo "<script> alert('Ein Fehler ist aufgetreten.');</script>";
	}
	$statement1->close();*/
}else{
	exit;
}

?>