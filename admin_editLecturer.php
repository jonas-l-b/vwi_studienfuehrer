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
<body>

<?php include "inc/nav.php" ?>

<div class="container" style="margin-top:60px">
	<h2>Dozent bearbeiten</h2>
	<hr>

	<?php
	/*Vorbereitung*/
	//Hide Form
	$display = "style=\"display:none\"";
	
	//unselect all lecturer options
	$result1 = mysqli_query($con,"SELECT * from lecturers");
	while($row_lec = mysqli_fetch_assoc($result1)){
		$lecturerSelection[$row_lec['lecturer_ID']] = "";
	}
	
	//unselect all institute options
	$result1 = mysqli_query($con,"SELECT * from institutes");
	while($row = mysqli_fetch_assoc($result1)){
		$instituteSelection[$row['institute_ID']] = "";
	}

	if (isset($_GET['btn-edit'])){ //Wenn Bearbeiten-Button geklickt
		//Show form
		$display = "";
		
		//Get lecturer selection
		$editID = strip_tags($_GET['select']);
		
		/*Get data for form values*/
		//data query
		$sql = "
			SELECT lecturers.lecturer_ID, lecturers.first_name, lecturers.last_name, institutes.institute_ID
			FROM lecturers
			JOIN lecturers_institutes ON lecturers.lecturer_ID = lecturers_institutes.lecturer_ID
			JOIN institutes ON lecturers_institutes.institute_ID = institutes.institute_ID
			WHERE lecturers.lecturer_ID = ".$editID.";
		";
		
		$result = mysqli_query($con,$sql);
		$row = mysqli_fetch_assoc($result);
		
		//Select correct lecturer in upper form
		$lecturerSelection[$row['lecturer_ID']] = "selected";
		
		//Get form values
		$first_name = $row['first_name'];
		$last_name = $row['last_name'];
		
		$instituteSelection[$row['institute_ID']] = "selected";

	}
	
	if (isset($_POST['btn-saveChanges'])){ //Wenn Speichern-Button geklickt
		$display = "style=\"display:none\"";
		
		//aktuelle ID
		$changeID = strip_tags($_POST['lecturer_ID']);
		
		//Daten aus Form ziehen
		$first_name = strip_tags($_POST['first_name']);
		$last_name = strip_tags($_POST['last_name']);
		$institute_select = strip_tags($_POST['institute_select']);
		$userID = $userRow['user_ID'];
		
		//lecturers ändern
		$sql = "
			UPDATE lecturers
			SET first_name = '$first_name', last_name = '$last_name', lastChangedBy_ID = '$userID', time_stamp2 = now()
			WHERE lecturer_ID = $changeID;
		";
		
		$q1 = mysqli_query($con,$sql);
		
		//Daten in lecturers_institutes löschen...
		$q2 = mysqli_query($con,"DELETE FROM lecturers_institutes WHERE lecturer_ID = '".$changeID."';");
		
		//...und neu einfügen
		$sql = "
			INSERT INTO lecturers_institutes(lecturer_ID, institute_ID)
			VALUES ('$changeID', '$institute_select');
		";
		$q3 = mysqli_query($con,$sql);
		
		if($q1==true AND $q2==true AND $q3==true){	
			$msg = "
				<div class='alert alert-success'>
					<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Die Änderungen wurden erfolgreich gespeichert.
				</div>
			";
		}
	}
	
	if (isset($_POST['btn-cancel'])){ //Wenn Abbrechen-Button geklickt
		$display = "style=\"display:none\"";
	}
	
	?>
	
	<?php
	$sql1 = "
		SELECT lecturers.lecturer_ID, lecturers.first_name, lecturers.last_name, institutes.abbr AS institute_abbr
		FROM lecturers
		JOIN lecturers_institutes ON lecturers.lecturer_ID = lecturers_institutes.lecturer_ID
		JOIN institutes ON lecturers_institutes.institute_ID = institutes.institute_ID
		ORDER BY institute_abbr, lecturers.last_name
	";
	
	$result1 = mysqli_query($con,$sql1);
	
	$rows = array();
	while($row1 = mysqli_fetch_assoc($result1)){
		array_push($rows, array(
								"id"=>$row1['lecturer_ID'],
								"subject_name"=>$row1['last_name'].", ".$row1['first_name'],
								"identifier"=>'('.$row1['institute_abbr'].')',
								"selected"=>$lecturerSelection[$row1['lecturer_ID']]
								));
	}	
	?>
	
	<?php if(isset($msg)) echo $msg ?>
	
	<?php
		echo $twig->render('admin_edit_auswahl_form.template.html', 
							array(	'rows' => $rows,
									'buttontext' => 'Diesen Dozenten bearbeiten'));
	?>
	
	<div <?php echo $display ?>>
	
		<br><br>

		<form method="POST">
		
			<div class="form-group">
				<label>Vorname</label>
				<input value="<?php echo $first_name ?>" name="first_name" type="text" class="form-control" placeholder="Vorname" required />
			</div>
			
			<div class="form-group" style="display:none">
				<input value="<?php echo $editID ?>" name="lecturer_ID" type="text" class="form-control"/>
			</div>
			
			<hr>
			
			<div class="form-group">
				<label>Nachname</label>
				<input value="<?php echo $last_name ?>" name="last_name" type="text" class="form-control" placeholder="Nachname" required />
			</div>
			
			<hr>
			
			<?php
			$insti = mysqli_query($con, "SELECT * FROM institutes ORDER BY name");
			$insti_selection = "";
			while($insti_row = mysqli_fetch_assoc($insti)){
				$insti_selection .= "<option value=".$insti_row['institute_ID']." ".$instituteSelection[$insti_row['institute_ID']].">".$insti_row['name']." (".$insti_row['abbr'].")</option>";
			}
			?>
			<div class="form-group">
				<label>Institut</label>
				<p><i>Falls gewünschtes Institut nicht in Dropdown vorhanden ist, muss es erst noch hinzugefügt werden. Dazu <a href="admin_createSubject.php" target="_blank">hier</a> klicken (neues Fenster; diese Seite muss dann aktualisiert werden).</i></p>
				<select name="institute_select" class="form-control" required>
					<?php echo $insti_selection ?>
				</select>
			</div>
							
			
			<button type="submit" class="btn btn-primary" name="btn-saveChanges">Änderungen speichern</button>
			<button type="submit" class="btn" name="btn-cancel" formnovalidate>Abbrechen</button>
			
		</form>
	</div>



</div>

</body>
</html>