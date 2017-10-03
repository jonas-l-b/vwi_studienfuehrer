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
	<h2>Institut bearbeiten</h2>
	<hr>

	<?php
	/*Vorbereitung*/
	//Hide Form
	$display = "style=\"display:none\"";
	
	//unselect all institute options
	$result1 = mysqli_query($con,"SELECT * from institutes");
	while($row = mysqli_fetch_assoc($result1)){
		$instituteSelection[$row['institute_ID']] = "";
	}
	
	if (isset($_GET['btn-edit'])){ //Wenn Bearbeiten-Button geklickt
		//Show form
		$display = "";
		
		//Get institute selection
		$editID = strip_tags($_GET['select']);
		
		/*Get data for form values*/
		//data query
		$sql = "
			SELECT *
			FROM institutes
			WHERE institute_ID = ".$editID.";
		";
		
		$result = mysqli_query($con,$sql);
		$row = mysqli_fetch_assoc($result);
		
		//Select correct institute in upper form
		$instituteSelection[$row['institute_ID']] = "selected";
		
		//Get form values
		$name = $row['name'];
		$abbr = $row['abbr'];
	}
	
	if (isset($_POST['btn-saveChanges'])){ //Wenn Speichern-Button geklickt
		$display = "style=\"display:none\"";
		
		//aktuelle ID
		$changeID = strip_tags($_POST['institute_ID']);
		
		//Daten aus Form ziehen
		$name = strip_tags($_POST['name']);
		$abbr = strip_tags($_POST['abbr']);
		$userID = $userRow['user_ID'];
		
		//lecturers ändern
		$sql = "
			UPDATE institutes
			SET name = '$name', abbr = '$abbr', lastChangedBy_ID = '$userID', time_stamp2 = now()
			WHERE institute_ID = $changeID;
		";
		
		$q1 = mysqli_query($con,$sql);
		
		if($q1==true){	
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
		SELECT *
		FROM institutes
		ORDER BY name
	";
	
	$result1 = mysqli_query($con,$sql1);
	
	$rows = array();
	while($row1 = mysqli_fetch_assoc($result1)){
		array_push($rows, array(
								"id"=>$row1['institute_ID'],
								"subject_name"=>$row1['name'],
								"identifier"=>'('.$row1['abbr'].')',
								"selected"=>$instituteSelection[$row1['institute_ID']]
								));
	}	
	?>
	
	<?php if(isset($msg)) echo $msg ?>
	
	<!-- COMBOBOX -->
	
	<?php
		echo $twig->render('admin_edit_auswahl_form.template.html', 
							array(	'rows' => $rows,
									'buttontext' => 'Dieses Institut bearbeiten'));
	?>
	
	<div <?php echo $display ?>>
	
		<br><br>

<!--HIER GEHTS LOS-->
		
		<form method="POST">
		
			<div class="form-group">
				<label>Name</label>
				<input value="<?php echo $name ?>" name="name" type="text" class="form-control" placeholder="Name" required />
			</div>
			
			<div class="form-group" style="display:none">
				<input value="<?php echo $editID ?>" name="institute_ID" type="text" class="form-control"/>
			</div>
			
			<hr>
			
			<div class="form-group">
				<label>Abkürzung</label>
				<input value="<?php echo $abbr ?>" name="abbr" type="text" class="form-control" placeholder="Abkürzung" required />
			</div>
						
			<button type="submit" class="btn btn-primary" name="btn-saveChanges">Änderungen speichern</button>
			<button type="submit" class="btn" name="btn-cancel" formnovalidate>Abbrechen</button>
			
		</form>
	</div>



</div>

</body>
</html>