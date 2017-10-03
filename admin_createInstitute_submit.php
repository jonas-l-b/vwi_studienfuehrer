<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$inst_name = $_POST['inst_name'];
$inst_abbr = strtoupper($_POST['inst_abbr']);
$userID = $userRow['user_ID'];

//Check if already exists
$sql1 = "
	SELECT *
	FROM institutes
	WHERE name = '$inst_name' AND abbr = '$inst_abbr';
";
$result1 = mysqli_query($con,$sql1);

//If no, insert
if (mysqli_num_rows($result1)==0){
	$sql2 = "
		INSERT INTO institutes (name, abbr, user_ID, time_stamp)
		VALUES ('$inst_name', '$inst_abbr', '$userID', now());
	";

	mysqli_query($con,$sql2);
	$db_logger->info("Neues Institut hinzugefügt: $inst_name $inst_abbr von User: $userID" );

	$insti_new_selection = "";

	$insti = mysqli_query($con,"SELECT * FROM institutes ORDER BY time_stamp DESC LIMIT 1;");
	while($insti_row = mysqli_fetch_assoc($insti)){
		$insti_new_selection .= "<option value=".$insti_row['institute_ID'].">".$insti_row['name']." (".$insti_row['abbr'].")</option>";
	}

	echo $insti_new_selection;
} else{
	echo "existsAlready";
}



?>