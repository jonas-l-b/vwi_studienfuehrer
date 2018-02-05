<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php


$question_id = $_POST['question_id'];
$user_id = $userRow['user_ID'];
$answer = $_POST['formAswer'];

$sql = "
	INSERT INTO answers (question_ID, user_ID, answer, time_stamp)
	VALUES ('$question_id', '$user_id', '$answer', now());
";

if(mysqli_query($con, $sql)){
	echo "erfolg";
}
?>