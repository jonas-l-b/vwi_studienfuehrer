<?php
include "sessionsStart.php";
include "connect.php";
include "processInput.php";
?>

<?php

$user_id = $userRow['user_ID'];
$recipient = process_input($_POST['recipient']);
$message = process_input($_POST['message']);

$sql = "
	INSERT INTO `user_messages`(`from_id`, `to_id`, `message`, `time_stamp`)
	VALUES ($user_id,$recipient,'$message',now())
";

if(mysqli_query($con, $sql)){
	
	$result = mysqli_query($con, "SELECT * FROM users WHERE user_ID = $recipient");
	$row = mysqli_fetch_assoc($result);
	
	$result2 = mysqli_query($con, "SELECT * FROM users WHERE user_ID = $user_id");
	$row2 = mysqli_fetch_assoc($result2);
	
	$subject = "[Studienführer] Der Nutzer ".$row2['username']." hat dir eine Nachricht geschickt";
	
	$body = "
		<p>der Nutzer <b><i>".$row2['username']."</i></b> hat dir eine Nachricht geschickt:</p>
		<table style=\"width:100%\">
			<tr>
				<td style=\"border-left: solid 3px #A9A9A9; background: #F5F5F5\">
					<span>".$message."</span>
				</td>
			</tr>
		</table>
		<p>Wenn du ihm antworten willst, kannst du das über diese E-Mail-Adresse tun: <a href='mailto:".$row2['email']."'>".$row2['email']."</a></p>
		<p>Falls du keine solche Nachrichten mehr erhalten willst, kannst du diese in deinem Profil abbestellen.</p>
	
	";
	
	EmailService::getService()->sendEmail($row['email'], $row['username'], $subject, $body);
	
	echo "erfolg";
}

echo $sql;

?>