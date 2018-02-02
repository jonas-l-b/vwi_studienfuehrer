<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>

<?php

session_start();

if(!isset($_SESSION['userSession'])){
	header("Location: login.php");
}elseif(isset($_SESSION['userSession'])!=""){
	header("Location: login.php");
}

session_destroy();
unset($_SESSION['userSession']);

//Cookies löschen
unset($_COOKIE['vwistudi_series']);
setcookie('vwistudi_series', '', time() - 3600, '/');
unset($_COOKIE['vwistudi_token']);
setcookie('vwistudi_token', '', time() - 3600, '/');
unset($_COOKIE['vwistudi_user']);
setcookie('vwistudi_user', '', time() - 3600, '/');
//Cookie-Daten aus DB löschen
mysqli_query($con, "DELETE FROM remember_me WHERE user_id = ".$userRow['user_ID']."");

echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='login.php?logout=true';</SCRIPT>");

?>
