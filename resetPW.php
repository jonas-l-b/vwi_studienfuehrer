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
			UPDATE users
			SET password = '".$hashed_password."'
			WHERE recoverhash = '".$recoverhash."';
		";
		
		//Destroy recoverhash to prevent multiple usuage
		$sql3="
			UPDATE users
			SET recoverhash = ''
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

<html>
<body>

<div class="container">

	<h3>Passwort ändern</h3>
	<p>Trage hier dein gewünschtes neues Passwort ein und bestätige mit dem Button:</p>
	
	<form class="form-signin" method="post" id="login-form">

		<div class="form-group">
		<input type="password" class="form-control" placeholder="Passwort" name="password" required />
		</div>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary" name="btn-login" id="btn-login">Passwort ändern</button> 
		</div>  
	</form>

</div>



</body>
</html>
