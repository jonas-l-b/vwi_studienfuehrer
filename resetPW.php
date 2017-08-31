<?php

include "header.php";

include "connect.php";

?>

<?php
if(isset($_GET['recoverhash'])){
	$recoverhash = strval ($_GET['recoverhash']);
	
	$sql="
		SELECT *
		FROM users
		WHERE recoverhash = '".$recoverhash."';
	";
	$result = mysqli_query($con,$sql);

	if (mysqli_num_rows($result) != 1){
		echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=resetPW_error';</SCRIPT>");
	}
	if (isset($_POST['btn-login'])) {
		
		$password = strip_tags($_POST['password']);
		$password = $con->real_escape_string($password);
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		
		$sql2="
			DELETE FROM anti_brute_force
			WHERE user_id = (SELECT user_ID FROM users WHERE recoverhash = '".$recoverhash."');
		";
		
		$sql3="
			UPDATE users
			SET password = '".$hashed_password."', recoverhash = ''
			WHERE recoverhash = '".$recoverhash."';
		";

		
		if ($con->query($sql2)) {
			if ($con->query($sql3)) {
				echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=resetPW_successful';</SCRIPT>");
			}
		}

		$con->close();
	}
}else{
	echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=resetPW_error';</SCRIPT>");
}

?>
<body>

<div class="container">

	<h3>Passwort ändern</h3>
	<p>Trage hier dein gewünschtes neues Passwort ein und bestätige mit dem Button:</p>
	
	<form class="form-signin" method="post" id="pw-recovery-form">

		<div class="form-group has-feedback <?php if(isset($hightlight_upass)) echo 'has-error' ?>">
				<input id="password" type="password" class="form-control" placeholder="Passwort" name="password" data-pw="pw" data-pw-error="Dein neues Passwort ist noch nicht stark genug! Benutze am besten mehrere Wörter, Groß- und Kleinbuchstaben, Zahlen und Sonderzeichen. " required  />
				<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				<div class="help-block with-errors"></div>
		</div>
			
		<!-- FUNKTIONIERT HIER NOCH NICHT AUS UNGEKLÄRTEN GRÜNDEN!<div class="progress">
			<div id="StrengthProgressBar" class="progress-bar"></div>
		</div>-->
			
		<div class="form-group has-feedback <?php if(isset($hightlight_upass)) echo 'has-error' ?>">
			<input id="userpassword2" type="password" class="form-control" placeholder="Passwort erneut eingeben" data-match="#password" name="password2" required data-error="Die Eingaben stimmen nicht überein." />
			<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
			<div class="help-block with-errors"></div>
		</div>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary" name="btn-login" id="btn-login">Passwort ändern</button> 
		</div>  
	</form>

</div>

<script type="text/javascript" src="res/lib/zxcvbn.js"></script>
<script type="text/javascript" src="res/lib/zxcvbn-bootstrap-strength-meter.js"></script>
<script type="text/javascript" src="res/lib/bootstrap-validator/validator.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
		var userInputs = new Array();
		userInputs.push("studienführer");
		$("#StrengthProgressBar").zxcvbnProgressBar({ 
			  passwordInput: "#userpassword",
			  ratings: ["Weitertippen", "Immer noch recht schwach", "Ok", "Stark!", "Unfassbar stark"],
			  userInputs: userInputs });
		$('#pw-recovery-form').validator({
			custom: {
				'pw': function($el) {
					var result = zxcvbn($el.val(), userInputs);
				    if(result.score>=2){
					  return false;  
				    }else{
					  return true;
				    } 
				}
			},
			errors: {
				pw: 'Deine Passwortstärke muss mindestens "OK" sein!'
			}
		});
	});
</script>

</body>
</html>
