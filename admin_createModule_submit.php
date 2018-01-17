<?php

include "sessionsStart.php";

include "connect.php";

$InstanceCache->deleteItem("treeside");
$InstanceCache->deleteItem("table_mod_selection");
?>

<?php
$code = $_POST['mod_code'];
$name = $_POST['mod_name'];
$type = $_POST['mod_type'];
$level = $_POST['mod_level'];
$ECTS = $_POST['mod_ECTS'];
	

//Check if already exists
$sql1 = "
	SELECT *
	FROM modules
	WHERE code = '$code' OR name = '$name'
";//WHERE code = '$code' AND name = '$name' AND type = '$type' AND ECTS = '$ECTS';
$result1 = mysqli_query($con,$sql1);

//If no, insert
if (mysqli_num_rows($result1)==0){
	$sql2 = "
		INSERT INTO modules (code, name, type, ECTS, user_ID, time_stamp)
		VALUES ('$code', '$name', '$type', '$ECTS', '$userID', now());
	";

	mysqli_query($con,$sql2);
	//$db_logger->info("Neues Modul hinzugefügt: $name von typ $type von User: $userID" );

	$mod_new_selection = "";

	$mod = mysqli_query($con,"SELECT * FROM modules ORDER BY time_stamp DESC LIMIT 1;");
	while($mod_row = mysqli_fetch_assoc($mod)){
		$mod_new_selection .= "<option value=".$mod_row['module_ID'].">".$mod_row['name']." [".$mod_row['code']."]</option>";
		$new_mod_id = $mod_row['module_ID'];
	}

	echo $mod_new_selection;
	
	//Add connection to modules_levels
	$level_array = explode(",",$level);

	$string = "";
	foreach($level_array as $value) {
		$string .= "name = '".$value."' OR ";
	}
	
	$string = substr($string, 0, -4);
	
	$sql3 = "
		SELECT *
		FROM levels
		WHERE ".$string.";
	";
	
	$result3 = mysqli_query($con,$sql3);
	
	while($row3 = mysqli_fetch_assoc($result3)){
		$level_ID = $row3['level_ID'];
		
		$sql4 = "
			INSERT INTO modules_levels (module_ID, level_ID)
			VALUES ('$new_mod_id', '$level_ID');
		";
		mysqli_query($con,$sql4);
	}
} else{
	echo "existsAlready";
}

?>