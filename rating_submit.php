<?php
/*
include "header.php";
*/
include "sessionsStart.php";

include "connect.php";

include "saveSubjectToVariable.php";

include "loadSubjectData.php";

?>

<?php
$lecture0 = filter_var($_POST['lecture0'], FILTER_SANITIZE_STRING);
$lecture1 = filter_var($_POST['lecture1'], FILTER_SANITIZE_STRING);
$lecture2 = filter_var($_POST['lecture2'], FILTER_SANITIZE_STRING);
$lecture3 = filter_var($_POST['lecture3'], FILTER_SANITIZE_STRING);

$examType = filter_var($_POST['examType'], FILTER_SANITIZE_STRING);

$exam0 = filter_var($_POST['exam0'], FILTER_SANITIZE_STRING);
$exam1 = filter_var($_POST['exam1'], FILTER_SANITIZE_STRING);
$exam2 = filter_var($_POST['exam2'], FILTER_SANITIZE_STRING);
$exam3 = filter_var($_POST['exam3'], FILTER_SANITIZE_STRING);
$exam4 = filter_var($_POST['exam4'], FILTER_SANITIZE_STRING);
$exam5 = filter_var($_POST['exam5'], FILTER_SANITIZE_STRING);

$examText = filter_var($_POST['examText'], FILTER_SANITIZE_STRING);
$general0 = filter_var($_POST['general0'], FILTER_SANITIZE_STRING);
$recommendation = filter_var($_POST['recommendation'], FILTER_SANITIZE_STRING);
$comment = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);

$sql="
	INSERT INTO `ratings` (`subject_ID`, `lecture0`, `lecture1`, `lecture2`, `lecture3`, `exam0`, `exam1`, `exam2`, `exam3`, `exam4`, `exam5`, `recommendation`, `comment`, `comment_rating`, `user_ID`, `time_stamp`)
	VALUES ('$subjectData[ID]', '$criterion1', '$criterion2', '$criterion3', '$criterion4', '$criterion5', '$recommendation', '$comment', 0, '$nameID', now())";

if ($con->query($sql) == TRUE) {
	//echo 'erfolgreich';
}

/*
$criterion1 = filter_var($_POST['criterion1'], FILTER_SANITIZE_STRING);
$criterion2 = filter_var($_POST['criterion2'], FILTER_SANITIZE_STRING);
$criterion3 = filter_var($_POST['criterion3'], FILTER_SANITIZE_STRING);
$criterion4 = filter_var($_POST['criterion4'], FILTER_SANITIZE_STRING);
$criterion5 = filter_var($_POST['criterion5'], FILTER_SANITIZE_STRING);
$recommendation = filter_var($_POST['recommendation'], FILTER_SANITIZE_STRING);
$comment = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);
$nameID = $userRow['user_ID'];

$sql="
	INSERT INTO `ratings` (`subject_ID`, `crit1`, `crit2`, `crit3`, `crit4`, `crit5`, `recommendation`, `comment`, `comment_rating`, `user_ID`, `time_stamp`)
	VALUES ('$subjectData[ID]', '$criterion1', '$criterion2', '$criterion3', '$criterion4', '$criterion5', '$recommendation', '$comment', 0, '$nameID', now())";

if ($con->query($sql) == TRUE) {
	//echo 'erfolgreich';
}
	
echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='index.php?subject=$subject';</SCRIPT>");
*/


?>