<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>

<?php
if($userRow['admin']==0){
	echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=no_admin';</SCRIPT>");
}
?>

<html>
<body>

<?php include "nav.php" ?>

<div class="container" style="margin-top:60px">

	<h2>Ja servus, lieber Administrator des Studienführers!</h2>
	<p>Hier kann die Datenbank maßgeblich verändert werden. Vorsicht dabei, Backups und Validierungen gibt's quasi (noch) nicht :)</p>
	
	<hr>
	
	<h3 class="adminHeader">Eintragen</h3>
	<p>Über diesen Button können <strong>Veranstaltungen</strong> und in diesem Zuge auch <strong>Dozenten</strong>, <strong>Institute</strong> und <strong>Module</strong> eingetragen werden.</p>
	<a href="admin_createSubject.php" class="btn btn-primary" role="button">Eintragen</a>
	
	<br><br>
	
	<h3 class="adminHeader">Löschen</h3>
	<p>Objekt gibt's nicht mehr? Dann hier über den entsprechenden Button löschen.</p>
	<a href="admin_deleteSubject.php" class="btn btn-primary" role="button">Veranstaltungen löschen</a>
	<a href="admin_deleteLecturerInstituteModule.php" class="btn btn-primary" role="button">Dozenten/Institute/Module löschen</a>

	<br><br>
	
	<h3 class="adminHeader">Bearbeiten</h3>
	<p>Es hat sich was geändert oder beim Eintragen gab's Tippfehler? Das kann hier behoben werden.</p>
	<a href="admin_editSubject.php" class="btn btn-primary" role="button">Veranstaltung bearbeiten</a>
	<a href="admin_editLecturer.php" class="btn btn-primary" role="button">Dozent bearbeiten</a>
	<a href="admin_editInstitute.php" class="btn btn-primary" role="button">Institut bearbeiten</a>
	<a href="admin_editModule.php" class="btn btn-primary" role="button">Modul bearbeiten</a>
	
</div>

</body>
</html>