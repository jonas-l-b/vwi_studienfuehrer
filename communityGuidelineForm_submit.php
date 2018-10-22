<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$q1 = $_POST['q1'];
$q2 = $_POST['q2'];
$q3 = $_POST['q3'];

if($q1 == 1 AND $q2 == 2 AND $q3 == 2){
	echo "correct";
	
	$result2 = mysqli_query($con, "SELECT * FROM users_badges WHERE user_id = ".$userRow['user_ID']." AND badge_id = 87");
	if(mysqli_num_rows($result2) == 0){ //Wenn badge noch nicht vorhanden
		$sql2="INSERT INTO `users_badges`(`user_id`, `badge_id`) VALUES (".$userRow['user_ID'].",87)";
		if ($con->query($sql2) == TRUE) {
			echo 'achievement';
		}
	}
}else{
	echo "false";
}

?>