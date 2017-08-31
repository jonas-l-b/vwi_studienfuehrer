<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$processedSuccess = $_POST['processedSuccess'];
if(!empty($_POST['finishComment'])){
	$finishComment = $_POST['finishComment'];
}
$finishCommentAdmin = $_POST['finishCommentAdmin'];
$message_id = substr($_POST['message_id'],11);

if(isset($finishComment)){
	$sql = "
		UPDATE messages
		SET processed = $processedSuccess, processed_by_id = ".$userRow['user_ID'].", processed_comment = '$finishComment', processed_comment_for_admins = '$finishCommentAdmin', processed_time_stamp = now()
		WHERE message_id = '".$message_id."'
	";
} else{
	$sql = "
		UPDATE messages
		SET processed = $processedSuccess, processed_by_id = ".$userRow['user_ID'].", processed_comment_for_admins = '$finishCommentAdmin', processed_time_stamp = now()
		WHERE message_id = '".$message_id."'
	";
}

if(mysqli_query($con, $sql)){
	echo "Erfolgreich markiert!";
	
	//Antwort per E-Mail an Benutzer schicken, wenn dieser Antwort wünscht
	$row = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM messages JOIN users ON messages.sender_id = users.user_ID WHERE message_id = '".$message_id."'"));
	
	if($row['answer_required'] == 1){
		$subject = "[Studienführer: Benachrichtigung] Deine Nachricht wurde bearbeitet";
		
		switch($processedSuccess){
			case 1:
				$erfolg = "Erfolgreich";
				break;
			case 2:
				$erfolg = "Nicht Erfolgreich";
				break;
		}
		
		if(isset($finishComment)){
			$passage = " und folgende Nachricht für dich hinterlassen:";
			$mes = "<p>".$finishComment."</p>";
		}else{
			$passage = ".";
			$mes = "";
		}

		$body = "
			<p>Ein Administrator hat deine Nachricht bearbeitet:</p>
			<hr>
			<p><strong>Deine Nachricht an uns</strong>:</p>
			<p><i>Gesendet am: row['time_stamp']</i></p>
			<p>".$row['comment']."</p>
			<hr>
			<p>Der Administrator hat die Bearbeitung deiner Nachricht als <i>".$erfolg."</i> markiert".$passage."</p>
			".$mes."
			<hr>
			<p>Falls du noch weitere Fragen oder Anmerkungen hast, kannst du dich gerne wieder an uns wenden. Benutze dazu bitte erneut das Kontaktformular auf der Webseite und antworte nicht auf diese Mail (da diese nicht ankommen würde).</p>
		";
		
		EmailService::getService()->sendEmail($row['email'], $row['username'], $subject, $body);
	}
}

?>