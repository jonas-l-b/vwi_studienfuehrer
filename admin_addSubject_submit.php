<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$subject_id = $_POST['subject_selection'];
$event_id = $_POST['event_id'];

//Check if already exists
$sql1 = "
	SELECT *
	FROM `sempro_ads`
	WHERE subject_id = '$subject_id' AND event_id = '$event_id'
";
$result1 = mysqli_query($con,$sql1);

//If no, insert
if (mysqli_num_rows($result1)==0){
	$sql2 = "
		INSERT INTO `sempro_ads`(`subject_id`, `event_id`)
		VALUES ($subject_id, $event_id)
	";

	if(!mysqli_query($con,$sql2)){
		echo "Es ist ein Fehler aufgetreten";
	}

} else{
	echo "Auf dieser Veranstaltung wird bereits geworben";
}

?>