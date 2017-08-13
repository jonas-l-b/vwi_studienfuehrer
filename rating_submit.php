<?php

include "header.php";

include "sessionsStart.php";

include "connect.php";

include "saveSubjectToVariable.php";

include "loadSubjectData.php";

?>

<?php
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



?>