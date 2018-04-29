<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php
$question_id = $_POST['question_id'];
$user_id = $userRow['user_ID'];
$answer = $_POST['formAnswer'];

$sql = "
	INSERT INTO answers (question_ID, user_ID, answer, time_stamp)
	VALUES ('$question_id', '$user_id', '$answer', now());
";

if(mysqli_query($con, $sql)){
	//Benachrichtigungsemail
	$sql2 = "SELECT * FROM questions WHERE ID = $question_id";
	$result = mysqli_query($con, $sql2);
	$row = mysqli_fetch_assoc($result);
	
	$sql3 = "
		SELECT *
		FROM users
		JOIN questions ON users.user_ID = questions.user_ID
		WHERE questions.ID = $question_id
	";
	$result2 = mysqli_query($con, $sql3);
	$row2 = mysqli_fetch_assoc($result2);
	
	$sql4 = "
		SELECT *
		FROM subjects
		JOIN questions ON questions.subject_ID = subjects.ID
		WHERE questions.ID = $question_id
	";
	$result3 = mysqli_query($con, $sql4);
	$row3 = mysqli_fetch_assoc($result3);	
	
	$subject = '[Studienführer] Jemand hat auf eine deiner Fragen geantwortet';
	$message="
	<p>Eine der Fragen, die du im Studienführer gestellt hast, wurde beantwortet.</p>
	<p>Du hast diese Frage in der Veranstaltung <strong>".$row3['subject_name']."</strong> gestellt.</p>
	<p><u>Deine Frage</u>:</p>
	<p style=\"margin-left:15px;\">".$row['question']."</p>
	<p><u>Abgegebene Antwort</u>:</p>
	<p style=\"margin-left:15px;\">".$answer."</p>
	<br>
	<p>Du kannst deine gestellten Fragen in deinem Profil anschauen: <a href=\"http://xn--studienfhrer-klb.vwi-karlsruhe.de/userProfile.php#questions\">hier klicken</a>.</p>
	";
	$mailService = EmailService::getService();
	$mailService->sendEmail($row2['email'], $row2['first_name'], $subject, $message);
	
	echo "erfolg";
}
?>