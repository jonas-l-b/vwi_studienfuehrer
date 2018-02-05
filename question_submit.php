<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$subject_id = $_POST['subject_id'];
$user_id = $userRow['user_ID'];
$question = $_POST['formQuestion'];

$sql = "
	INSERT INTO questions (subject_ID, user_ID, question, time_stamp)
	VALUES ('$subject_id', '$user_id', '$question', now());
";

if(mysqli_query($con, $sql)){
	echo "erfolg";
}

?>