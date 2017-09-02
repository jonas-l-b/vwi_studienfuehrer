<?php

include "sessionsStart.php";
include('connect.php');

$dsAnon = 'Konto unwiderruflich löschen. Bewertungen anonymisieren.';
$dsAll = 'Konto und Bewertungen unwiderruflich löschen.';

if(isset($_GET['getDeleteModal'])){
	echo $twig->render('deleteModal.template.html', 
							array( 'dsAnon' => $dsAnon, 
									'dsAll' => $dsAll)
						);
}else if(isset($_POST['deleteUser'])){
	if(isset($_POST['password'])&&isset($_POST['anonymisieren'])&&isset($_POST['userDeleteSentence'])){
		$password = strip_tags($_POST['password']);
		$password = $con->real_escape_string($password);
		if (password_verify($password, $userRow['password'])){
			echo "successAnon";
		}else{
			echo "pwFail";
		}
	}else{
		echo "formFail";
	}
}else {
	echo "Keine Zugriffserlaubnis";
	exit();
}
?>