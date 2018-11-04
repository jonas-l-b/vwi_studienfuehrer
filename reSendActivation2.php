<?php

include "connect.php";

?>
<?php

$email = $_POST['email'];

$sql = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($con, $sql);

if(mysqli_num_rows($result) == 1){
	$row = mysqli_fetch_assoc($result);
	
	$subject = 'Aktivierung deines Studienführer-Accounts';
	$message="
	<p>vielen Dank für deine Registrierung!</p>
	<p>Dein Account wurde erstellt. Um ihn zu aktivieren, klicke bitte auf diesen Link:<br>
	https://xn--studienfhrer-klb.vwi-karlsruhe.de/verify.php?email=".$row['email']."&hash=".$row['hash']."</p>
	";
	$mailService = EmailService::getService();
	if($mailService->sendEmail($row['email'], $row['first_name'], $subject, $message)){
		echo "erfolg";
	}else{
		echo "fail";
	}
}else{
	echo "fail";
}

?>