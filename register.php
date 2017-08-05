<?php

include "header.php";

?>

<?php
session_start();
if (isset($_SESSION['userSession'])!="") {
	header("Location: home.php");
}
require_once 'connect.php';

if(isset($_POST['btn-signup'])) {
 
	$firstName = strip_tags($_POST['first_name']);
	$lastName = strip_tags($_POST['last_name']);
	$username = strip_tags($_POST['username']);
	$email = strip_tags($_POST['email']);
	$upass = strip_tags($_POST['password']);
	$degree = strip_tags($_POST['degree']);
	$advance = strip_tags($_POST['advance']);
	$semester = strip_tags($_POST['semester']);
	if(isset($_POST['info'])){
		$info = strip_tags($_POST['info']);
	}else{
		$info = "no";
	}
	
	$firstName = $con->real_escape_string($firstName);
	$lastName = $con->real_escape_string($lastName);
	$username = $con->real_escape_string($username);
	$email = $con->real_escape_string($email);
	$upass = $con->real_escape_string($upass);
	$degree = $con->real_escape_string($degree);
	$advance = $con->real_escape_string($advance);
	$semester = $con->real_escape_string($semester);
	$info = $con->real_escape_string($info);
	$hash = md5(rand(0,1000));

	$hashed_password = password_hash($upass, PASSWORD_DEFAULT); // this function works only in PHP 5.5 or latest version

	$check_email = $con->query("SELECT email FROM users WHERE email='$email'");
	$count=$check_email->num_rows;

	$check_username = $con->query("SELECT username FROM users WHERE username='$username'");
	$count2=$check_username->num_rows;
	
	if ($count==0 && $count2==0) {
		$query = "INSERT INTO users(admin,first_name,last_name,username,email,password,active,degree,advance,semester,info,hash) VALUES(0,'$firstName','$lastName','$username','$email','$hashed_password',0,'$degree','$advance','$semester','$info','$hash')";
		if ($con->query($query)) {
			//Send mail
			$to      = $email;
			$subject = 'Aktivierung deines Studienführer-Accounts (VWI-ESTIEM Karlsrhe)'; // Give the email a subject 

			$message="
			<html>
			<head>
			<title>Erfolgreiche Registierung!</title>
			</head>
			<body>
			<div style=\"font-family:calibri\">
			<p>Hallo ".$firstName.",</p>
			<p>vielen Dank für deine Registrierung!</p>
			<p>Dein Account wurde erstellt. Um ihn zu aktivieren, klicke bitte auf diesen Link:<br>
			http://app.vwi-karlsruhe.de/studienfuehrer/verify.php?email=".$email."&hash=".$hash."</p>
			<p>Viel Spaß mit dem Studienführer,<br>
			Deine VWI-ESTIEM Hochschulgruppe</p>
			<br><br>
			<p></p>
			</div>
			</body>
			</html>
			";
			$headers = "From: VWI-ESTIEM Karlsruhe" . "\r\n";
			//$headers .= "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			if (mail($to, $subject, $message, $headers)){
				//echo "Mail sent";
			}
			
			$msg = "<div class='alert alert-success'>
			<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Erfolgreich registiert! Wir haben einen Aktivierungslink an die angegebene E-Mail-Adresse gesendet.
			</div>";
		}else {
			$msg = "<div class='alert alert-danger'>
			<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Beim Registieren ist ein Fehler aufgetreten! Bitte wende dich an VWI-ESTIEM Karlsruhe.
			</div>";
		}
	}else {
		if($count>0 AND $count2!==0){
			$msg = "<div class='alert alert-danger'>
			<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Diese E-Mail-Adresse wird bereits verwendet! Bitte korrigiere die hervorgehobenen Felder - das Passwort muss aus Sicherheitsgründen erneut eingegeben werden.
			</div>";
			
			$memorey_firstName = $firstName;
			$memorey_lastName = $lastName;
			$memorey_username = $username;
			//$memorey_email = $email;
			$memorey_degree = $degree;
			$memorey_advance = $advance;
			$memorey_semester = $semester;
			$memorey_info = $info;
			
			//$highlight_username = "style=\"background-color:rgb(242, 222, 222)\"";
			$highlight_email = "style=\"background-color:rgb(242, 222, 222)\"";
			$hightlight_upass = "style=\"background-color:rgb(242, 222, 222)\"";
			
		}
		if($count2>0 AND $count==0){
			$msg = "<div class='alert alert-danger'>
			<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Dieser Benutzername wird bereits verwendet! Bitte korrigiere die hervorgehobenen Felder - das Passwort muss aus Sicherheitsgründen erneut eingegeben werden.
			</div>";

			$memorey_firstName = $firstName;
			$memorey_lastName = $lastName;
			//$memorey_username = $username;
			$memorey_email = $email;
			$memorey_degree = $degree;
			$memorey_advance = $advance;
			$memorey_semester = $semester;
			$memorey_info = $info;
			
			$highlight_username = "style=\"background-color:rgb(242, 222, 222)\"";
			//$highlight_email = "style=\"background-color:rgb(242, 222, 222)\"";
			$hightlight_upass = "style=\"background-color:rgb(242, 222, 222)\"";
		}
		if($count2>0 AND $count>0){
			$msg = "<div class='alert alert-danger'>
			<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Dieser Benutzername und diese E-Mail-Adresse werden bereits verwendet! Bitte korrigiere die hervorgehobenen Felder - das Passwort muss aus Sicherheitsgründen erneut eingegeben werden.
			</div>";

			$memorey_firstName = $firstName;
			$memorey_lastName = $lastName;
			//$memorey_username = $username;
			//$memorey_email = $email;
			$memorey_degree = $degree;
			$memorey_advance = $advance;
			$memorey_semester = $semester;
			$memorey_info = $info;
			
			$highlight_username = "style=\"background-color:rgb(242, 222, 222)\"";
			$highlight_email = "style=\"background-color:rgb(242, 222, 222)\"";
			$hightlight_upass = "style=\"background-color:rgb(242, 222, 222)\"";
		}
	}
	$con->close();
}
?>

<!DOCTYPE html>
<html>
<body>
<div class="container">
	<h1>Willkommen zum Studienführer!</h1>
	<p>Um dich zu registieren, musst du lediglich die Felder unten ausfüllen und auf den Button klicken. Sofern nicht explizit von dir erlaubt, werden wir deine Daten lediglich für den Studienführer nutzen.</p>
	<p style="font-weight:bold">Der Studienführer ist und bleibt kostenlos.</p>
</div>
<div class="signin-form">
	<div class="container">
		<form class="form-signin" method="post" id="register-form">
			<h3 class="form-signin-heading">Hier registrieren:</h3><hr />
			
			<?php
			if (isset($msg)) {
				echo $msg;
			}
			?>
			  
			<div class="form-group">
			<input value="<?php if(isset($memorey_firstName)) echo $memorey_firstName ?>" type="text" class="form-control" placeholder="Vorname" name="first_name" required  />
			</div>
			
			<div class="form-group">
			<input value="<?php if(isset($memorey_lastName)) echo $memorey_lastName ?>" type="text" class="form-control" placeholder="Nachname" name="last_name" required  />
			</div>
			
			<div class="form-group">
			<input <?php if(isset($highlight_username)) echo $highlight_username ?> value="<?php if(isset($memorey_username)) echo $memorey_username ?>" type="text" class="form-control" placeholder="Benutzername" name="username" required  />
			</div>
			
			<div class="form-group">
			<input <?php if(isset($highlight_email)) echo $highlight_email ?> value="<?php if(isset($memorey_email)) echo $memorey_email ?>" type="email" class="form-control" placeholder="E-Mail-Adresse" name="email" required  />
			<span id="check-e"></span>
			</div>
			
			<div class="form-group">
			<input id="userpassword" <?php if(isset($hightlight_upass)) echo $hightlight_upass ?> type="password" class="form-control" placeholder="Passwort" name="password" required  />
			</div>
			
			<div class="progress">
				<div id="StrengthProgressBar" class="progress-bar"></div>
			</div>
			
			<div class="form-group">
			<input value="<?php if(isset($memorey_degree)) echo $memorey_degree ?>" type="text" class="form-control" placeholder="Studiengang" name="degree" required  />
			</div>
			
			<div class="form-group">
				<select class="form-control" name="advance" required>
					<option value="bachelor">Bachelor</option>
					<option value="master" <?php if(isset($memorey_advance))if($memorey_advance == "master") echo "selected" ?> >Master</option>
				</select>
			</div>
			
			<div class="form-group">
			<input value="<?php if(isset($memorey_semester)) echo $memorey_semester ?>" type="text" class="form-control" placeholder="Fachsemester" name="semester" required  />
			</div>
			
			<div class="checkbox">
				<label><input type="checkbox" name="info" value="yes" <?php if(isset($memorey_info))if($memorey_info == "yes") echo "checked" ?> >Ich möchte über speziell für mich interessante Events informiert werden. Das können beispielsweise Einladungen zu (kostenlosen) Events wie Workshops, Vorträgen oder Fallstudien sein, die die Hochschulgruppe VWI-ESTIEM Karlsruhe zusammen mit Unternehmen veranstaltet.</label>
			</div>
			
			<hr>
			<?php /*Hier wäre es sinnvoll noch ein ReCAPTCHA von Google einzubauen */ ?>
			<div class="form-group">
				<button type="submit" class="btn btn-primary" name="btn-signup">
					<span class="glyphicon glyphicon-log-in"></span> &nbsp; Account erstellen
				</button> 
				<a href="login.php" class="btn btn-default" style="float:right;">Zum Login</a>
			</div>
		</form>
    </div>
</div>
<?php /*Die folgenden Skripte implementieren die Strength-Meter Bar des Password Inputs. Basis für die Berechnung der Stärke ist die zxcvbn library*/ ?>
<script type="text/javascript" src="res/lib/zxcvbn.js"></script>
<script type="text/javascript" src="res/lib/zxcvbn-bootstrap-strength-meter.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
		$("#StrengthProgressBar").zxcvbnProgressBar({ 
			  passwordInput: "#userpassword",
			  ratings: ["Lieber weitertippen", "Immer noch recht schwach", "Langsam wird's ok", "Stark!", "Unfassbar stark"] });
	});
</script>
</body>
</html>