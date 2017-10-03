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
	if (isset($_GET['module_id'])){
		$module_id = strval ($_GET['module_id']);
	}
	else{
		echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=no_module_in_url';</SCRIPT>");
	}
	
	//Moduldatensatz laden
	$sql = "
		SELECT code, modules.name AS module_name, type, ects
		FROM modules
		WHERE modules.module_ID = '".$module_id."'
	";
	$result = mysqli_query($con,$sql);

	// Check, ob Datensatz existiert (ist der Fall, wenn mindestens ein Ergebnis zurückgegeben wird)
	if (mysqli_num_rows($result) >= 1 ) {
		$moduleData = mysqli_fetch_assoc($result);
	} else {
		echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=no_module_in_db';</SCRIPT>");
	}
	
	/*Lade alle Einträge mit mehreren möglichen Einträgen*/
	//levels
	$sql = "
		SELECT levels.name AS level_name
		FROM modules
		JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
		JOIN levels ON modules_levels.level_ID = levels.level_ID
		WHERE modules.module_ID = '".$module_id."'
		ORDER BY CASE
			when levels.name = 'bachelor_basic' then 1
			when levels.name = 'bachelor' then 2
			when levels.name = 'master' then 3
		END
	";
	$result = mysqli_query($con,$sql);
	$levels = "";
	while($row = mysqli_fetch_assoc($result)){
		switch($row['level_name']){
			case "bachelor_basic":
				$levels .= "Bachelor: Kernprogramm"."<br>";
				break;
			case "bachelor":
				$levels .= "Bachelor: Vertiefung"."<br>";
				break;
			case "master":
				$levels .= "Master"."<br>";
				break;
		}
	}
	$levels = substr($levels, 0, -4);
	
	//subjects
	$sql = "
		SELECT subject_name, subjects.code AS subject_code
		FROM subjects
		JOIN subjects_modules ON subjects.ID = subjects_modules.subject_ID
		JOIN modules ON subjects_modules.module_ID = modules.module_ID
		WHERE modules.module_ID = '".$module_id."'
		ORDER BY subject_name
	";
	$result = mysqli_query($con,$sql);
	$subjects = "";
	while($row = mysqli_fetch_assoc($result)){
		$subjects .= "<a href=\"index.php?subject=".$row['subject_code']."\">".$row['subject_name']."</a><br>";
	}
	$subjects = substr($subjects, 0, -4);
	?>
	
	<h2>Modul: <?php echo $moduleData['module_name']?></h2>
	<hr>
	<table class="table" style="border-top:solid; border-top-color:white">
		<tbody>
			<tr>
				<th>Kennung:</th>
				<td><?php echo $moduleData['code']?></td>
			</tr>
			<tr>
				<th>Level:</th>
				<td><?php echo $levels?></td>
			</tr>
			<tr>
				<th>Typ:</th>
				<td><?php echo $moduleData['type']?></td>
			</tr>
			<tr>
				<th>ECTS:</th>
				<td><?php echo $moduleData['ects']?></td>
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