<?php

include "connect.php";

?>

<?php
if (isset($_GET['email']) AND isset($_GET['hash'])){
	$email = strval ($_GET['email']);
	$hash = strval ($_GET['hash']);
	
	$sql="
		SELECT *
		FROM users
		WHERE email = '".$email."' and hash = '".$hash."';
	";
	$result = mysqli_query($con,$sql);

	if (mysqli_num_rows($result) == 0){
		echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=verify_error';</SCRIPT>");
	} else{
		$sql2="
			UPDATE users
			SET active = 1
			WHERE email = '".$email."' and hash = '".$hash."';
		";
		if ($con->query($sql2) == TRUE) {
			//echo 'erfolgreich';
		}
		echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=verify_successful';</SCRIPT>");
	}
}
else{
	echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=verify_error';</SCRIPT>");
}
?>