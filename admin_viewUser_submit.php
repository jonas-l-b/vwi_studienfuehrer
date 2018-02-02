<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php
$userID = $_POST['userID'];

if($userID == ""){
	$answer = "pleaseChoose";
}else{
	$userRow = mysqli_fetch_assoc(mysqli_query($con, "SELECT * from users WHERE user_ID = $userID"));
	
	$status = "";
	if($userRow['admin'] == 1){
		$status = "(Admin)";
	}
	if($userRow['super_admin'] == 1){
		$status = "(Super-Admin)";
	}
	
	if($userRow['active'] == 0){
		$active = "Nein <span style=\"float:right;\"><button class=\"btn btn-primary\" id=\"reSend\">Aktivierungsmail erneut senden</button></span>";
	}else{
		$active = "Ja";
	}

	$answer = "
		<br>
		<h3>".$userRow['last_name'].", ".$userRow['first_name']." ".$status."</h3>
		<table class=\"table\" style=\"border-top:solid; border-top-color:white\">
			<tbody>
				<tr>
					<th>Benutzername:</th>
					<td>".$userRow['username']."</td>
				</tr>
				<tr>
					<th>E-Mail:</th>
					<td><a href=\"mailto:".$userRow['email']."\">".$userRow['email']."</a></td>
				</tr>
				<tr>
					<th>Studiengang:</th>
					<td>".$userRow['degree']."</td>
				</tr>
				<tr>
					<th>Fortschritt:</th>
					<td>".ucfirst($userRow['advance'])."</td>
				</tr>
				<tr>
					<th>Semester:</th>
					<td>".$userRow['semester']."</td>
				</tr>
				<tr>
					<th>Profil aktiv?</th>
					<td>".$active."</td>
				</tr>
			</tbody>
		</table>
	";
	
	/*
	$answer = "
		<br>
		<table class=\"table\" style=\"border-top:solid; border-top-color:white\">
			<tbody>
				<tr>
					<th>Vorname:</th>
					<td>".$userRow['first_name']."</td>
				</tr>				
				<tr>
					<th>Nachname:</th>
					<td>".$userRow['last_name']."</td>
				</tr>				
				<tr>
					<th>Benutzername:</th>
					<td>".$userRow['username']."</td>
				</tr>
				<tr>
					<th>E-Mail:</th>
					<td><a href=\"mailto:".$userRow['email']."\">".$userRow['email']."</a></td>
				</tr>
				<tr>
					<th>Studiengang:</th>
					<td>".$userRow['degree']."</td>
				</tr>
				<tr>
					<th>Fortschritt:</th>
					<td>".ucfirst($userRow['advance'])."</td>
				</tr>
				<tr>
					<th>Semester:</th>
					<td>".$userRow['semester']."</td>
				</tr>
			</tbody>
		</table>
	";
	*/
	
}
echo $answer;
?>