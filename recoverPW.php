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
		$subject = 'Änderung deines Studienführer-Passworts (VWI-ESTIEM Karlsrhe)';

		$message="
		<html>
		<head>
		<title>Passwortänderung</title>
		</head>
		<body>
		<div style=\"font-family:calibri\">
		<p>Hallo ".$row['first_name'].",</p>
		<p>Um dein Passwort zu ändern, klicke auf diesen Link und folge den Anweisungen:<br>
		http://app.vwi-karlsruhe.de/studienfuehrer/resetPW.php?recoverhash=".$recoverhash."</p>
		<p>Falls du diese Mail nicht angefordert hast oder du dein Passwort doch nicht ändern willst, ignoriere diese Mail einfach.<p>
		<p>Viele Grüße,<br>
		Deine VWI-ESTIEM Hochschulgruppe</p>
		<br><br>
		<p></p>
		</div>
		</body>
		</html>
		";
	} else{
		$subject = 'Wiederherstellung deines Studienführer-Passworts (VWI-ESTIEM Karlsrhe)';

		$message="
		<html>
		<head>
		<title>Passwortwiederherstellung</title>
		</head>
		<body>
		<div style=\"font-family:calibri\">
		<p>Hallo ".$row['first_name'].",</p>
		<p>Um dein Passwort zurückzusetzen, klicke auf diesen Link und folge den Anweisungen:<br>
		http://app.vwi-karlsruhe.de/studienfuehrer/resetPW.php?recoverhash=".$recoverhash."</p>
		<p>Falls du diese Mail nicht angefordert hast oder dir dein Passwort inzwischen wieder eingefallen ist, ignoriere diese Mail einfach.<p>
		<p>Viele Grüße,<br>
		Deine VWI-ESTIEM Hochschulgruppe</p>
		<br><br>
		<p></p>
		</div>
		</body>
		</html>
		";
	}
	
	$headers = "From: VWI-ESTIEM Karlsruhe" . "\r\n";
	//$headers .= "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	
	if (mail($to, $subject, $message, $headers)){
		//echo "Mail sent";
	}
}
?>
<html>
<body>

<div style="display: inline-block; position: fixed; top: 0; bottom: 0; left: 0; right: 0; width: 50%; height: 30%; margin: auto; padding:25px;">
	<div align="center" style="margin:auto;">
		<h4><?php if(isset($msg)) echo $msg ?></h4>
	</div>
</div>

</body>
</html>