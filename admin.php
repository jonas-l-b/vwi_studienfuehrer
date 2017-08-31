<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>

<?php
if($userRow['admin']==0){
	echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=no_admin';</SCRIPT>");
}
?>

<html>
<body>

<?php include "inc/nav.php" ?>

<div class="container" style="margin-top:60px">

	<h2>Ja servus, lieber Administrator des Studienführers!</h2>
	<br>
		
<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" href="#modifyData">Daten bearbeiten</a></li>
		<li><a data-toggle="tab" href="#messages">Nachrichten</a></li>
		<li><a data-toggle="tab" href="#notifications">Benachrichigungen</a></li>
	<!--<li><a data-toggle="tab" href="#menu3">Menu 3</a></li>-->
	</ul>

	<div class="tab-content">
		<div id="modifyData" class="tab-pane fade in active">
			<br>
			
			<p><i>Hier kann die Datenbank maßgeblich verändert werden. Vorsicht dabei, Backups und Validierungen gibt's quasi (noch) nicht :)</i></p>
			
			<h3 class="adminHeader">Eintragen</h3>
			<p>Über diesen Button können <strong>Veranstaltungen</strong> und in diesem Zuge auch <strong>Dozenten</strong>, <strong>Institute</strong> und <strong>Module</strong> eingetragen werden.</p>
			<a href="admin_createSubject.php" class="btn btn-primary" role="button">Eintragen</a>
			
			<br><br>
			
			<h3 class="adminHeader">Löschen</h3>
			<p>Objekt gibt's nicht mehr? Dann hier über den entsprechenden Button löschen.</p>
			<a href="admin_deleteSubject.php" class="btn btn-primary" role="button">Veranstaltungen löschen</a>
			<a href="admin_deleteLecturerInstituteModule.php" class="btn btn-primary" role="button">Dozenten/Institute/Module löschen</a>

			<br><br>
			
			<h3 class="adminHeader">Bearbeiten</h3>
			<p>Es hat sich was geändert oder beim Eintragen gab's Tippfehler? Das kann hier behoben werden.</p>
			<a href="admin_editSubject.php" class="btn btn-primary" role="button">Veranstaltung bearbeiten</a>
			<a href="admin_editLecturer.php" class="btn btn-primary" role="button">Dozent bearbeiten</a>
			<a href="admin_editInstitute.php" class="btn btn-primary" role="button">Institut bearbeiten</a>
			<a href="admin_editModule.php" class="btn btn-primary" role="button">Modul bearbeiten</a>
		</div>
		
		<div id="messages" class="tab-pane fade">
			<br>
			<div id="inbox">  <!-- inbox START -->
				<div class="tab">
					<?php //Zählt Werte für Badges
					$types = array("bug", "mistake", "question", "feedback", "comment");
					for($i = 0; $i < count($types); $i++) {
						$badge[$types[$i]] = mysqli_num_rows(mysqli_query($con, "SELECT * FROM messages WHERE message_type = '".$types[$i]."' AND processed = 0"));
					}
					?>
				
					<button class="tablinks active" onclick="changeInbox(event, 'bug')">Bugs <span class="badge <?php if($badge['bug']!=0) echo "highlightedBadge"?>"><?php echo $badge['bug']?></span></button>
					<button class="tablinks" onclick="changeInbox(event, 'mistake')">Fehler <span class="badge <?php if($badge['mistake']!=0) echo "highlightedBadge"?>"><?php echo $badge['mistake']?></span></button>
					<button class="tablinks" onclick="changeInbox(event, 'question')">Fragen <span class="badge <?php if($badge['question']!=0) echo "highlightedBadge"?>"><?php echo $badge['question']?></span></button>
					<button class="tablinks" onclick="changeInbox(event, 'feedback')">Feedback <span class="badge <?php if($badge['feedback']!=0) echo "highlightedBadge"?>"><?php echo $badge['feedback']?></span></button>
					<button class="tablinks" onclick="changeInbox(event, 'comment')">Kommentare <span class="badge <?php if($badge['comment']!=0) echo "highlightedBadge"?>"><?php echo $badge['comment']?></span></button>
				</div>

				<?php
				for($i = 0; $i < count($types); $i++) {
				?>
					<div id="<?php echo $types[$i] ?>" class="tabcontent" style="display:none">
						<div class="openMessages"> <!-- open START -->
						<p style="font-size:20px"><span class="open1" style="font-weight:bold">Offen</span> | <span class="closed1" style="color:lightgrey; cursor: pointer; cursor: hand;" >Bearbeitet</span></p>
							<?php
							//Offen
							$result = mysqli_query($con, "SELECT * FROM messages WHERE message_type = '$types[$i]' AND processed = 0");
							while($row = mysqli_fetch_assoc($result)){
								
								//Glyphicon 1
								if($row['read_last_id']=="0"){
									$glyphicon1 = "glyphicon-envelope";
								} else{
									$glyphicon1 = "glyphicon-list-alt";
								}
								
								//Zuletzt gelesen
								if($row['read_last_id']=="0"){
									$lastRead = "";
								} else{
									$result2 = mysqli_query($con, "SELECT username FROM users WHERE user_ID = '".$row['read_last_id']."'");
									$user = mysqli_fetch_assoc($result2);
									$lastRead = "<br>Zuletzt gelesen: <strong>".$user['username']."</strong>";
								}

								//Glyphicon 
								if($row['read_last_id']=="0"){
									$glyphicon2Line = "";
								} else{					
									if($row['assigned_to_id']=="0"){
										$glyphicon2Line = "<span class=\"symbol glyphicon glyphicon glyphicon-question-sign\"></span>";
									} else{
										$glyphicon2Line = "<span class=\"symbol glyphicon glyphicon glyphicon-user\"></span>";
									}
								}
								
								//Zugewiesen
								if($row['read_last_id']=="0"){
									$assignedToLine = "";
								} else{
									if($row['assigned_to_id']=="0"){
										$assignedTo = "<i>nicht zugewiesen</i>";
									} else{
										$result3 = mysqli_query($con, "SELECT username FROM users WHERE user_ID = '".$row['assigned_to_id']."'");
										$user2 = mysqli_fetch_assoc($result3);
										$assignedTo = $user2['username'];
									}
									$assignedToLine = "<span class=\"text\">Wird bearbeitet von:<br><strong><span class=\"changeAssignedTo\">".$assignedTo."</span></strong></span>";
								}

								?>
								<div class="message" id="<?php echo ("message_id_".$row['message_id']) ?>">
									<span class="symbol glyphicon <?php echo $glyphicon1 ?>"></span>
									<span class="text">Empfangen: <?php echo $row['time_stamp'] ?><span class="lastRead"><?php echo $lastRead ?></span></span>
									<span class="assignedToGlyphicon"> <?php echo $glyphicon2Line ?> </span>
									<span class="assignedTo"> <?php echo $assignedToLine ?> </span>
								</div>
								<?php
							}
							?>
						</div> <!-- open END -->
						
						<div class="closedMessages" style="display:none"> <!-- closed START -->
							<p style="font-size:20px"><span class="open2" style="color:lightgrey; cursor: pointer; cursor: hand;">Offen</span> | <span class="closed2" style="font-weight:bold" >Bearbeitet</span></p>
								
							<?php
							$result = mysqli_query($con, "SELECT * FROM messages WHERE message_type = '$types[$i]' AND (processed = 1 OR processed = 2)");
							while($row = mysqli_fetch_assoc($result)){

								//Glyphicon 1
								if($row['processed']=="1"){
									$glyphicon1 = "glyphicon-ok-circle";
									$solved = "gelöst";
								} elseif($row['processed']=="2"){
									$glyphicon1 = "glyphicon-remove-circle";
									$solved = "ungelöst";
								}
								
								//Gelöst von
								$result2 = mysqli_query($con, "SELECT username FROM users WHERE user_ID = '".$row['processed_by_id']."'");
								$processed_by = mysqli_fetch_assoc($result2);
								
								//Versandt
								if($row['answer_required']=="1"){
									$glyphicon2 = "<span class=\"symbol glyphicon glyphicon glyphicon-send\"></span>";
									$answerDate = "<span class=\"text\">Antwort verschickt am:<br><strong>".substr($row['processed_time_stamp'],10)."</strong></span>";
								} else{
									$glyphicon2 = "";
									$answerDate = "";
								}
							
								?>
								<div class="message" id="<?php echo ("message_id_".$row['message_id']) ?>">
									<span class="symbol glyphicon <?php echo $glyphicon1 ?>"></span>
									<span class="text">Als <?php echo $solved ?> markiert von:<br><strong><?php echo $processed_by['username'] ?></strong></span>
									<?php echo $glyphicon2 ?>
									<?php echo $answerDate ?>
								</div>
								<?php
							}
							?>
						</div> <!-- closed END -->
						
					</div>
				<?php
				}
				?>

				<script>
				function changeInbox(evt, type) {
					// Declare all variables
					var i, tabcontent, tablinks;

					// Get all elements with class="tabcontent" and hide them
					tabcontent = document.getElementsByClassName("tabcontent");
					for (i = 0; i < tabcontent.length; i++) {
						tabcontent[i].style.display = "none";
					}

					// Get all elements with class="tablinks" and remove the class "active"
					tablinks = document.getElementsByClassName("tablinks");
					for (i = 0; i < tablinks.length; i++) {
						tablinks[i].className = tablinks[i].className.replace(" active", "");
					}

					// Show the current tab, and add an "active" class to the link that opened the tab
					document.getElementById(type).style.display = "block";
					evt.currentTarget.className += " active";
				}
				</script>
			</div> <!-- inbox END -->
			
			<div id="messageContent" style="display:none"> <!-- message START -->
				<button type="button" class="btn btn-default" id="backToInbox">
					<span class="glyphicon glyphicon-menu-left"></span> Zurück zum Posteingang
				</button>
				
				<br><br>
				
				<div class="messageDetail" id="messageDetail">
				</div>
			</div> <!-- message END -->
			
			<script>
			$(document).ready(function(){
				$('#bug').css('display', 'block'); //Alle Tabs sind anfangs ausgeblendet; das sorgt dafür, dass Bugs eingeblendet ist
				
				var m_id = "";
				
				//Open Message
				$(".message").click(function(){
					//Für andere Funktion speichern
					m_id = $(this).attr('id');
					
					//Nachricht Zeit zum Laden geben
					$("#messageDetail").html("Nachricht wird geladen...");
					
					//Nachricht laden
					$.ajax({
						url: "admin_loadMessage.php",
						type: "post",
						data: {message_id: $(this).attr('id')} ,
						success: function (data) {
						   $("#messageDetail").html(data);
						},
						error: function() {
						   $("#messageDetail").html("Die Nachricht konnte nicht geladen werden!");
						}
					});
					
					//Anzeige
					$("#inbox").hide();
					$("#messageContent").show();
					
					/*Update Inbox*/
					var this_save = this;
					//last read
					$(this).find(".lastRead").html("<br>Zuletzt gelesen: <strong><?php echo $userRow['username'] ?></strong>");
					//assigned to glyphicon
					$.ajax({
						url: "admin_updateInbox1.php",
						type: "post",
						data: {message_id: $(this).attr('id')} ,
						success: function (data) {
							var output = "<span class=\"symbol glyphicon glyphicon-" + data + "\"></span>";
							$(this_save).find(".assignedToGlyphicon").html(output); //Hier stimmt noch was nicht!
						}
					});
					//assigned to
					$.ajax({
						url: "admin_updateInbox2.php",
						type: "post",
						data: {message_id: $(this).attr('id')} ,
						success: function (data) {
							var output = "<span class=\"text\">Wird bearbeitet von:<br><strong>" + data + "</strong></span>";
							$(this_save).find(".assignedTo").html(output);
						}
					});
				});
				
				//Nachricht-zuweisen-Button
				$('#messageDetail').on('click', '#assignButton', function(e) {
					e.preventDefault();
					$.ajax({
						url: "admin_assignMessage.php",
						type: "post",
						data: $("#assignForm").serialize() + "&message_id=" + m_id,
						success: function (data) {
							alert("Erfolgreich zugewiesen (Refresh, dass das auch angezeigt wird)!");
							//alert(data);
							//var output = "<span class=\"text\">Wird bearbeitet von:<br><strong>" + data + "</strong></span>";
							$(this_save).find(".changeAssignedTo").html(data); //FUNKTIONIERT NICHT
						},
						error: function() {
							alert("Error!");
						}
					});
				});
				
				//Nachricht-bearbeitet-Button
				$('#messageDetail').on('click', '#finishButton', function() {
					$.ajax({
						url: "admin_finishMessage.php",
						type: "post",
						data: $("#finishForm").serialize() + "&message_id=" + m_id,
						success: function (data) {
							alert(data);
							$(this_save).find("#finishModalBody").html("Super, weiter so!");
						},
						error: function() {
							alert("Error!");
						}
					});
					$(document).ajaxStop(function (){
						location.reload();
					});
				});
				
				//Open Inbox
				$("#backToInbox").click(function(){
					$("#inbox").show();
					$("#messageContent").hide();
				});
				
				//Switch between open and closed
				$('.closed1').click(function(){
					$(this).closest('.tabcontent').find('.openMessages').hide();
					$(this).closest('.tabcontent').find('.closedMessages').show();
				});
				$('.open2').click(function(){
					$(this).closest('.tabcontent').find('.openMessages').show();
					$(this).closest('.tabcontent').find('.closedMessages').hide();
				});
				
			});	
			</script>
		
		</div> <!-- messages END -->
		
			
	<div id="notifications" class="tab-pane fade">
			<h3>Nachrichten</h3>
			<p>Diese Administratoren werden benachrichtigt, wenn neue Nachrichten empfangen werden oder wenn andere ihnen Nachrichten zuweisen:</p>
			<?php
			$sql = "
				SELECT *
				FROM users
				LEFT OUTER JOIN admin_notifications ON users.user_ID = admin_notifications.admin_id
				WHERE users.admin = 1 AND admin_notifications.admin_id IS NULL
			";
			$result = mysqli_query($con, $sql);
			$messageDisplay = "style=\"display:none\"";
			if(mysqli_num_rows($result)==0){
				$addDisplay = "style=\"display:none\"";
				$messageDisplay = "";
			}else{
				$options = "<option disabled selected value style=\"display:none\"> -- Bitte wählen -- </option>";
				while($row = mysqli_fetch_assoc($result)){
					$options .= "<option value=\"".$row['user_ID']."\">".$row['first_name']." ".$row['last_name']." (".$row['username'].")</option>";
				}
			}
			?>
			<p <?php echo $messageDisplay ?>><i>(Es gibt keine Admins, die nicht benachrichtigt werden.)</i></p>
			<div <?php if(isset($addDisplay)) echo $addDisplay ?>>
				<form action="admin_notifications_submit.php" method="post" class="form-inline">
					<div class="form-group">
						<label>Administrator 
							<select name="admin_id" class="form-control" required>
								<?php echo $options ?>
							</select>
						</label>	
					</div>
					<button class="btn btn-default" id="assignButton">Hinzufügen</button>
				</form>
			</div>
			<br>
			
			<?php
			$sql = "
				SELECT *
				FROM admin_notifications
				JOIN users ON admin_notifications.admin_id = users.user_ID
				WHERE type = 'messages'
			";
			$result = mysqli_query($con, $sql);
			while($row = mysqli_fetch_assoc($result)){
				echo "<p style=\"font-size:25px; display:flex; align-items: center;\"><span id=\"".$row['admin_id']."\" class=\"glyphiconDelete glyphicon glyphicon-minus-sign\" title=\"Diesen Admin nicht mehr benachrichtigen\" style=\"color:red; cursor: pointer; cursor: hand;\"></span>&nbsp".$row['first_name']." ".$row['last_name']." (".$row['username'].")</p>";
			}
			?>
			
			<script>
			$(document).ready(function(){
				$(".glyphiconDelete").click(function(){
					var help_this = this;
					$.ajax({
						url: "admin_notifications_delete.php",
						type: "post",
						data: {admin_id: $(this).attr('id')} ,
						success: function (data) {
							$(help_this).closest("p").fadeOut();
							location.reload();
						},
						error: function() {
						   alert("Beim Löschen ist ein Fehler aufgetreten!");
						}
					});
					
				});
			});
			
			</script>
			
		</div>
	<!--<div id="menu3" class="tab-pane fade">
			<h3>Menu 3</h3>
			<p>Eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
		</div>
	-->
	</div>
</div>

<script>
// Javascript to enable link to tab
var url = document.location.toString();
if (url.match('#')) {
	$('.nav-tabs a[href="#'+url.split('#')[1]+'"]').tab('show') ;
} 

// With HTML5 history API, we can easily prevent scrolling!
$('.nav-tabs a').on('shown.bs.tab', function (e) {
	if(history.pushState) {
		history.pushState(null, null, e.target.hash); 
	} else {
		window.location.hash = e.target.hash; //Polyfill for old browsers
	}
})

$('#linkToAdminEdit').click(function(event){
 	$('.nav-tabs a[href="#modifyData"]').tab('show');
});
$('#linkToAdminMessages').click(function(event){
 	event.preventDefault();
 	$('.nav-tabs a[href="#messages"]').tab('show');
});
$('#linkToAdminNotifications').click(function(event){
 	event.preventDefault();
 	$('.nav-tabs a[href="#notifications"]').tab('show');
});
</script>

</body>
</html>