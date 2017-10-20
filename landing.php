<?php

include "header.php";

?>


<?php
//landing fängt Fehler etc. ab
$m = "";
$msg = "Hallo!";

if (isset($_GET['m'])) {
	$m = $_GET['m'];
}

if ($m == "no_subject_in_url"){
	$msg = "Keine Veranstaltung in der URL hinterlegt. Bitte zurückgehen und erneut versuchen.";
}

if ($m == "no_subject_in_db"){
	$msg = "Veranstaltung existiert nicht in der Datenbank.";
}

if ($m == "no_module_in_url"){
	$msg = "Kein Modul in der URL hinterlegt. Bitte zurückgehen und erneut versuchen.";
}

if ($m == "no_module_in_db"){
	$msg = "Modul existiert nicht in der Datenbank.";
}

if ($m == "no_lecturer_in_url"){
	$msg = "Kein Dozent in der URL hinterlegt. Bitte zurückgehen und erneut versuchen.";
}

if ($m == "no_lecturer_in_db"){
	$msg = "Dozent existiert nicht in der Datenbank.";
}

if ($m == "no_institute_in_url"){
	$msg = "Kein Institut in der URL hinterlegt. Bitte zurückgehen und erneut versuchen.";
}

if ($m == "no_institute_in_db"){
	$msg = "Institut existiert nicht in der Datenbank.";
}

if ($m == "verify_error"){
	$msg = "Der Verifizierungslink is fehlerhaft. Stelle sicher, dass du ihn richtig aus der E-Mail kopiert hast und wende dich bei weiteren Problemen an VWI-ESTIEM.";
}

if ($m == "verify_successful"){
	$msg = "<h4>Die Aktivierung war erfolgreich! <br> Melde dich gleich an:</h4>
		  <a href=\"login.php\" class=\"btn btn-primary\" role=\"button\">Login</a>";
}

if ($m == "resetPW_error"){
	$msg = "Der Passwortzurücksetzungslink wurde bereits verwendet oder ist fehlerhaft. Stelle sicher, dass du ihn richtig aus der E-Mail kopiert hast und wende dich bei weiteren Problemen an VWI-ESTIEM.";
}

if ($m == "resetPW_successful"){
	$msg = "<h4>Du hast dein Passwort erfolgreich geändert. Jeder Passwortzurücksetzungslink kann nur einmal verwendet werden. <br><br> Melde dich jetzt an:</h4>
		  <a href=\"login.php\" class=\"btn btn-primary\" role=\"button\">Login</a>";
}

if ($m == "no_admin"){
	$msg = "
		<p><h2>Zugriff verweigert!</h2><p>
		<p>Du hast versucht, dich als Administrator anzumelden, allerdings besitzt du nicht die nötigen Rechte.<br>Wende dich ans WIM-Ressort der VWI-ESTIEM Hochschulgruppe.</p>
	";
}

?>

<html>
<body>

<div style="display: inline-block; position: fixed; top: 0; bottom: 0; left: 0; right: 0; width: 50%; height: 30%; margin: auto; padding:25px;">
	<div align="center" style="margin:auto;">
		<a href="http://vwi-karlsruhe.de/" target="blank"><img src="pictures/logo.png" style="width:150px;"></a>
		<br><br>
		<?php echo $msg ?>
	</div>
</div>


</body>
</html>