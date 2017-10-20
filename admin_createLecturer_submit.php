<?php

include "sessionsStart.php";

include "connect.php";

$InstanceCache->deleteItem("table_lec_selection");
?>

<?php

$first_name = $_POST['first_name1'];
$last_name = $_POST['last_name1'];
$institute = $_POST['institute1'];
$userID = $userRow['user_ID'];

//Check if already exists
$sql1 = "
	SELECT *
	FROM lecturers
	JOIN lecturers_institutes ON lecturers.lecturer_ID=lecturers_institutes.lecturer_ID
	JOIN institutes ON lecturers_institutes.institute_ID=institutes.institute_ID
	WHERE first_name = '$first_name' AND last_name = '$last_name' AND institutes.institute_ID = '$institute';
";
$result1 = mysqli_query($con,$sql1);

//If no, add to lecturers
if (mysqli_num_rows($result1)==0){
	$sql2 = "
		INSERT INTO lecturers (first_name, last_name, user_ID, time_stamp)
		VALUES ('$first_name', '$last_name', '$userID', now());
	";
	mysqli_query($con,$sql2);
	//$db_logger->info("Neuer Dozent hinzugefügt: $first_name $last_name von User: $userID" );

	//Add connection to lecturers_institutes (Verbindung zuerst ist wichtig, damit beim Hinzufügen zur Auswahl im nächsten Schritt auch der richtige (neuste) Dozent ausgewählt wird. Andernfalls ist ihm ja noch kein Institut zugewiesen!)
	$result = mysqli_query($con,"SELECT * FROM lecturers ORDER BY time_stamp DESC LIMIT 1");
	$row = mysqli_fetch_assoc($result);
	$new_lec_id = $row['lecturer_ID'];
	
	$sql3 = "
		INSERT INTO lecturers_institutes (lecturer_ID, institute_ID)
		VALUES ('$new_lec_id', '$institute');
	";
	mysqli_query($con,$sql3);	
	
	//Add new lecturer to selection
	$lec_new_selection = "";

	$lec = mysqli_query($con,"
			SELECT lecturers.lecturer_ID, last_name, first_name, abbr
			FROM lecturers
			JOIN lecturers_institutes ON lecturers.lecturer_ID=lecturers_institutes.lecturer_ID
			JOIN institutes ON lecturers_institutes.institute_ID=institutes.institute_ID
			ORDER BY lecturers.time_stamp DESC LIMIT 1;
		");
	while($lec_row = mysqli_fetch_assoc($lec)){
		$lec_new_selection .= "<option value=".$lec_row['lecturer_ID'].">".$lec_row['last_name'].", ".$lec_row['first_name']." (".$lec_row['abbr'].")</option>";
	}

	echo $lec_new_selection;

} else{
	echo "existsAlready";
}

?>