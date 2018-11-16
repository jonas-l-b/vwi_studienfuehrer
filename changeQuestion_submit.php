<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$subject_id = $_POST['subject_id'];
$user_id = $userRow['user_ID'];
$question = $_POST['formQuestion'];
$question_id = $_POST['question_id'];

$sql = "
	UPDATE `questions` SET `question`='$question',`time_stamp_last_change`=now()
	WHERE ID = $question_id
";

if(mysqli_query($con, $sql)){
	echo "erfolg";
}

?>