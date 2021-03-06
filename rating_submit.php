<?php
include "sessionsStart.php";
include "connect.php";
include "processInput.php";
?>

<?php
$lecture0 = process_input($_POST['lecture0']);
$lecture1 = process_input($_POST['lecture1']);
$lecture2 = process_input($_POST['lecture2']);

$examType = process_input($_POST['examType']);

$exam0 = process_input($_POST['exam0']);
$exam1 = process_input($_POST['exam1']);
$exam2 = process_input($_POST['exam2']);
$exam3 = process_input($_POST['exam3']);

$examText = process_input($_POST['examText']);

$examSemester = process_input($_POST['examSemester']);

$general0 = process_input($_POST['general0']);
$recommendation = process_input($_POST['recommendation']);
$comment = process_input($_POST['comment']);

$subject = process_input($_POST['subject']);

$nameID = $userRow['user_ID'];

//Check ob Eintrag eingetragen oder geändert werden soll
$result = mysqli_query($con, "SELECT * FROM ratings WHERE subject_ID = '$subject' AND user_ID = '$nameID'");
if(mysqli_num_rows($result) == 1){
	$sql="
		UPDATE ratings
		SET lecture0 = '$lecture0', lecture1 = '$lecture1', lecture2 = '$lecture2', examType = '$examType', exam0 = '$exam0', exam1 = '$exam1', exam2 = '$exam2', exam3 = '$exam3', examText = '$examText', examSemester = '$examSemester', general0 = '$general0', recommendation = '$recommendation', comment = '$comment', time_stamp_change = now()
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
		SELECT COUNT(DISTINCT ratings.subject_ID) AS count FROM ratings
		JOIN subjects_modules ON ratings.subject_ID = subjects_modules.subject_ID
		JOIN modules ON subjects_modules.module_ID = modules.module_ID
        JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
		WHERE ratings.user_ID = ".$nameID." AND modules.type = '$types[$i]' AND NOT modules_levels.level_ID = 1
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

//Generalist
$types = array("BWL","VWL","INFO","OR","ING");
$check = array(false,false,false,false,false);

for ($i = 0; $i <= count($types)-1; $i++) {
	$sql="
		SELECT DISTINCT ratings.subject_ID AS subject_ID FROM ratings
		JOIN subjects_modules ON ratings.subject_ID = subjects_modules.subject_ID
		JOIN modules ON subjects_modules.module_ID = modules.module_ID
        JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
		WHERE ratings.user_ID = ".$nameID." AND modules.type = '$types[$i]' AND NOT modules_levels.level_ID = 1
	";
	$result=mysqli_query($con, $sql);
	if(mysqli_num_rows($result) >= 1){
		$check[$i] = true;
	}
}

if($check[0] AND $check[1] AND $check[2] AND $check[3] AND $check[4]){
	$result2 = mysqli_query($con, "SELECT * FROM users_badges WHERE user_id = '$nameID' AND badge_id = 90");
	if(mysqli_num_rows($result2) == 0){ //Wenn badge noch nicht vorhanden
		$sql2="INSERT INTO `users_badges`(`user_id`, `badge_id`) VALUES ($nameID,90)";
		if ($con->query($sql2) == TRUE) {
			echo 'achievement';
		}
	}
}

//Nachtschärmer
if(date('G') >= 22 OR date('G') <= 5){
	$result2 = mysqli_query($con, "SELECT * FROM users_badges WHERE user_id = '$nameID' AND badge_id = 96");
	if(mysqli_num_rows($result2) == 0){ //Wenn badge noch nicht vorhanden
		$sql2="INSERT INTO `users_badges`(`user_id`, `badge_id`) VALUES ('$nameID',96)";
		if ($con->query($sql2) == TRUE) {
			echo "achievement";
		}
	}
}

?>