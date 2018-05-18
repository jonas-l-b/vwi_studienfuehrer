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
	/*Benachrichtigungsemail*/
	//Check, ob Fragensteller Benachrichtigung haben will
	$sql6="SELECT user_ID FROM questions WHERE ID = ".$question_id."";
	$result6=mysqli_query($con, $sql6);
	$row6 = mysqli_fetch_assoc($result6);
	$authorOfQuestionId = $row6['user_ID'];
	
	$sql5="SELECT * FROM user_notifications WHERE user_ID = ".$authorOfQuestionId."";
	$result5=mysqli_query($con,$sql5);

	$row5=mysqli_fetch_assoc($result5);
	if($row5['own_questions']==1 || mysqli_num_rows($result5)==0){ //Send eMail
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
		
		$subject = '[Studienführer] Jemand hat auf eine deiner Fragen geantwortet!';
		$message="
		<p>eine der Fragen, die du im Studienführer gestellt hast, wurde beantwortet.<br>
		Du hast diese Frage in der Veranstaltung <strong>".$row3['subject_name']."</strong> gestellt.</p>
		<p>Deine Frage:</p>
		<table style=\"width:100%\">
			<tr>
				<td style=\"border-left: solid 3px #A9A9A9; background: #F5F5F5\">
					<span style=\"font-size:1.2em\">".$row['question']."</span>
				</td>
			</tr>
		</table>
		<br>
		<p>Abgegebene Antwort:</p>
		<table style=\"width:100%\">
			<tr>
				<td style=\"border-left: solid 3px #A9A9A9; background: #F5F5F5\">
					<span style=\"font-size:1.2em\">".$answer."</span>
				</td>
			</tr>
		</table>
		<br>
		<br>
		<p>Du kannst alle deine gestellten Fragen in deinem Profil anschauen: <a href=\"http://xn--studienfhrer-klb.vwi-karlsruhe.de/userProfile.php#questions\">hier klicken</a>.</p>
		";
		$mailService = EmailService::getService();
		$mailService->sendEmail($row2['email'], $row2['first_name'], $subject, $message);
	}
	
	echo "erfolg";
}
?>