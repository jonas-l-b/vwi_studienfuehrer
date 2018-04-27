<?php

include "sessionsStart.php";
include "connect.php";

?>
<?php

$userId = $_POST['userId'];

$sql = "SELECT * FROM users WHERE user_ID = $userId";
$result = mysqli_query($con, $sql);

if(mysqli_num_rows($result) == 1){
	$row = mysqli_fetch_assoc($result);
	
	$subject = 'Aktivierung deines Studienführer-Accounts';
	$message="
	<p>vielen Dank für deine Registrierung!</p>
	<p>Dein Account wurde erstellt. Um ihn zu aktivieren, klicke bitte auf diesen Link:<br>
	http://studienführer.vwi-karlsruhe.de/verify.php?email=".$row['email']."&hash=".$row['hash']."</p>
	";
	$mailService = EmailService::getService();
	if($mailService->sendEmail($row['email'], $row['first_name'], $subject, $message)){
		echo "Die Aktivierungs-E-Mail wurde erfolgreich an ".$row['first_name']." ".$row['last_name']." versandt.";
	}else{
		echo "Bei Senden der E-Mail ist ein Fehler aufgetreten.";
	}
}elseif(mysqli_num_rows($result) > 1){
	echo "Die Nutzer-ID konnte nicht eindeutig zugeordnet werden. Bitte Datenbank manuell untersuchen!";
}else{
	echo "Es ist ein unvorhergesehender Fehler aufgetreten. Bitte erneut versuchen oder Datenbank manuell untersuchen.";
}

?>