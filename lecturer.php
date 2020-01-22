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
	if (isset($_GET['lecturer_id'])){
		$lecturer_id = strval ($_GET['lecturer_id']);
	}
	else{
		$showMain = "none";
		$showFailedLoad = "";
	}
	
	//Moduldatensatz laden
	$sql = "
		SELECT lecturers.lecturer_ID, name, active
		FROM lecturers
		WHERE lecturers.lecturer_ID = '".$lecturer_id."'
	";
	$result = mysqli_query($con,$sql);

	// Check, ob Datensatz existiert (ist der Fall, wenn mindestens ein Ergebnis zurückgegeben wird)
	if (mysqli_num_rows($result) >= 1 ) {
		$lecturerData = mysqli_fetch_assoc($result);
	} else {
		$showMain = "none";
		$showFailedLoad = "";
	}
	
	/*Lade alle Einträge mit mehreren möglichen Einträgen*/
	//institutes
	$sql = "
		SELECT institutes.institute_ID, name, abbr
		FROM institutes
		JOIN lecturers_institutes ON institutes.institute_ID=lecturers_institutes.institute_ID
		WHERE lecturer_ID = '".$lecturer_id."'
		ORDER BY name
	";
	$result = mysqli_query($con,$sql);
	$institutes = "";
	while($row = mysqli_fetch_assoc($result)){
		$institutes .= "<a href=\"institute.php?institute_id=".$row['institute_ID']."\">".$row['name']." (".$row['abbr'].")</a><br>";			
	}
	$institutes = substr($institutes, 0, -4);

	//subjects
	$sql = "
		SELECT subject_name, subjects.ID AS subject_id
		FROM subjects
		JOIN subjects_lecturers ON subjects.ID = subjects_lecturers.subject_ID
		JOIN lecturers ON subjects_lecturers.lecturer_ID = lecturers.lecturer_ID
		WHERE lecturers.lecturer_ID = '".$lecturer_id."'
		ORDER BY subject_name
	";
	$result = mysqli_query($con,$sql);
	$subjects = "";
	while($row = mysqli_fetch_assoc($result)){
		$subjects .= "<a href=\"index.php?subject=".$row['subject_id']."\">".$row['subject_name']."</a><br>";
	}
	$subjects = substr($subjects, 0, -4);
	?>
	
	<div style="display:<?php echo $showMain?>">
		<p style="margin-bottom:0px; margin-left:1px; font-weight:bold; color:grey; letter-spacing: 0.5px; font-family:open sans">DOZENT</p>
		<h2 style="margin-top:0px"><?php echo $lecturerData['name'] ?></h2>
		<hr>
		
		<?php
		if ($lecturerData['active'] == 0){
			echo '
				<br>
				<div class="alert alert-danger">
					Dieser Dozent existiert im aktuellen Modulhandbuch nicht mehr.
				</div>
			';
		}
		?>
		
		<table class="table" style="border-top:solid; border-top-color:white">
			<tbody>
				<tr>
					<th>Institut(e):</th>
					<td><?php echo $institutes?></td>
				</tr>
				<tr>
					<th>Veranstaltung(en):</th>
					<td><?php echo $subjects?></td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div style="display:<?php echo $showFailedLoad?>">
		Der Dozent konnte nicht geladen werden. Entweder wurde keine Donzenten-ID übergeben oder die übergebene Donzenten-ID existiert nicht in unserer Datenbank.
	</div>

</div>

</body>
</html>