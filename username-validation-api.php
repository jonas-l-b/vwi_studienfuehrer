<?php

/*
*		Dieses Skript checkt, ob ein Benutzername schon vergeben ist.
*
*		
*
*/
header('Content-type: application/json');
include "connect.php";

if (isset($_GET['username'])){
	$statement1 = $con->prepare("SELECT * FROM users WHERE LOWER(username) = ?");
	$statement1->bind_param('s', strtolower($_GET['username']));
	$statement1->execute();
	$users = $statement1->get_result();
	if($row = mysqli_fetch_assoc($users)){
		echo "{ok: true}";
	}else{
		echo "{ok: false}";
	}
	$statement1->close();
}else{
	exit;
}
?>
