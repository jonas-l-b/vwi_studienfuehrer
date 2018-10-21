<?php

include "connect.php";

?>


<?php

if (isset($_GET['subjectID'])){
	$subjectID = strval ($_GET['subjectID']);
}

if (isset($_GET['commentID'])){
	$commentID = strval ($_GET['commentID']);
}

if (isset($_GET['userID'])){
	$userID = strval ($_GET['userID']);
}

if (isset($_GET['ratingDirection'])){
	$ratingDirection = strval ($_GET['ratingDirection']);
}
if ($ratingDirection == "up"){
	$ratingDirection = '1';
} else{
	$ratingDirection = '-1';
}

$sql="
	INSERT INTO `commentratings` (`subject_ID`, `comment_ID`, `user_ID`, `rating_direction`, `time_stamp`)
	VALUES ('$subjectID', '$commentID', '$userID', '$ratingDirection', now())
";

if ($con->query($sql) == TRUE) {
	echo 'erfolgreich';
}

//Badge evtl. freischalten (Downvote)
$sql="
	SELECT COUNT(user_ID) AS count FROM commentratings
	WHERE user_ID = ".$userID." AND rating_direction = -1
";
$result=mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

$counts = array(1,15);
$badges = array(69,70);

for ($i = 0; $i <= count($counts)-1; $i++) {
	if($row['count'] >= $counts[$i]){ //Wenn genügend ratings vorhanden
		$result2 = mysqli_query($con, "SELECT * FROM users_badges WHERE user_id = '$userID' AND badge_id = '$badges[$i]'");
		if(mysqli_num_rows($result2) == 0){ //Wenn badge noch nicht vorhanden
			$sql2="INSERT INTO `users_badges`(`user_id`, `badge_id`) VALUES ($userID,'$badges[$i]')";
			if ($con->query($sql2) == TRUE) {
				echo 'achievement';
			}
		}
	}
}

//Badge evtl. freischalten (Upvote)
$sql="
	SELECT COUNT(user_ID) AS count FROM commentratings
	WHERE user_ID = ".$userID." AND rating_direction = 1
";
$result=mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

$counts = array(1,15);
$badges = array(71,72);

for ($i = 0; $i <= count($counts)-1; $i++) {
	if($row['count'] >= $counts[$i]){ //Wenn genügend ratings vorhanden
		$result2 = mysqli_query($con, "SELECT * FROM users_badges WHERE user_id = '$userID' AND badge_id = '$badges[$i]'");
		if(mysqli_num_rows($result2) == 0){ //Wenn badge noch nicht vorhanden
			$sql2="INSERT INTO `users_badges`(`user_id`, `badge_id`) VALUES ($userID,'$badges[$i]')";
			if ($con->query($sql2) == TRUE) {
				echo 'achievement';
			}
		}
	}
}

?>

<?php
$sql="
	SELECT SUM(rating_direction)
	FROM commentratings
	WHERE subject_ID = '$subjectID';
";

$result = mysqli_query($con,$sql);
if (FALSE === $result) die("Select sum failed: ".mysqli_error);
$row = mysqli_fetch_row($result);
$sum = $row[0];

$sql="
	UPDATE ratings
	SET comment_rating = '$sum'
	WHERE ID = '$commentID';
";

if ($con->query($sql) == TRUE) {
	echo 'erfolgreich';
}
?>