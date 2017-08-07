<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div class="container" style="margin-top:60px">
	<?php
	// Modul aus URL speichern
	if (isset($_GET['institute_id'])){
		$institute_id = strval ($_GET['institute_id']);
	}
	else{
		echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=no_institute_in_url';</SCRIPT>");
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
		//echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=no_institute_in_db';</SCRIPT>");
	}
	
	/*Lade alle Einträge mit mehreren möglichen Einträgen*/
	//lecturers
	$sql = "
		SELECT DISTINCT lecturers.lecturer_ID, lecturers.last_name, lecturers.first_name, institutes.institute_ID, abbr
		FROM lecturers
		JOIN lecturers_institutes ON lecturers.lecturer_ID = lecturers_institutes.lecturer_ID
		JOIN institutes ON lecturers_institutes.institute_ID = institutes.institute_ID
		WHERE institutes.institute_ID = ".$institute_id."
		ORDER BY abbr, lecturers.last_name
	";
	$result = mysqli_query($con,$sql);
	$lecturers = "";
	while($row = mysqli_fetch_assoc($result)){
		$lecturers .= "<a href=\"lecturer.php?lecturer_id=".$row['lecturer_ID']."\">".substr($row['first_name'],0,1).". ".$row['last_name']."</a> (<a href=\"institute.php?institute_id=".$row['institute_ID']."\">".$row['abbr']."</a>)<br>";
	}
	$lecturers = substr($lecturers, 0, -4);
	
	//subjects
	$sql = "
		SELECT DISTINCT subject_name, subjects.code AS subject_code
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
		$subjects .= "<a href=\"index.php?subject=".$row['subject_code']."\" target=\"blank\">".$row['subject_name']."</a><br>";
	}
	$subjects = substr($subjects, 0, -4);
	?>
	
	<h2>Institut: <?php echo $instituteData['name']." (".$instituteData['abbr'].")"?></h2>
	<hr>
	<table class="table" style="border-top:solid; border-top-color:white">
		<tbody>
			<tr>
				<th>Dozenten:</th>
				<td><?php echo $lecturers?></td>
			</tr>
			<tr>
				<th>Veranstaltungen:</th>
				<td><?php echo $subjects?></td>
			</tr>
		</tbody>
	</table>


</div>

</body>
</html>
