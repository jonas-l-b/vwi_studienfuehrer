<?php

//include "header.php";

include "sessionsStart.php";

include "connect.php";

/*
include "saveSubjectToVariable.php";

include "loadSubjectData.php";
*/
?>

<?php
$lecture0 = filter_var($_POST['lecture0'], FILTER_SANITIZE_STRING);
$lecture1 = filter_var($_POST['lecture1'], FILTER_SANITIZE_STRING);
$lecture2 = filter_var($_POST['lecture2'], FILTER_SANITIZE_STRING);

$examType = filter_var($_POST['examType'], FILTER_SANITIZE_STRING);

$exam0 = filter_var($_POST['exam0'], FILTER_SANITIZE_STRING);
$exam1 = filter_var($_POST['exam1'], FILTER_SANITIZE_STRING);
$exam2 = filter_var($_POST['exam2'], FILTER_SANITIZE_STRING);
$exam3 = filter_var($_POST['exam3'], FILTER_SANITIZE_STRING);

$examText = filter_var($_POST['examText'], FILTER_SANITIZE_STRING);

$examSemester = filter_var($_POST['examSemester'], FILTER_SANITIZE_STRING);

$general0 = filter_var($_POST['general0'], FILTER_SANITIZE_STRING);
$recommendation = filter_var($_POST['recommendation'], FILTER_SANITIZE_STRING);
$comment = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);

$subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);

$nameID = $userRow['user_ID'];

//Check ob Eintrag eingetragen oder geändert werden soll
$result = mysqli_query($con, "SELECT * FROM ratings WHERE subject_ID = '$subject' AND user_ID = '$nameID'");
if(mysqli_num_rows($result) == 1){
	$sql="
		UPDATE ratings
		SET lecture0 = '$lecture0', lecture1 = '$lecture1', lecture2 = '$lecture2', examType = '$examType', exam0 = '$exam0', exam1 = '$exam1', exam3 = '$exam3', examText = '$examText', examSemester = '$examSemester', general0 = '$general0', recommendation = '$recommendation', comment = '$comment', time_stamp_change = now()
		WHERE subject_ID = '$subject' AND user_ID = '$nameID';
	";
	if ($con->query($sql) == TRUE) {
		echo 'change';
	}
}elseif(mysqli_num_rows($result) == 0){
	$sql="
		INSERT INTO `ratings` (`subject_ID`, `lecture0`, `lecture1`, `lecture2`, `examType`, `exam0`, `exam1`, `exam2`, `exam3`, `examText`, `examSemester`, `general0`, `recommendation`, `comment`, `comment_rating`, `user_ID`, `time_stamp`)
		VALUES ('$subject', '$lecture0', '$lecture1', '$lecture2', '$examType', '$exam0', '$exam1', '$exam2', '$exam3', '$examText', '$examSemester', '$general0', '$recommendation', '$comment', 0, '$nameID', now())
	";
	if ($con->query($sql) == TRUE) {
		echo 'erfolg';
	}
}else{
	echo "errorM";
	exit;
}
?>