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

//Bewertungen zählen und evtl. Errungenschaft freischalten
$sql="
	SELECT COUNT(user_ID) AS count FROM ratings
	WHERE user_ID = ".$nameID."
";
$result=mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

$counts = array(1,5,10,15,20,25,30,35,40);
$badges = array(60,61,62,63,64,65,66,67,68);

for ($i = 0; $i <= count($counts)-1; $i++) {
	if($row['count'] >= $counts[$i]){ //Wenn genügend ratings vorhanden
		$result2 = mysqli_query($con, "SELECT * FROM users_badges WHERE user_id = '$nameID' AND badge_id = '$badges[$i]'");
		if(mysqli_num_rows($result2) == 0){ //Wenn badge noch nicht vorhanden
			$sql2="INSERT INTO `users_badges`(`user_id`, `badge_id`) VALUES ($nameID,'$badges[$i]')";
			if ($con->query($sql2) == TRUE) {
				echo 'achievement';
			}
		}
	}
}

//Bestimmte Bewertungen zählen und evtl. Errungenschaft freischalten
$types = array("BWL","VWL","INFO","OR","ING");
$badges = array(75,76,77,78,79);

for ($i = 0; $i <= count($types)-1; $i++) {
	
	$sql="
		SELECT COUNT(ratings.user_ID) AS count FROM ratings
		JOIN subjects_modules ON ratings.subject_ID = subjects_modules.subject_ID
		JOIN modules ON subjects_modules.module_ID = modules.module_ID
		WHERE ratings.user_ID = ".$nameID." AND modules.type = '$types[$i]'
	";
	$result=mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);

	if($row['count'] >= 5){ //Wenn genügend ratings vorhanden
		$result2 = mysqli_query($con, "SELECT * FROM users_badges WHERE user_id = '$nameID' AND badge_id = '$badges[$i]'");
		if(mysqli_num_rows($result2) == 0){ //Wenn badge noch nicht vorhanden
			$sql2="INSERT INTO `users_badges`(`user_id`, `badge_id`) VALUES ($nameID,'$badges[$i]')";
			if ($con->query($sql2) == TRUE) {
				echo 'achievement';
			}
		}
	}
}
echo "blablabla";
?>