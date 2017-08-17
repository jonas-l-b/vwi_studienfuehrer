<?php

include "header.php";

?>

<?php
session_start();
if (isset($_SESSION['userSession'])!="") {
	header("Location: home.php");
}
require_once 'connect.php';

$msg1 = "";

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
	
	if ($count==0 && $count2==0 && strtolower($username) != strtolower(explode("@", $email, 2)[0])) {
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
		if(strtolower($username) == strtolower(explode("@", $email, 2)[0])){
			$msg1 .= "<div class='alert alert-danger'>
			<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Verwende nicht dein U-Kürzel als deinen Nutzernamen!
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
<body>
<div class="container">
	<h1>Willkommen zum Studienführer!</h1>
	<p>Um dich zu registieren, musst du lediglich die Felder unten ausfüllen und auf den Button klicken. Sofern nicht explizit von dir erlaubt, werden wir deine Daten lediglich für den Studienführer nutzen.</p>
	<p style="font-weight:bold">Der Studienführer ist und bleibt kostenlos.</p>
</div>
<div class="signin-form">
	<div class="container">
		<form class="form-signin" method="post" id="register-form" action="register.php">
			<h3 class="form-signin-heading">Hier registrieren:</h3><hr />
			
			<?php
			if (isset($msg)) {
				echo $msg . $msg1;
			}
			?>
			  
			<div class="form-group has-feedback">
				<input value="<?php if(isset($memorey_firstName)) echo $memorey_firstName ?>" type="text" class="form-control" placeholder="Vorname" name="first_name" id="bad1" required  />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block with-errors"></div>
			</div>
			
			<div class="form-group has-feedback">
				<input value="<?php if(isset($memorey_lastName)) echo $memorey_lastName ?>" type="text" class="form-control" placeholder="Nachname" name="last_name" id="bad2" required  />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block with-errors"></div>
			</div>
			
			<div class="form-group has-feedback <?php if(isset($highlight_username)) echo 'has-error' ?>">
				<input value="<?php if(isset($memorey_username)) echo $memorey_username ?>" 
					type="text" pattern="^[a-zA-Z0-9_äöüÄÖÜßẞ][a-zA-Z0-9_äöüÄÖÜßẞ][a-zA-Z0-9_äöüÄÖÜßẞ][a-zA-Z0-9_äöüÄÖÜßẞ][a-zA-Z0-9_äöüÄÖÜßẞ]+$" 
					maxlength="30" class="form-control" placeholder="Benutzername" name="username" aria-describedby="helpBlock" data-username="username" data-username-error="Der Benutzername ist leider schon vergeben."
					data-error="Dein Benutzername muss zwischen 5 und 30 Zeichen lang sein. Erlaubt sind Ziffern 0-9 und Buchstaben a-Z, Umlaute und das kleine und (jetzt auch) das große ẞ." id="bad3" required  />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block">Benutze <strong>nicht</strong> dein U-Kürzel. 
					<a href="#" data-trigger="focus" data-toggle="popoverUKUERZEL" title="Benutze kein U-Kürzel als Nutzernamen." data-content="U-Kürzel sind (entgegen der häufigen Annahme) nicht anonym. Zum Beispiel kann in ILIAS jeder Administrator einer Gruppe mit den geeigneten Rechten ein U-Kürzel einem Namen und einer Matrikelnummer zuordnen. Wir möchten, dass du den Studienführer anonym nutzen kannst, wähle daher einen Nutzernamen, indem dein U-Kürzel am besten nicht vorkommt.">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
				</div>
				<div class="help-block"></div>
				<div class="help-block with-errors"></div>
			</div>
			
			<div class="form-group has-feedback <?php if(isset($highlight_email)) echo 'has-error' ?>">
				<input value="<?php if(isset($memorey_email)) echo $memorey_email ?>" type="email" pattern="^u[a-z][a-z][a-z][a-z]@student.kit.edu$" class="form-control" placeholder="E-Mail-Adresse" name="email" required  />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block">Gib eine deine U-Kürzel E-Mail-Adresse ein. Zum Beispiel: uxxxx@student.kit.edu
					<a href="#" data-toggle="popoverEMAIL" data-trigger="focus" title="Benutze deine Studierendenemailadresse." data-content="Der Studienführer soll nur für Studierende des KIT zur Verfügung stehen. Wir können diesen Status am leichtesten über deine u-Email-Adresse verifizieren.">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
				</div>
				<div class="help-block with-errors"></div>
			</div>
			
			<div class="form-group has-feedback <?php if(isset($hightlight_upass)) echo 'has-error' ?>">
				<input id="userpassword" type="password" class="form-control" placeholder="Passwort" name="password" data-pw="pw" data-pw-error="Deine Passwortstärke muss mindestens 'Mittelmäßig!' sein!" required  />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block with-errors"></div>
			</div>
			
			<div class="progress">
				<div id="StrengthProgressBar" class="progress-bar"></div>
			</div>
			
			<div class="form-group has-feedback <?php if(isset($hightlight_upass)) echo 'has-error' ?>">
				<input id="userpassword2" type="password" class="form-control" placeholder="Passwort erneut eingeben" data-match="#userpassword" name="password2" required data-error="Die Eingaben stimmen nicht überein." />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block with-errors"></div>
			</div>
			
			<div class="form-group has-feedback">
				<input value="<?php if(isset($memorey_degree)) echo $memorey_degree ?>" type="text" class="form-control" placeholder="Studiengang" name="degree" required  />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block with-errors"></div>
			</div>
			
			<div class="form-group has-feedback">
				<select class="form-control" name="advance" required style="-moz-appearance: none;-webkit-appearance: none;appearance: none;">
					<option value="bachelor">Bachelor</option>
					<option value="master" <?php if(isset($memorey_advance))if($memorey_advance == "master") echo "selected" ?> >Master</option>
				</select>
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
			</div>
			
			<div class="form-group has-feedback">
				<input value="<?php if(isset($memorey_semester)) echo $memorey_semester ?>" type="number" max="18" min="1" step="1" class="form-control" placeholder="Fachsemester" name="semester" required  />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block with-errors"></div>
			</div>
			
			<div class="checkbox has-feedback">
				<label><input type="checkbox" name="info" value="yes" <?php if(isset($memorey_info))if($memorey_info == "yes") echo "checked" ?> >Ich möchte über speziell für mich interessante Events informiert werden. Das können beispielsweise Einladungen zu (kostenlosen) Events wie Workshops, Vorträgen oder Fallstudien sein, die die Hochschulgruppe VWI-ESTIEM Karlsruhe zusammen mit Unternehmen veranstaltet.</label>
			</div>
			
			<div class="checkbox has-feedback">
				<label><input type="checkbox" name="nutzungsbedingungen" id="bedingungen"  
				value="yes">Hiermit bestätigst du, dass du unsere <a href="#" data-toggle="modal" data-target="#bedingungenModal">Datenschutzerklärung, Nutzungsbedingungen und Gemeinschaftsstandards</a> gelesen hast und diese akzeptierst.</label>
			</div>
			
			<hr>
			<?php /*Hier wäre es sinnvoll noch ein ReCAPTCHA von Google einzubauen */ ?>
			<div class="form-group">
				<button id="submitbutton" class="btn btn-primary" name="btn-signup">
					<span class="glyphicon glyphicon-log-in"></span> &nbsp; Account erstellen
				</button> 
				<a href="login.php" class="btn btn-default" style="float:right;">Zum Login</a>
			</div>
		</form>
    </div>
</div>

<!-- Bedingungen Modal -->
<div id="bedingungenModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Unsere Regeln</h4>
      </div>
      <div class="modal-body">
        <p>Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. Lorem Ipsum Dolor. </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
      </div>
    </div>

  </div>
</div>




<div id="snackbar">Du musst unsere Bedingungen akzeptieren bevor du dich registrieren kannst!</div>


<?php /*Die folgenden Skripte implementieren die Strength-Meter Bar des Password Inputs. Basis für die Berechnung der Stärke ist die zxcvbn library.
		Außerdem berechnen wir, ob das Input Form als ganzes abgeschickt werden darf.*/ ?>
<script type="text/javascript" src="res/lib/zxcvbn.js"></script>
<script type="text/javascript" src="res/lib/zxcvbn-bootstrap-strength-meter.js"></script>
<script type="text/javascript" src="res/lib/bootstrap-validator/validator.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
		
		var userInputs = ["studienführer", "vwi", "estiem", "wiwi", "wing", "hochschulgruppe", "hsg"];
		$( '#bad1' ).blur(function() {
		  userInputs.push($('#bad1').val());
		});
		$( '#bad2' ).blur(function() {
		  userInputs.push($('#bad2').val());
		});
		$( '#bad3' ).blur(function() {
		  userInputs.push($('#bad3').val());
		});
		$("#StrengthProgressBar").zxcvbnProgressBar({ 
			  passwordInput: "#userpassword",
			  ratings: ["Weitertippen", "Immer noch recht schwach", "Mittelmäßig", "Stark!", "Unfassbar stark"],
			  userInputs: userInputs });
		$('#register-form').validator({
			custom: {
				'pw': function($el) {
					var result = zxcvbn($el.val(), userInputs);
				    if(result.score>=2){
					  return false;  
				    }else{
					  return true;
				    } 
				},
				'username': function($el) {
					$.ajax({
						type: "GET",
						url: "username-validation-api.php",
						dataType: "text", 
						data: { username: $el.val() }
					}).done(function (res) {
						if(res==="{ok: true}"){
							$el.next().next().next().text('Der Benutzername ist leider schon vergeben.');
							$el.parent().addClass('has-error');
							return false;
						}else{
							$el.next().next().next().text('');
							$el.parent().removeClass('has-error');
							return true;
						}
					});
				}
			},
			errors: {
				pw: 'Deine Passwortstärke muss mindestens "Mittelmäßig" sein!', 
				username: 'Der Benutzername ist leider schon vergeben.'
			}
		});
		
		$( "#submitbutton" ).click(function() {
		  if($( "#submitbutton" ).hasClass('disabled')==false){
			if($( "#bedingungen:checked").length > 0){
				$( "#register-form" ).submit();  
			}else{
				var x = document.getElementById("snackbar")
				// Add the "show" class to DIV
				x.className = "show";
				// After 3 seconds, remove the show class from DIV
				setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
			}
		  }
		});
		

		$('[data-toggle="popoverUKUERZEL"]').popover(); 
		$('[data-toggle="popoverEMAIL"]').popover();
	});
</script>
</body>
</html>