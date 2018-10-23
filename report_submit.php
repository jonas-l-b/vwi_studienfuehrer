<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$commentId = strip_tags($_POST['commentId']);
$comment = strip_tags($_POST['comment']);
if(!empty($_POST['answer'])){
	$answer = 1;
}else{
	$answer = 0;
}
$user_id = $userRow['user_ID'];

$sql = "
	INSERT INTO messages (sender_id, receiver_id, message_type, comment_id, answer_required, comment, time_stamp)
	VALUES ('$user_id', -1, 'comment', '$commentId', '$answer', '$comment', now());
";

if(mysqli_query($con,$sql)){
	echo "erfolg";
	
	//Nachtwächter-Badge
	if(date('G') >= 23 OR date('G') <= 11){
		$result2 = mysqli_query($con, "SELECT * FROM users_badges WHERE user_id = ".$user_id." AND badge_id = 73");
		if(mysqli_num_rows($result2) == 0){ //Wenn badge noch nicht vorhanden
			$sql2="INSERT INTO `users_badges`(`user_id`, `badge_id`) VALUES (".$user_id.",73)";
			if ($con->query($sql2) == TRUE) {
				echo "achievement";
			}
		}
	}
	
	//E-Mail-Benachrichtigungen verschicken
	$subject = "[Studienführer: Benachrichtigung] Neue Nachricht für Admins eingegangen";
	$body = "nosig
		<span>ein Benutzer hat eine Nachricht an die Administratoren geschickt:</span>
		<hr>
		<p>Betreff: <strong>Kommentar</strong></p>
		<span>".$comment."</span>
		<hr>
		<span class='foo'><a href=\"app.vwi-karlsruhe.de/studienfuehrer/admin.php#messages\">Hier</a> kannst du die Nachricht online anschauen. Du erhälst diese Nachricht, weil du als Administrator <a href=\"app.vwi-karlsruhe.de/studienfuehrer/admin.php#notifications\">hier</a> in die Benachrichtigungs-Liste eingetragen wurdest.</span>
	";
	
	$sql = "
		SELECT *
		FROM admin_notifications
		JOIN users on users.user_ID = admin_notifications.admin_id
		WHERE type = 'messages'
	";
	$result = mysqli_query($con, $sql);	
	
	while($row = mysqli_fetch_assoc($result)){
		EmailService::getService()->sendEmail($row['email'], $row['username'], $subject, $body);
	}
	
}



?>