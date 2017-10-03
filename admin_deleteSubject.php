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

<?php include "inc/nav.php" ?>

<div class="container" style="margin-top:60px">
	<h2>Veranstaltung löschen</h2>
	<hr>
	
	<?php
	$q = mysqli_query($con,"SELECT * FROM moduletypes");
	$modulTypes = array();
	while($row = mysqli_fetch_assoc($q)){
		$modulTypes[] .= $row['name'];
	}
		
	//Wenn Veranstaltung löschen gedrückt wird
	foreach ($modulTypes as $type) {
		if (isset($_POST['btn-deleteSubject-'.$type.''])){

			$SubjectDeleteID = strip_tags($_POST['sub_select']);
			
			$q1 = mysqli_query($con,"DELETE FROM subjects WHERE ID = '".$SubjectDeleteID."';");
			$q2 = mysqli_query($con,"DELETE FROM subjects_lecturers WHERE subject_ID = '".$SubjectDeleteID."';");
			$q3 = mysqli_query($con,"DELETE FROM subjects_modules WHERE subject_ID = '".$SubjectDeleteID."';");
			
			if($q1==true AND $q2==true AND $q3==true){
				//create message
				$msg = "
					<div class='alert alert-success'>
						<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Die Veranstaltung wurde erfolgreich aus der Datenbank gelöscht.
					</div>
				";
			}
		}	
	}
	?>

	<?php if(isset($msg)) echo $msg ?>	
	
	<div class="row">
		<?php
		
		foreach ($modulTypes as $type) {
			$sql = "
				SELECT DISTINCT subjects.ID, subjects.subject_name, modules.type
				FROM subjects
				JOIN subjects_modules ON subjects.ID=subjects_modules.subject_ID
				JOIN modules ON subjects_modules.module_ID=modules.module_ID
				WHERE modules.type = '".$type."'
				ORDER BY subjects.subject_name
			";
			
			$sub = mysqli_query($con,$sql);
			
			$sub_selection = "";
			while($sub_row = mysqli_fetch_assoc($sub)){
				$sub_selection .= "<option value=".$sub_row['ID'].">".$sub_row['subject_name']." (".$sub_row['type'].")</option>";
			}
					
			echo "
				<div class=\"col-md-4 well adminDeleteSubject\">
					<form id=\"deleteForm\" method=\"POST\" onsubmit=\"return confirm('Bist du dir sicher, dass du diese Veranstaltung löschen möchtest?');\">
						<h4><strong>".$type."</strong></h4>
						<div class=\"form-group\">
							<p>Welche Veranstaltung soll gelöscht werden?</p>

							<select id=\"sub_select\" name=\"sub_select\" class=\"form-control\" required>
								".$sub_selection."
							</select>
						</div>
						<button type=\"submit\" class=\"btn btn-primary\" name=\"btn-deleteSubject-".$type."\">Veranstaltung löschen</button>
					</form>
				</div>
			";
			
		}
		?>
	</div>
	
</div>
</body>
</html>