<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$facebook = $_POST['facebook_link'];
$studydrive = $_POST['studydrive_link'];
//$modulebook = $_POST['modulhandbuch_link'];

$user_id = $_POST['user_id'];
$subject_id = $_POST['subject_id'];

$result=mysqli_query($con, "SELECT facebook, studydrive, modulebook FROM subjects WHERE ID = ".$subject_id."");
$row = mysqli_fetch_assoc($result);

if($row['facebook'] != $facebook){ //Wenn Datenbankeintrag sich von Formulardaten unterscheidet
	$sql="
		UPDATE `subjects` SET `facebook`='$facebook',`facebook_user_id`='$user_id'
		WHERE ID = $subject_id
	";
	if ($con->query($sql) == TRUE) {
		echo 'erfolg';
		
		$sql2="
			SELECT * FROM users_links
			WHERE user_id = $user_id AND subject_id = $subject_id AND type = 'facebook'
		";
		$result2 = mysqli_query($con, $sql2);
		if(mysqli_num_rows($result2) == 0 ){
			mysqli_query($con, "INSERT INTO `users_links`(`user_id`, `subject_id`, `type`) VALUES ($user_id,$subject_id,'facebook')");
		}
	}
}

if($row['studydrive'] != $studydrive){ //Wenn Datenbankeintrag sich von Formulardaten unterscheidet
	$sql="
		UPDATE `subjects` SET `studydrive`='$studydrive',`studydrive_user_id`='$user_id'
		WHERE ID = $subject_id
	";
	if ($con->query($sql) == TRUE) {
		echo 'erfolg';
		
		$sql2="
			SELECT * FROM users_links
			WHERE user_id = $user_id AND subject_id = $subject_id AND type = 'studydrive'
		";
		$result2 = mysqli_query($con, $sql2);
		if(mysqli_num_rows($result2) == 0 ){
			mysqli_query($con, "INSERT INTO `users_links`(`user_id`, `subject_id`, `type`) VALUES ($user_id,$subject_id,'studydrive')");
		}
		
	}
}


//Evtl. Errungenschaft freischalten
$sql="
	SELECT COUNT(user_ID) AS count FROM users_links
	WHERE user_ID = ".$user_id."
";
$result=mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

$counts = array(1,15);
$badges = array(91,92);

for ($i = 0; $i <= count($counts)-1; $i++) {
	if($row['count'] >= $counts[$i]){ //Wenn genügend ratings vorhanden
		$result2 = mysqli_query($con, "SELECT * FROM users_badges WHERE user_id = ".$user_id." AND badge_id = '$badges[$i]'");
		if(mysqli_num_rows($result2) == 0){ //Wenn badge noch nicht vorhanden
			$sql2="INSERT INTO `users_badges`(`user_id`, `badge_id`) VALUES (".$user_id.",'$badges[$i]')";
			if ($con->query($sql2) == TRUE) {
				echo 'achievement';
			}
		}
	}
}

?>