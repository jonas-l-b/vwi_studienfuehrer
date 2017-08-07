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
	if (isset($_GET['lecturer_id'])){
		$lecturer_id = strval ($_GET['lecturer_id']);
	}
	else{
		//echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=no_lecturer_in_url';</SCRIPT>");
	}
	
	//Moduldatensatz laden
	$sql = "
		SELECT lecturers.lecturer_ID, first_name, last_name, institutes.name AS institute_name, abbr, institutes.institute_ID
		FROM lecturers
		JOIN lecturers_institutes ON lecturers.lecturer_ID = lecturers_institutes.lecturer_ID
		JOIN institutes ON lecturers_institutes.institute_ID = institutes.institute_ID	
		WHERE lecturers.lecturer_ID = '".$lecturer_id."'
	";
	$result = mysqli_query($con,$sql);

	// Check, ob Datensatz existiert (ist der Fall, wenn mindestens ein Ergebnis zurückgegeben wird)
	if (mysqli_num_rows($result) >= 1 ) {
		$lecturerData = mysqli_fetch_assoc($result);
	} else {
		//echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=no_lecturer_in_db';</SCRIPT>");
	}
	
	/*Lade alle Einträge mit mehreren möglichen Einträgen*/
	//subjects
	$sql = "
		SELECT subject_name, subjects.code AS subject_code
		FROM subjects
		JOIN subjects_lecturers ON subjects.ID = subjects_lecturers.subject_ID
		JOIN lecturers ON subjects_lecturers.lecturer_ID = lecturers.lecturer_ID
		WHERE lecturers.lecturer_ID = '".$lecturer_id."'
		ORDER BY subject_name
	";
	$result = mysqli_query($con,$sql);
	$subjects = "";
	while($row = mysqli_fetch_assoc($result)){
		$subjects .= "<a href=\"index.php?subject=".$row['subject_code']."\" target=\"blank\">".$row['subject_name']."</a><br>";
	}
	$subjects = substr($subjects, 0, -4);
	?>
	
	<h2>Dozent: <?php echo $lecturerData['first_name']." ".$lecturerData['last_name']?></h2>
	<hr>
	<table class="table" style="border-top:solid; border-top-color:white">
		<tbody>
			<tr>
				<th>Institut:</th>
				<td><?php echo "<a href=\"institute.php?institute_id=".$lecturerData['institute_ID']."\">".$lecturerData['institute_name']."</a> (".$lecturerData['abbr'].")"?></td>
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