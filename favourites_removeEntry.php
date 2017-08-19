<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php
$user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_STRING);
$subject_id = filter_var($_POST['subject_id'], FILTER_SANITIZE_STRING);


$sql="
	DELETE FROM favourites
	WHERE user_ID = '$user_id' AND subject_ID = '$subject_id';
";

if(mysqli_query($con, $sql)){
	echo "Erfolg";
}



?>