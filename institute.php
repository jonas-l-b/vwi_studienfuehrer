<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div class="container" style="margin-top:60px">
	<?php
	//Variablen, die dafür sorgen, dass im Falle einer fehlerhaften oder fehlenden ID in der URL eine entsprechende Nachricht angezeigt wird
	$showMain = "";
	$showFailedLoad = "none";
	
	// Modul aus URL speichern
	if (isset($_GET['institute_id'])){
		$institute_id = strval ($_GET['institute_id']);
	}
	else{
		$showMain = "none";
		$showFailedLoad = "";
	}
	
	//Moduldatensatz laden
	$sql = "
		SELECT *
		FROM institutes
		WHERE institutes.institute_ID = '".$institute_id."'
	";
	$result = mysqli_query($con,$sql);

	// Check, ob Datensatz existiert (ist der Fall, wenn mindestens ein Ergebnis zurückgegeben wird)
	if (mysqli_num_rows($result) >= 1 ) {
		$instituteData = mysqli_fetch_assoc($result);
	} else {
		$showMain = "none";
		$showFailedLoad = "";
	}
	
	/*Lade alle Einträge mit mehreren möglichen Einträgen*/
	//lecturers
	$sql = "
		SELECT DISTINCT lecturers.lecturer_ID, lecturers.name, institutes.institute_ID, abbr
		FROM lecturers
		JOIN lecturers_institutes ON lecturers.lecturer_ID = lecturers_institutes.lecturer_ID
		JOIN institutes ON lecturers_institutes.institute_ID = institutes.institute_ID
		WHERE institutes.institute_ID = ".$institute_id."
		ORDER BY abbr, lecturers.last_name
	";
	$result = mysqli_query($con,$sql);
	$lecturers = "";
	while($row = mysqli_fetch_assoc($result)){
		$lecturers .= "<li><a href=\"lecturer.php?lecturer_id=".$row['lecturer_ID']."\">".$row['name']."</a></li>";
	}
	$lecturers = substr($lecturers, 0, -5);
	
	//subjects
	$sql = "
		SELECT DISTINCT subject_name, subjects.ID AS subject_id
		FROM subjects
		JOIN subjects_lecturers ON subjects.ID = subjects_lecturers.subject_ID
		JOIN lecturers ON subjects_lecturers.lecturer_ID = lecturers.lecturer_ID
		JOIN lecturers_institutes ON lecturers.lecturer_ID = lecturers_institutes.lecturer_ID
		JOIN institutes ON lecturers_institutes.institute_ID = institutes.institute_ID
		WHERE institutes.institute_ID = '".$institute_id."'
		ORDER BY subject_name
	";
	$result = mysqli_query($con,$sql);
	$subjects = "";
	while($row = mysqli_fetch_assoc($result)){
		$subjects .= "<li><a href=\"index.php?subject=".$row['subject_id']."\">".$row['subject_name']."</a></li>";
	}
	$subjects = substr($subjects, 0, -5);
	
	//modules
	$sql = "
		SELECT DISTINCT modules.module_ID AS module_ID, modules.name AS module_name, levels.name AS level
		FROM modules
		JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
		JOIN levels ON modules_levels.level_ID = levels.level_ID
		JOIN subjects_modules ON modules.module_ID = subjects_modules.module_ID
		JOIN subjects ON subjects_modules.subject_ID = subjects.ID
		JOIN subjects_lecturers ON subjects.ID = subjects_lecturers.subject_ID
		JOIN lecturers ON subjects_lecturers.lecturer_ID = lecturers.lecturer_ID
		JOIN lecturers_institutes ON lecturers.lecturer_ID = lecturers_institutes.lecturer_ID
		JOIN institutes ON lecturers_institutes.institute_ID = institutes.institute_ID
		WHERE institutes.institute_ID = '".$institute_id."'
		ORDER BY levels.name, modules.name
	";
	$result = mysqli_query($con,$sql);
	$modules = "";
	while($row = mysqli_fetch_assoc($result)){
		
		$level="";
		switch($row['level']){
			case "bachelor_basic":
				$level .= "Bachelor: Kernprogramm";
				break;
			case "bachelor":
				$level .= "Bachelor: Vertiefung";
				break;
			case "master":
				$level .= "Master";
				break;
		}
		
		
		
		$modules .= "<li><a href=\"module.php?module_id=".$row['module_ID']."\">".$row['module_name']."</a> (".$level.")</li>";
		$level="";
	}
	$modules = substr($modules, 0, -5);
	?>
	
	<div style="display:<?php echo $showMain?>">
		<p style="margin-bottom:0px; margin-left:1px; font-weight:bold; color:grey; letter-spacing: 0.5px; font-family:open sans">INSTITUT</p>
		<h2 style="margin-top:0px"><?php echo $instituteData['name']." (".$instituteData['abbr'].")"?></h2>
		<hr>
		
		<?php
		if ($instituteData['active'] == 0){
			echo '
				<br>
				<div class="alert alert-danger">
					Dieses Institut existiert im aktuellen Modulhandbuch nicht mehr.
				</div>
			';
		}
		?>
		
		<div class="row">
			<div class="col-md-4">
				<h3>Veranstaltungen</h3>
				<ul>
				<?php echo $subjects?>
				</ul>
			</div>
			<div class="col-md-4">
				<h3>Module</h3>
				<ul>
				<?php echo $modules?>
				</ul>
			</div>
			<div class="col-md-4">
				<h3>Dozenten</h3>
				<ul>
				<?php echo $lecturers?>
				</ul>
			</div>
		</div>
	</div>
	
	<div style="display:<?php echo $showFailedLoad?>">
		Das Institut konnte nicht geladen werden. Entweder wurde keine Institut-ID übergeben oder die übergebene Institut-ID existiert nicht in unserer Datenbank.
	</div>

	<div align="right" style="font-size:90%;">
		<?php
		$result=mysqli_query($con, "SELECT value FROM help WHERE name='infoDate'");
		$row=mysqli_fetch_assoc($result);
		echo "Stand der Informationen: " . $row['value'];
		?>	
	
		<a href="#" data-trigger="focus" data-toggle="dateOfInfo_popover" data-content="
				Die Informationen auf dieser Seite kommen direkt aus dem Modulhandbuch. Wir beziehen diese direkt von der KIT-Seite und verändern sie grundsätzlich nicht.
			">
			<span class="glyphicon glyphicon-question-sign"></span>
		</a>
		<script>$('[data-toggle="dateOfInfo_popover"]').popover();</script>
	</div>

</div>

</body>
</html>
