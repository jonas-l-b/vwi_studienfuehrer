<?php
include "sessionsStart.php";
include "connect.php";
include "processInput.php";
?>

<?php

$subject_id = $_POST['subject_id'];
$user_id = $userRow['user_ID'];
$question = process_input($_POST['formQuestion']);
$question_id = process_input($_POST['question_id']);

$sql = "
	UPDATE `questions` SET `question`='$question',`time_stamp_last_change`=now()
	WHERE ID = $question_id
";

if(mysqli_query($con, $sql)){
	echo "erfolg";
}

?>