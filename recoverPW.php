<?php

include "connect.php";

include "header.php";

?>

<?php

$email = $_POST['email'];
$recoverType = $_POST['recoverType'];

$sql="
	SELECT *
	FROM users
	WHERE email = '".$email."';
";

$result = mysqli_query($con,$sql);

if (mysqli_num_rows($result)==0){
	$msg = "Die eingegebene E-Mail-Adresse existiert nicht in unserer Datenbank. Bitte registriere dich zuerst.";
}else{
	$msg = "Wir haben dir eine E-Mail zur Zurücksetzung deines Passworts geschickt.";
	
	$row = mysqli_fetch_assoc($result);
	
	//Passworthash in die Datenbank setzen, damit Link in Mail nur 1x funktoniert
	$recoverhash = md5(rand(0,1000));
	
	$sql="
		UPDATE users
		SET recoverhash = '".$recoverhash."'
		WHERE email = '".$email."';
	";
	mysqli_query($con,$sql);
	
	//Mail schicken
	$to = $email;
	
	if($recoverType=="change"){
		$subject = 'Änderung deines Studienführer-Passworts';

		$message="
		<p>Um dein Passwort zu ändern, klicke auf diesen Link und folge den Anweisungen:<br>
		http://studienführer.vwi-karlsruhe.de/resetPW.php?recoverhash=".$recoverhash."</p>
		<p>Falls du diese Mail nicht angefordert hast oder du dein Passwort doch nicht ändern willst, ignoriere diese Mail einfach.<p>
		";
	} else{
		$subject = 'Wiederherstellung deines Studienführer-Passworts';

		$message="
		<p>Um dein Passwort zurückzusetzen, klicke auf diesen Link und folge den Anweisungen:<br>
		http://studienführer.vwi-karlsruhe.de/resetPW.php?recoverhash=".$recoverhash."</p>
		<p>Falls du diese Mail nicht angefordert hast oder dir dein Passwort inzwischen wieder eingefallen ist, ignoriere diese Mail einfach.<p>
		";
	}	
	$mailService = EmailService::getService();
	$mailService->sendEmail($email, $row['first_name'], $subject, $message);
}
?>
<body>

<div style="display: inline-block; position: fixed; top: 0; bottom: 0; left: 0; right: 0; width: 50%; height: 30%; margin: auto; padding:25px;">
	<div align="center" style="margin:auto;">
		<h4><?php if(isset($msg)) echo $msg ?></h4>
	</div>
</div>

</body>
</html>