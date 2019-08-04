<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$subject_id = $_POST['subject_id'];
$event_id = $_POST['event_id'];

$sql = "
	DELETE FROM `sempro_ads`
	WHERE subject_id = $subject_id and event_id = $event_id
";

if(mysqli_query($con,$sql)){
	echo "Erfolgreich gelöscht";
}else{
	echo "Es ist ein Fehler aufgetreten";
}
?>