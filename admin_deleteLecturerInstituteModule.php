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
	<h2>Dozent, Institut oder Modul löschen</h2>
	<hr>
	
	<?php
	if (isset($_POST['btn-deleteLecturer'])){

		$LecturerDeleteID = strip_tags($_POST['lec_select']);
		
		$q1 = mysqli_query($con,"DELETE FROM lecturers WHERE lecturer_ID = '".$LecturerDeleteID."';");
		$q2 = mysqli_query($con,"DELETE FROM lecturers_institutes WHERE lecturer_ID = '".$LecturerDeleteID."';");
		
		if($q1==true AND $q2==true){
			//create message
			$msg_lec = "
				<div class='alert alert-success'>
					<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Der Dozent wurde erfolgreich aus der Datenbank gelöscht.
				</div>
			";
		}
	}
	?>

	<?php
	$sql1 = "
		SELECT lecturers.lecturer_ID, last_name, first_name, subjects_lecturers.subject_ID, institutes.abbr AS institutes_abbr
		FROM lecturers
		LEFT JOIN subjects_lecturers ON lecturers.lecturer_ID = subjects_lecturers.lecturer_ID
        LEFT JOIN lecturers_institutes ON lecturers.lecturer_ID = lecturers_institutes.lecturer_ID
        LEFT JOIN institutes ON lecturers_institutes.institute_ID = institutes.institute_ID
		WHERE subjects_lecturers.subject_ID IS NULL
	";
	$result1 = mysqli_query($con,$sql1);
	
	if(mysqli_num_rows($result1)==0) $disabled1 = "disabled";
	
	$lec_selection = "";
	while($row1 = mysqli_fetch_assoc($result1)){
		$lec_selection .= "<option value=".$row1['lecturer_ID'].">".$row1['last_name'].", ".$row1['first_name']." (".$row1['institutes_abbr'].")</option>";
	}
	?>
	
	<div class="col-md-4">
		<?php if(isset($msg_lec)) echo $msg_lec ?>
		<h3>Dozent löschen</h3>
		<p>Dozenten können nur gelöscht werden, wenn sie mit keiner Veranstaltung mehr verbunden sind. Folgende Dozenten im Dropdown können gelöscht werden:</p>
		<form id="deleteLecturer" method="POST" onsubmit="return confirm('Bist du dir sicher, dass du den ausgewählten Dozenten löschen möchtest?');">
			<div class="form-group">
				<select name="lec_select" class="form-control" required>
					<?php echo $lec_selection ?>
				</select>
			</div>
			<button type="submit" class="btn btn-primary" name="btn-deleteLecturer" <?php if(isset($disabled1)) echo $disabled1 ?>>Dozent löschen</button>
		</form>
	</div>

	<?php
	if (isset($_POST['btn-deleteInstitute'])){

		$InstituteDeleteID = strip_tags($_POST['inst_select']);
		
		$q3 = mysqli_query($con,"DELETE FROM institutes WHERE institute_ID = '".$InstituteDeleteID."';");
		
		if($q3==true){
			//create message
			$msg_inst = "
				<div class='alert alert-success'>
					<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Das Institut wurde erfolgreich aus der Datenbank gelöscht.
				</div>
			";
		}
	}
	?>

	<?php
	$sql2 = "
		SELECT institutes.institute_ID, institutes.name, institutes.abbr
		FROM institutes
		LEFT JOIN lecturers_institutes ON institutes.institute_ID=lecturers_institutes.institute_ID
        WHERE lecturers_institutes.lecturers_institutes_ID IS NULL
	";
	$result2 = mysqli_query($con,$sql2);
	
	if(mysqli_num_rows($result2)==0) $disabled2 = "disabled";
	
	$inst_selection = "";
	while($row2 = mysqli_fetch_assoc($result2)){
		$inst_selection .= "<option value=".$row2['institute_ID'].">".$row2['name']." (".$row2['abbr'].")</option>";
	}
	?>
	
	<div class="col-md-4">
		<?php if(isset($msg_inst)) echo $msg_inst ?>
		<h3>Institut löschen</h3>
		<p>Institute können nur gelöscht werden, wenn sie mit keinem Dozenten mehr verbunden sind. Folgende Institute im Dropdown können gelöscht werden:</p>
		<form id="deleteInstitute" method="POST" onsubmit="return confirm('Bist du dir sicher, dass du das ausgewählte Institut löschen möchtest?');">
			<div class="form-group">
				<select name="inst_select" class="form-control" required>
					<?php echo $inst_selection ?>
				</select>
			</div>
			<button type="submit" class="btn btn-primary" name="btn-deleteInstitute" <?php if(isset($disabled2)) echo $disabled2 ?>>Institut löschen</button>
		</form>
	</div>

	<?php
	if (isset($_POST['btn-deleteModule'])){

		$ModuleDeleteID = strip_tags($_POST['mod_select']);
		
		$q4 = mysqli_query($con,"DELETE FROM modules WHERE module_ID = '".$ModuleDeleteID."';");
		$q5 = mysqli_query($con,"DELETE FROM modules_levels WHERE module_ID = '".$ModuleDeleteID."';");
		
		if($q4==true AND $q5==true){
			//create message
			$msg_mod = "
				<div class='alert alert-success'>
					<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Das Modul wurde erfolgreich aus der Datenbank gelöscht.
				</div>
			";
		}
	}
	?>

	<?php
	$sql3 = "
		SELECT modules.module_ID, modules.code, modules.name
		FROM modules
		LEFT JOIN subjects_modules ON modules.module_ID=subjects_modules.module_ID
        WHERE subjects_modules.subjects_modules_ID IS NULL
	";
	$result3 = mysqli_query($con,$sql3);
	
	if(mysqli_num_rows($result3)==0) $disabled3 = "disabled";
	
	$mod_selection = "";
	while($row3 = mysqli_fetch_assoc($result3)){
		$mod_selection .= "<option value=".$row3['module_ID'].">".$row3['name']." [".$row3['code']."]</option>";
	}
	?>
	
	<div class="col-md-4">
		<?php if(isset($msg_mod)) echo $msg_mod ?>
		<h3>Modul löschen</h3>
		<p>Module können nur gelöscht werden, wenn sie mit keiner Veranstaltung mehr verbunden sind. Folgende Module im Dropdown können gelöscht werden:</p>
		<form id="deleteModule" method="POST" onsubmit="return confirm('Bist du dir sicher, dass du das ausgewählte Modul löschen möchtest?');">
			<div class="form-group">
				<select name="mod_select" class="form-control" required>
					<?php echo $mod_selection ?>
				</select>
			</div>
			<button type="submit" class="btn btn-primary" name="btn-deleteModule" <?php if(isset($disabled3)) echo $disabled3 ?>>Modul löschen</button>
		</form>
	</div>

</div>
</body>
</html>