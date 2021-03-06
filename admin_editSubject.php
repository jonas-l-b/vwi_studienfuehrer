<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>

<?php
if($userRow['admin']==0){
	echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=no_admin';</SCRIPT>");
}

$InstanceCache->deleteItem("treeside");
?>

<head>
	<link rel="stylesheet" href="res/css/sem.css">
</head>

<body>

<?php include "inc/nav.php" ?>

<div class="container" style="margin-top:60px">
	<h2>Veranstaltung bearbeiten</h2>
	<hr>

	<?php
	/*Vorbereitung*/
	//Hide Form
	$display = "style=\"display:none\"";
	
	//Unselect all subject options
	$result1 = mysqli_query($con,"SELECT * from subjects");
	while($row_sub = mysqli_fetch_assoc($result1)){
		$subjectSelection[$row_sub['ID']] = "";
	}
	
	//unselect all lecturer options
	$result2 = mysqli_query($con,"SELECT * from lecturers");
	while($row_lec = mysqli_fetch_assoc($result2)){
		$lecturerSelection[$row_lec['lecturer_ID']] = "";
	}
	
	//unselect all modules options
	$result3 = mysqli_query($con,"SELECT * from modules");
	while($row_mod = mysqli_fetch_assoc($result3)){
		$moduleSelection[$row_mod['module_ID']] = "";
	}
	
	//unselect all semester and language options
	$semesterSelection['Winter'] = "";
	$semesterSelection['Sommer'] = "";
	$semesterSelection['Ganzjährig'] = "";
	$semesterSelection['Unregelmäßig'] = "";
	$languageSelection['Deutsch'] = "";
	$languageSelection['Englisch'] = "";
	
	if (isset($_GET['btn-edit'])){ //Wenn Bearbeiten-Button geklickt
		//Show form
		$display = "";
		
		//Get subject selection
		$editID = strip_tags($_GET['select']);
		
		/*Get data for form values*/
		//data query
		$sql1 = "
			SELECT ID, subject_name, identifier, subjects.ECTS AS subject_ECTS, lecturers.lecturer_ID, semester, language
			FROM subjects
			JOIN subjects_lecturers ON subjects.ID = subjects_lecturers.subject_ID
			JOIN lecturers ON subjects_lecturers.lecturer_ID = lecturers.lecturer_ID
			JOIN lecturers_institutes ON lecturers.lecturer_ID = lecturers_institutes.lecturer_ID
			JOIN institutes ON lecturers_institutes.institute_ID = institutes.institute_ID
			JOIN subjects_modules ON subjects.ID = subjects_modules.subject_ID
			JOIN modules ON subjects_modules.module_ID = modules.module_ID
			WHERE subjects.ID = '".$editID."';
		";
		
		$result1 = mysqli_query($con,$sql1);
		$row1 = mysqli_fetch_assoc($result1);
		
		//Select correct subject in upper form
		$subjectSelection[$row1['ID']] = "selected";
		
		//Get form values
		$sub_name = $row1['subject_name'];
		$sub_identifier = $row1['identifier'];
		$sub_ECTS = $row1['subject_ECTS'];
		
		$sql2 = "
			SELECT DISTINCT lecturers.lecturer_ID
			FROM subjects
			JOIN subjects_lecturers ON subjects.ID = subjects_lecturers.subject_ID
			JOIN lecturers ON subjects_lecturers.lecturer_ID = lecturers.lecturer_ID
			WHERE subjects.ID = '".$editID."';
		";
		$result2 = mysqli_query($con,$sql2);
		while($row2 = mysqli_fetch_assoc($result2)){
			$lecturerSelection[$row2['lecturer_ID']] = "selected";
		}
		
		$sql3 = "
			SELECT DISTINCT modules.module_ID
			FROM subjects
			JOIN subjects_modules ON subjects.ID = subjects_modules.subject_ID
			JOIN modules ON subjects_modules.module_ID = modules.module_ID
			WHERE subjects.ID = '".$editID."';
		";
		$result3 = mysqli_query($con,$sql3);
		while($row3 = mysqli_fetch_assoc($result3)){
			$moduleSelection[$row3['module_ID']] = "selected";
		}
		
		$semesterSelection[$row1['semester']] = "selected";
		$languageSelection[$row1['language']] = "selected";
	}
	
	if (isset($_POST['btn-saveChanges'])){ //Wenn Speichern-Button geklickt
		
	
		$display = "style=\"display:none\"";
		
		//ID der aktuellen Veranstaltung
		$changeID = strip_tags($_POST['subject_ID']);
		
		//Daten aus Form ziehen
		$subject_name = strip_tags($_POST['subject_name']);
		$identifier = strip_tags($_POST['identifier']);			
		$ECTS = strip_tags($_POST['ECTS']);
		$lec_select = $_POST['lec_select'];				
		$mod_select = $_POST['mod_select'];				
		$semester = strip_tags($_POST['semester']);
		$language = strip_tags($_POST['language']);
		$userID = $userRow['user_ID'];
		
		//subjects ändern
		$sql = "
			UPDATE subjects
			SET subject_name = '$subject_name', identifier = '$identifier', ECTS = '$ECTS', semester = '$semester', language = '$language', lastChangedBy_ID = '$userID', time_stamp2 = now()
			WHERE ID = $changeID;
		";
		
		$q1 = mysqli_query($con,$sql);
		
		//Daten in Verbindungstabellen löschen...
		$q2 = mysqli_query($con,"DELETE FROM subjects_lecturers WHERE subject_ID = '".$changeID."';");
		$q3 = mysqli_query($con,"DELETE FROM subjects_modules WHERE subject_ID = '".$changeID."';");
		
		//...und neu einfügen (-> Module und Dozenten aktualisieren)
		foreach($lec_select as $value){ //subjects_lecturers
			$sql2 = "
				INSERT INTO subjects_lecturers(subject_ID, lecturer_ID)
				VALUES ('$changeID', '$value');
			";
			$q4 = mysqli_query($con,$sql2);
		}
		
		foreach($mod_select as $value){ //subjects_modules
			$sql3 = "
				INSERT INTO subjects_modules(subject_ID, module_ID)
				VALUES ('$changeID', '$value');
			";
			$q5 = mysqli_query($con,$sql3);
		}
		
		if($q1==true AND $q2==true AND $q3==true AND $q4==true AND $q5==true){	
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
	$result1 = mysqli_query($con,"SELECT * FROM subjects");
	
	$rows = array();
	while($row1 = mysqli_fetch_assoc($result1)){
		array_push($rows, array(
								"id"=>$row1['ID'],
								"subject_name"=>$row1['subject_name'],
								"identifier"=>'['.$row1['identifier'].']',
								"selected"=>$subjectSelection[$row1['ID']]
								));
	}
	?>
	
	<?php if(isset($msg)) echo $msg ?>
	
	<!-- COMBOBOX -->
	<form class="form-inline" method="GET">
		<div class="form-group">
			<?php
			$rand_id = rand();
			?>
			<select id="combobox<?php echo $rand_id?>" class="combobox form-control input-large" name="select" required>
				<option></option>
				<?php
				foreach ($rows as &$row) {
					echo "<option value='".$row['id']."' ".$row['selected'].">".$row['subject_name']." ".$row['identifier']."</option>";
				}
				?>
			</select>
			<script>
				$(document).ready(function(){
					$('#combobox<?php echo $rand_id?>').combobox();
				});
			</script>
			<button type="submit" class="btn btn-primary" id="btn-edit" name="btn-edit">Diese Veranstaltung bearbeiten</button>
		</div>
	</form>
	
	<div <?php echo $display ?>>
	
		<br><br>
		
		<form id="createSubjectForm" method="POST">
							
			<div class="form-group">
				<label>Veranstaltungsname</label>
				<p>Wie heißt die Veranstaltung? Bitte den vollständigen Veranstaltungen angeben; dazu am Modulhandbuch orientieren.</p>
				<input value="<?php echo $sub_name ?>" name="subject_name" type="text" class="form-control" placeholder="Veranstaltungsname" required />
			</div>
			
			<div class="form-group" style="display:none">
				<input value="<?php echo $editID ?>" name="subject_ID" type="text" class="form-control"/>
			</div>
			
			<hr>
			
			<div class="form-group">
				<label>Kennung</label>
				<p>Welche <strong>Veranstaltungs</strong>kennung hat die Veranstaltung im Modulhandbuch (Veranstaltungskennungen beginnen immer mit einem <strong>T</strong>; Bsp.: <strong>"T-WIWI-102861"</strong>)?
				<input value="<?php echo $sub_identifier ?>" name="identifier" type="text" class="form-control" placeholder="Kennung" required />
			</div>
			
			<hr>
			
			<div class="form-group">
				<label>ECTS</label>
				<p>Wie viele ECTS bringt die Veranstaltung ein?</p>
				<input value="<?php echo $sub_ECTS ?>" name="ECTS" type="text" class="form-control" placeholder="ECTS" required />
			</div>
			
			<hr>


			<?php
			$lec_selection = "";

			$lec = mysqli_query($con,"
					SELECT lecturers.lecturer_ID, last_name, first_name
					FROM lecturers
				");
			while($lec_row = mysqli_fetch_assoc($lec)){
				$sql_abbr = mysqli_query($con,"
						SELECT *
						FROM institutes
						JOIN lecturers_institutes ON institutes.institute_ID=lecturers_institutes.institute_ID
						WHERE lecturer_ID = '".$lec_row['lecturer_ID']."'
					");
				$abbr = "";
				while($abbr_row = mysqli_fetch_assoc($sql_abbr)){
					$abbr .= $abbr_row['abbr'] . ", ";
				}
				$abbr = substr($abbr, 0, -2);

				$lec_selection .= "<option value=".$lec_row['lecturer_ID']." ".$lecturerSelection[$lec_row['lecturer_ID']].">".$lec_row['last_name'].", ".$lec_row['first_name']." (".$abbr.")</option>";
			}
			?>

			<div class="form-group">
				<label>Dozent(en)</label>
				<p>Wer verantwortet die Veranstaltung?</p>
				<p><i>Falls gewünschter Dozent nicht in Dropdown vorhanden ist, muss er erst noch hinzugefügt werden. Dazu <a href="admin_createSubject.php" target="_blank">hier</a> klicken (neues Fenster; diese Seite muss dann aktualisiert werden).</i></p>
				<select id="lec_select" name="lec_select[]" multiple="" class="search ui fluid dropdown form-control" required>
					<?php echo $lec_selection ?>
				</select>
			</div>
		
			<hr>
			
			<?php
			$mod_selection = "";

			$mod = mysqli_query($con,"SELECT * FROM modules ORDER BY name");

			while($mod_row = mysqli_fetch_assoc($mod)){
				$mod_selection .= "<option value=".$mod_row['module_ID']." ".$moduleSelection[$mod_row['module_ID']].">".$mod_row['name']." [".$mod_row['code']."]</option>";
			}
			?>

			<div class="form-group">
				<label>Teil der Module</label>
				<p>Welchen Modulen ist die Veranstaltung zuzuordnen?</p>
				<p><i>Falls gewünschtes Modul nicht in Dropdown vorhanden ist, muss es erst noch hinzugefügt werden. Dazu <a href="admin_createSubject.php" target="_blank">hier</a> klicken (neues Fenster; diese Seite muss dann aktualisiert werden).</i></p>
				<select id="mod_select" name="mod_select[]" multiple="" class="search ui fluid dropdown form-control" required>
					<?php echo $mod_selection ?>
				</select>
			</div>		
	
			<hr>
			
			<div class="form-group">
				<label>Semester</label>
				<p>In welchem Turnus findet die Veranstaltung statt?</p>
				<select name="semester" class="form-control" required>
					<option value="Winter" <?php echo $semesterSelection['Winter'] ?>>Winter</option>
					<option value="Sommer" <?php echo $semesterSelection['Sommer'] ?>>Sommer</option>
					<option value="Ganzjährig" <?php echo $semesterSelection['Ganzjährig'] ?>>Ganzjährig</option>
					<option value="Unregelmäßig" <?php echo $semesterSelection['Unregelmäßig'] ?>>Unregelmäßig</option>
				</select>
			</div>
			
			<hr>
			
			<div class="form-group">
				<label>Sprache</label>
				<p>In welcher Sprache findet die Veranstaltung statt?</p>
				<select name="language" class="form-control" required>
					<option value="Deutsch" <?php echo $languageSelection['Deutsch'] ?>>Deutsch</option>
					<option value="Englisch" <?php echo $languageSelection['Englisch'] ?>>Englisch</option>
				</select>
			</div>

			<hr>
			
			<button type="submit" class="btn btn-primary" name="btn-saveChanges">Änderungen speichern</button>
			<button type="submit" class="btn" name="btn-cancel" formnovalidate>Abbrechen</button>
			
		</form>
	</div>

</div>

<script>
$('.ui.dropdown')
  .dropdown({
    fullTextSearch: true,
	useLabels: true
  })
;
</script>

</body>
</html>