<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

$InstanceCache->deleteItem("treeside");
$InstanceCache->deleteItem("table_mod_selection");
?>

<?php
if($userRow['admin']==0){
	echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=no_admin';</SCRIPT>");
}
?>

<html>

<head>
	<link rel="stylesheet" href="res/css/sem.css">
</head>

<body>

<?php include "inc/nav.php" ?>

<div class="container" style="margin-top:60px">
	<h2>Modul bearbeiten</h2>
	<hr>

	<?php
	/*Vorbereitung*/
	//Hide Form
	$display = "style=\"display:none\"";

	//unselect all module options
	$result1 = mysqli_query($con,"SELECT * from modules");
	while($row = mysqli_fetch_assoc($result1)){
		$moduleSelection[$row['module_ID']] = "";
	}

	//unselect all institute options
	$result1 = mysqli_query($con,"SELECT * from moduletypes");
	while($row = mysqli_fetch_assoc($result1)){
		$typeSelection[$row['name']] = "";
	}

	//unselect all level options
	$result1 = mysqli_query($con,"SELECT * from levels");
	while($row = mysqli_fetch_assoc($result1)){
		$levelSelection[$row['name']] = "";
	}

	if (isset($_GET['btn-edit'])){ //Wenn Bearbeiten-Button geklickt
		//Show form
		$display = "";

		//Get module selection
		$editID = strip_tags($_GET['select']);

		/*Get data for form values*/
		//data query
		$sql = "
			SELECT modules.module_ID, modules.name AS module_name, code, type, ECTS, levels.name AS level_name
			FROM modules
			JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
			JOIN levels ON modules_levels.level_ID = levels.level_ID
			WHERE modules.module_ID = ".$editID.";
		";

		$result = mysqli_query($con,$sql);
		$row = mysqli_fetch_assoc($result);

		//Select correct lecturer in upper form
		$moduleSelection[$row['module_ID']] = "selected";

		//Get form values
		$name = $row['module_name'];
		$code = $row['code'];

		$typeSelection[$row['type']] = "selected";

		$sql = "
			SELECT DISTINCT levels.name
			FROM modules
			JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
			JOIN levels ON modules_levels.level_ID = levels.level_ID
			WHERE modules.module_ID = ".$editID.";
		";
		$result1 = mysqli_query($con,$sql);
		while($row1 = mysqli_fetch_assoc($result1)){
			$levelSelection[$row1['name']] = "selected";
		}

		$ECTS = $row['ECTS'];
	}

	if (isset($_POST['btn-saveChanges'])){ //Wenn Speichern-Button geklickt
		$display = "style=\"display:none\"";

		//aktuelle ID
		$changeID = strip_tags($_POST['module_ID']);

		//Daten aus Form ziehen
		$name = strip_tags($_POST['name']);
		$code = strip_tags($_POST['code']);
		$type = strip_tags($_POST['type_select']);
		$level_select = $_POST['level_select'];
		$ECTS = strip_tags($_POST['ECTS']);
		$userID = $userRow['user_ID'];

		//modules ändern
		$sql = "
			UPDATE modules
			SET code = '$code', name = '$name', type = '$type', ECTS = '$ECTS', lastChangedBy_ID = '$userID', time_stamp2 = now()
			WHERE module_ID = $changeID;
		";

		$q1 = mysqli_query($con,$sql);

		//Daten in modules_levels löschen...
		$q2 = mysqli_query($con,"DELETE FROM modules_levels WHERE module_ID = '".$changeID."';");

		//...und neu einfügen
		foreach($level_select as $value){
			$row = mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM levels WHERE name = '$value'"));
				$lvl = $row['level_ID'];
				$sql2 = "
					INSERT INTO modules_levels(module_ID, level_ID)
					VALUES ('$changeID', '$lvl');
				";
				$q3 = mysqli_query($con,$sql2);

		}

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
		SELECT *
		FROM modules
		ORDER BY name
	";

	$result1 = mysqli_query($con,$sql1);

	$rows = array();
	while($row1 = mysqli_fetch_assoc($result1)){
		array_push($rows, array(
								"id"=>$row1['module_ID'],
								"subject_name"=>$row1['name'],
								"identifier"=>'['.$row1['code'].']',
								"selected"=>$moduleSelection[$row1['module_ID']]
								));
	}
	?>

	<?php if(isset($msg)) echo $msg ?>

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
			<button type="submit" class="btn btn-primary" id="btn-edit" name="btn-edit">Dieses Modul bearbeiten</button>
		</div>
	</form>

	<div <?php echo $display ?>>

		<br><br>

		<form method="POST">

			<div class="form-group" style="display:none">
				<input value="<?php echo $editID ?>" name="module_ID" type="text" class="form-control"/>
			</div>

			<div class="form-group">
				<label>Modul-Name</label>
				<p>Wie heißt das Modul?</p>
				<input value="<?php echo $name ?>" name="name" type="text" class="form-control" placeholder="Modul-Name" required />
			</div>

			<div class="form-group">
				<label>Kennung</label>
				<p>Welche <strong>Modul</strong>kennung hat das Modul im Modulhandbuch (Modulkennungen beginnen immer mit einem <strong>M</strong>; Bsp.: <strong>M-WIWI-101500</strong>)?</p>
				<input value="<?php echo $code ?>" name="code" type="text" class="form-control" placeholder="Nummer" required />
			</div>

			<?php
			$mod_type1 = mysqli_query($con, "SELECT * FROM moduletypes");
			$mod_type1_selection = "";
			while($mod_type1_row = mysqli_fetch_assoc($mod_type1)){
				$mod_type1_selection .= "<option value=".$mod_type1_row['name']." ".$typeSelection[$mod_type1_row['name']].">".$mod_type1_row['name']."</option>";
			}
			?>
			<div class="form-group">
				<label>Modultyp</label>
				<p>Von welchem Typ ist das Modul?</p>
				<select name="type_select" class="form-control" name="modul_type" required>
					<?php echo $mod_type1_selection ?>
				</select>
			</div>
			
			<div class="form-group">
				<label>Modul-Level</label>
				<p>Wann kann das Modul belegt werden?</p>
				<select id="level_select" name="level_select[]" multiple="" class="search ui fluid dropdown form-control" required>
					<option value="bachelor_basic" <?php echo $levelSelection['bachelor_basic'] ?> >Bachelor: Kernprogramm</option>
					<option value="bachelor" <?php echo $levelSelection['bachelor'] ?>>Bachelor: Vertiefungsprogramm</option>
					<option value="master" <?php echo $levelSelection['master'] ?>>Master</option>
				</select>
			</div>

			<div class="form-group">
				<label>ECTS</label>
				<p>Wie viele ECTS bringt das gesamt Modul ein?</p>
				<input value="<?php echo $ECTS ?>" name="ECTS" type="text" class="form-control" placeholder="ECTS" required />
			</div>

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
