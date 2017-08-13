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
	
	//unselect all smester and language options
	$semesterSelection['Winter'] = "";
	$semesterSelection['Sommer'] = "";
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
			SELECT ID, subject_name, subjects.code AS subject_code, identifier, lv_number, subjects.ECTS AS subject_ECTS, lecturers.lecturer_ID, semester, language
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
		$sub_code = $row1['subject_code'];
		$sub_identifier = $row1['identifier'];
		$sub_lv_number = $row1['lv_number'];
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
		$code = strip_tags($_POST['code']);
		$identifier = strip_tags($_POST['identifier']);
		$lv_number = strip_tags($_POST['lv_number']);				
		$ECTS = strip_tags($_POST['ECTS']);
		$lec_select = $_POST['lec_select'];				
		$mod_select = $_POST['mod_select'];				
		$semester = strip_tags($_POST['semester']);
		$language = strip_tags($_POST['language']);
		$userID = $userRow['user_ID'];
		
		//subjects ändern
		$sql = "
			UPDATE subjects
			SET subject_name = '$subject_name', code = '$code', identifier = '$identifier', lv_number = '$lv_number', ECTS = '$ECTS', semester = '$semester', language = '$language', lastChangedBy_ID = '$userID', time_stamp2 = now()
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
	
	$selection = "";
	while($row1 = mysqli_fetch_assoc($result1)){
		$selection .= "<option value=".$row1['ID']." ".$subjectSelection[$row1['ID']].">".$row1['subject_name']." [".$row1['identifier']."]</option>";
	}	
	?>
	
	<?php if(isset($msg)) echo $msg ?>
	<form class="form-inline" method="GET">
		<div class="form-group">
			<select name="select" class="form-control" required>
				<?php echo $selection ?>
			</select>
		</div>
		<button type="submit" class="btn btn-primary" name="btn-edit">Diese Veranstaltung bearbeiten</button>
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
				<label>Veranstaltungskürzel</label>
				<p>Kürzel für die Veranstaltung, der in der URL erscheint (Bsp.: index.php?subject=<strong>weku1</strong>). Dieses Kürzel musst du dir ausdenken. Leerzeichen sind nicht erlaubt.</p>
				<input value="<?php echo $sub_code ?>" name="code" type="text" class="form-control" placeholder="Veranstaltungskürzel" required />
			</div>
			
			<hr>
			
			<div class="form-group">
				<label>Kennung</label>
				<p>Welche <strong>Veranstaltungs</strong>kennung hat die Veranstaltung im Modulhandbuch (Veranstaltungskennungen beginnen immer mit einem <strong>T</strong>; Bsp.: <strong>"T-WIWI-102861"</strong>)?
				<input value="<?php echo $sub_identifier ?>" name="identifier" type="text" class="form-control" placeholder="Kennung" required />
			</div>
			
			<hr>
			
			<div class="form-group">
				<label>LV.-Nummer</label>
				<p>Welche LV.-nummer hat die Veranstaltung im Modulhandbuch (LV.-Nummern bestehen nur aus Zahlen und finden sich im Modulhandbuch auf der jeweiligen Seite der Veranstaltung; Bsp.: <strong>"2521533"</strong>)?
				<input value="<?php echo $sub_lv_number ?>" name="lv_number" type="text" class="form-control" placeholder="LV.-Nummer" required />
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
			
			$sql = "
				SELECT *
				FROM lecturers
				JOIN lecturers_institutes ON lecturers.lecturer_ID=lecturers_institutes.lecturer_ID
				JOIN institutes ON lecturers_institutes.institute_ID=institutes.institute_ID
				ORDER BY name, last_name
			";
			
			$lec = mysqli_query($con,$sql);
			
			while($lec_row = mysqli_fetch_assoc($lec)){
				$lec_selection .= "<option value=".$lec_row['lecturer_ID']." ".$lecturerSelection[$lec_row['lecturer_ID']]." >".$lec_row['last_name'].", ".$lec_row['first_name']." (".$lec_row['abbr'].")</option>";
			}
			
			?>
			
			<div class="form-group">
				<label>Dozent(en)</label>
				<p>Wer verantwortet die Veranstaltung?</p>
				<p><i>Falls gewünschter Dozent nicht in Dropdown vorhanden ist, muss er erst noch hinzugefügt werden. Dazu <a href="admin_createSubject.php" target="blank">hier</a> klicken (neues Fenster; diese Seite muss dann aktualisiert werden).</i></p>
				<p><i>Durch Gedrückthalten von STRG mehrere Dozenten auswählen. <strong>Hinweis: Wenn man einfach in die Asuwahl klickt, sind alle vorausgewählten Einträge nicht mehr markiert. Um vorausgewählte Einträge zu behalten, <u>auf die Scrollbar der Auswahl klicken</u> und dann durch Gedrückthalten von STRG Einträge an- und abwählen. Bei Fehlern mit Button ganz unten Abbrechen und erneut versuchen.</strong></i></p>
				<select id="lec_select" name="lec_select[]" multiple class="form-control" required>
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
				<p><i>Falls gewünschtes Modul nicht in Dropdown vorhanden ist, muss es erst noch hinzugefügt werden. Dazu <a href="admin_createSubject.php" target="blank">hier</a> klicken (neues Fenster; diese Seite muss dann aktualisiert werden).</i></p>
				<p><i>Durch Gedrückthalten von STRG mehrere Module auswählen. <strong>Hinweis: Wenn man einfach in die Asuwahl klickt, sind alle vorausgewählten Einträge nicht mehr markiert. Um vorausgewählte Einträge zu behalten, <u>auf die Scrollbar der Auswahl klicken</u> und dann durch Gedrückthalten von STRG Einträge an- und abwählen. Bei Fehlern mit Button ganz unten Abbrechen und erneut versuchen.</strong></i></p>
				<select id="mod_select" name="mod_select[]" multiple class="form-control" required>
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

</body>
</html>