<?php

include "sessionsStart.php";
include('connect.php');

$dsAnon = 'Konto unwiderruflich löschen. Bewertungen anonymisieren.';
$dsAll = 'Konto und Bewertungen unwiderruflich löschen.';

$userEmail = $userRow['email'];
$user_first_name = $userRow['first_name'];

function sendDeletionMail($Anon){
	$body = "<p>Wir haben dein Profil gelöscht. Dabei haben wir deine Bewertungen ";
	if(!$Anon){
		$body .= "nicht anonymisiert, sondern restlos gelöscht.";
	}else{
		$body .= "anonymisiert. Dadurch können Studierende auch weiterhin von deinen Kommentaren profitieren. Sie können deinem Profil aber nicht mehr zugeordnet werden.</p>";
	}
	$body .= "<p> Wir finden es schade, dich gehen zu sehen. Vielleicht sieht man sich ja doch noch mal wieder. Gerne kannst du uns mitteilen, warum du dich gegen uns entschieden hast. Kontaktiere dafür einfach studienführer@vwi-karlsruhe.de.</p>";
	EmailService::getService()->sendEmail($userEmail,$user_first_name , "Löschung deines Studienführer-Profils", $body);
}


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
			if($userRow['admin'] != "0"){
				if(trim($_POST['userDeleteSentence']) == $dsAnon && $_POST['anonymisieren'] == "on"){
					if(		$con->query("DELETE FROM favourites WHERE user_ID=".$userRow['user_ID']) === true
						&&	$con->query("DELETE FROM anti_brute_force WHERE user_id=".$userRow['user_ID']) === true
						&&	$con->query("UPDATE messages SET answer_required='0'  WHERE sender_id=".$userRow['user_ID']) === true 			//Vorbereitung für anderen Branch.
							){
						if($con->query("UPDATE users SET first_name='Gelöschter', last_name='Nutzer', username='Profil deaktiviert', email='', password='', active='0', degree='', advance='', semester='',info='no',hash='',recoverhash='' WHERE user_ID=".$userRow['user_ID']) === true){
							echo "successAnon";
						}else{
							echo "Alles außer Profilleiste";
						}
					}else{
						echo "dbFail";
					}
					session_destroy();
					unset($_SESSION['userSession']);
					sendDeletionMail(true);
				}else if(trim($_POST['userDeleteSentence']) == $dsAll && $_POST['anonymisieren'] == "off"){
					if(		$con->query("DELETE FROM favourites WHERE user_ID=".$userRow['user_ID']) === true 
						&&	$con->query("DELETE FROM anti_brute_force WHERE user_id=".$userRow['user_ID']) === true 
						&&	$con->query("UPDATE messages SET answer_required='0' WHERE sender_id=".$userRow['user_ID']) === true 			//Vorbereitung für anderen Branch.
						&&	$con->query("DELETE FROM commentratings WHERE user_ID=".$userRow['user_ID']) === true 			//Ratings zu Comments des gelöschten User werden noch nicht gelöscht (Tote Daten. Später kümmern.)
						&&	$con->query("DELETE FROM ratings WHERE user_ID=".$userRow['user_ID']) === true 
					  ){
						if($con->query("DELETE FROM users WHERE user_ID=".$userRow['user_ID']) === true){
							echo "successAll"; 
						}else{
							echo "Alles außer Profilleiste";
						}						
					  }else{
						echo "dbFail";  
					  }
					session_destroy();
					unset($_SESSION['userSession']);
					sendDeletionMail(false);
				}else{
					echo "formFail";
				}
			}else{
				echo "adminError";
			}
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