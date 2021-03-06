<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div id="main" class="container">
	<h2>Erfahrungen & Kontakt</h2>
	<p>Auf dieser Seite werden alle geteilten Erfahrungen eines Nutzers gelistet. Außerdem besteht die Möglichkeit zur Kontaktaufnahme.</p>

	<?php
	//Check if current user activated messages
	$sql = "
		SELECT * FROM user_notifications
		WHERE user_id = ".$userRow['user_ID']."
	";
	$result = mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);
	
	if ($row["user_messages"] != 1){
		echo "Um diese Funktion nutzen zu können, musst du selbst zustimmen, dass Nutzer dir Nachrichten schicken dürfen. Tu das in deinem <a href='userProfile.php#notifications'>Profil</a> und komme hierher zurück.";
	}else{
		if (isset($_GET['recipient_id']) && !empty($_GET['recipient_id'])){
			$recipient_id = strval($_GET['recipient_id']);
			$sql = "
				SELECT * FROM `users`
				JOIN user_notifications ON users.user_ID = user_notifications.user_id
				WHERE users.user_ID = $recipient_id
			";
			$result = mysqli_query($con, $sql);
			$row = mysqli_fetch_assoc($result);
			
			if ($row["username"] == ""){
				?>
				<p>Allerdings passt die Nutzer-ID, die in der URL übergeben wurde, zu keinem Nutzer in der Datenbank.
				Klicke unter Kommentaren auf den Nutzernamen des Verfassers, um mit einer passenden Nutzer-ID hierherzugelangen.</p>				
				<?php
			}else{
				$sql2 = "SELECT * FROM user_notifications WHERE user_id = $recipient_id";
				$result2 = mysqli_query($con, $sql2);
				$row2 = mysqli_fetch_assoc($result2);
				
				//Alle Erfahrungen
				?>
				<h3>Zu diesen Veranstaltungen hat <b><i><?php echo $row["username"] ?></i></b> Erfahrungen geteilt:</h3>
				<?php
				$sql3 = "
					SELECT ratings.subject_ID AS subject_id, subjects.subject_name AS subject_name FROM `ratings`
					JOIN subjects on ratings.subject_ID = subjects.ID
					WHERE user_ID = $recipient_id
				";
				$result3 = mysqli_query($con, $sql3);
				
				if (mysqli_num_rows($result3) == 0 ) {
					?>
					<i><?php echo $row["username"] ?> hat noch keine Erfahrungen geteilt.</i>
					<?php
				}
				?>
				<ul style="padding-left: 1em">
				<?php
				while($row3 = mysqli_fetch_assoc($result3)){
					?>
					<li><a href="index.php?subject=<?php echo $row3['subject_id']?>"><?php echo $row3['subject_name']?></a></li>
					<?php
				}?>
				</ul>
				<?php
				//Nachricht an Nutzer
				?>
				<h3>Nachricht an <b><i><?php echo $row["username"] ?></i></b> schicken:</h3>
				<p>Falls du weitere Fragen an einen Nutzer des Studienführers hast, kannst du hier diesem Nutzer eine Nachricht schicken, sofern:</p>
				<ul style="padding-left: 1em">
					<li>dieser Nutzer dem Empfang von Nachrichten nicht widersprochen hat</li>
					<li>du dem Empfang von Nachrichten nicht widersprochen hast (das kannst du in deinem <a href="userProfile.php#notifications">Profil</a> tun)</li>
				</ul>
				<p>Der Empfänger deiner Nachricht bekommt diese von uns zusammen mit deiner E-Mail-Adresse zugeschickt, sodass er dir direkt antworten kann. Zur weiteren Kommunikation kannst du natürlich direkt auf seine E-Mail antworten; diese Seite dient also nur zur Kontaktaufnahme.</p>

				<?php				
				if($row2['user_messages'] == 1){
					if($recipient_id == $userRow['user_ID']) echo "<p style='color:rgb(0, 204, 0)'><i>Du kannst dir natürlich auch selbst eine Nachricht schreiben, aber dann musst du dir natürlich auch selbst antworten :)</i></p>";
					?>
					<div id="messageBody">
						<form id="submit_message">
							<div class="form-group">
								<label for="message">Nachricht:</label>
								<textarea class="form-control" rows="5" id="message" name="message"></textarea>
							</div>
							<button id="submitMessageButton" class="btn btn-primary">Jetzt verschicken</button>
						</form>
					</div>
					<?php
				}else{
					?>
					<p>
						Der Nutzer <b><i><?php echo $row["username"] ?></i></b> möchte leider keine Nachrichten empfangen.	
					</p>
					<?php
				}
			}

		}else{
			?>
			<p>Allerdings wurde keine Nutzer-ID in der URL übergeben.
			Klicke unter Kommentaren auf den Nutzernamen des Verfassers, um mit einer passenden Nutzer-ID hierherzugelangen.</p>
			<?php
		}
	}
	?>
	
</div>

<script>
$( document ).ready(function() {

	$('#submitMessageButton').click(function(e){
	e.preventDefault(); // avoid to execute the actual submit of the form.
		
		if($('#message').val().length <= 5){
			alert("Deine Nachricht ist sehr kurz. Schreibe etwas mehr!");
			return;
		}

		$.ajax({
			type: "POST",
			url: "sendMessage_submit.php",
			data: $("#submit_message").serialize()+ "&recipient=" + <?php echo $recipient_id?>,
			success: function(data) {
				//alert(data);
				if(data.includes("erfolg")){
					$('#messageBody').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Deine Nachricht wurde erfolgreich verschickt!</div>");
				}else{
					$('#messageBody').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Beim Versenden deiner Nachricht ist ein Fehler aufgetreten! Bitte probiere es erneut (oftmals liegt es am Server, sodass es beim zweiten Mal klappt); falls es immernoch nicht funktioniert, schreib uns: studienführer@vwi-karlsruhe.de.</div>");
				}
			}
		});
	});

});
</script>

</body>