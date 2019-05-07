<?php
include "sessionsStart.php";
include "connect.php";
include "processInput.php";
?>

<?php
$user_id = $userRow['user_ID'];
$reason = process_input($_POST['reason']);

if(!empty($_POST['area'])){
	$area = process_input($_POST['area']); //IS THIS SAFE??? if(!empty(strip_tags($_POST['area']))) geht nicht
} else{
	$area = "";
}

if(!empty($_POST['subject_id'])){
	$object_id = process_input($_POST['subject_id']);
}elseif(!empty($_POST['module_id'])){
	$object_id = process_input($_POST['module_id']);
}elseif(!empty($_POST['lecturer_id'])){
	$object_id = process_input($_POST['lecturer_id']);
}elseif(!empty($_POST['institute_id'])){
	$object_id = process_input($_POST['institute_id']);
}else{
	$object_id = 0;
}

if(!empty($_POST['answer']) OR $reason == "question"){
	$answer = 1;
}else{
	$answer = 0;
}
	
$comment = process_input($_POST['comment']);

$sql = "
	INSERT INTO messages (sender_id, receiver_id, message_type, area, object_id, answer_required, comment, time_stamp)
	VALUES ('$user_id', -1, '$reason', '$area', '$object_id', '$answer', '$comment', now());
";

if(mysqli_query($con,$sql)){
	echo "erfolg";
	
	//Badge Verbesserer
	$sql="
		SELECT COUNT(sender_id) AS count FROM messages
		WHERE sender_id = '$user_id' AND (message_type = 'mistake' OR message_type = 'bug')
	";
	$result=mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);

	if($row['count'] >= 15){ //Wenn genügend counts vorhanden
		$result2 = mysqli_query($con, "SELECT * FROM users_badges WHERE user_id = '$user_id' AND badge_id = '80'");
		if(mysqli_num_rows($result2) == 0){ //Wenn badge noch nicht vorhanden
			$sql2="INSERT INTO `users_badges`(`user_id`, `badge_id`) VALUES ($user_id,'80')";
			if ($con->query($sql2) == TRUE) {
				echo 'achievement';
			}
		}
	}
	
	//E-Mail-Benachrichtigungen verschicken
	$subject = "[Studienführer: Benachrichtigung] Neue Nachricht für Admins eingegangen";
		
	switch($reason){
		case "bug":
			$type = "Bug";
			break;
		case "mistake":
			$type = "Fehler";
			break;
		case "question":
			$type = "Frage";
			break;
		case "feedback":
			$type = "Feedback";
			break;
	}
	$body = "
		<span>ein Benutzer hat eine Nachricht an die Administratoren des Studienführers geschickt:</span>

		<p>Betreff: <strong>".$type."</strong></p>
		<table style=\"width:100%\">
			<tr>
				<td style=\"border-left: solid 3px #A9A9A9; background: #F5F5F5\">
					<span>".$comment."</span>
				</td>
			</tr>
		</table>
		<br>
			
		<span class='foo'><a href=\"https://xn--studienfhrer-klb.vwi-karlsruhe.de/admin.php#messages\">Hier</a> kannst du die Nachricht online anschauen. Du erhälst diese Nachricht, weil du als Administrator <a href=\"https://xn--studienfhrer-klb.vwi-karlsruhe.de/admin.php#notifications\">hier</a> in die Benachrichtigungs-Liste eingetragen wurdest.</span>
	";

	$sql = "
		SELECT *
		FROM admin_notifications
		JOIN users on users.user_ID = admin_notifications.admin_id
		WHERE type = 'messages'
	";
	$result = mysqli_query($con, $sql);
	while($row = mysqli_fetch_assoc($result)){		
		EmailService::getService()->sendEmail($row['email'], $row['first_name'], $subject, $body);
	}
	
}

?>