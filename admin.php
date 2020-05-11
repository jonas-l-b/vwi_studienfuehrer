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

<head>
	<link rel="stylesheet" href="res/css/sem.css">
</head>

<body>

<?php include "inc/nav.php" ?>

<div class="container" style="margin-top:60px">

	<h2>Ja servus, lieber Administrator des Studienführers!</h2>
	<br>
		
<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" href="#modifyData">Daten bearbeiten</a></li>
		<?php
		if(mysqli_num_rows(mysqli_query($con, "SELECT * FROM messages WHERE processed = 0")) > 0){
			$envelope = "<span class=\"glyphicon glyphicon-envelope\"></span>";
		}
		?>
		<li><a data-toggle="tab" href="#messages">Posteingang<?php if(isset($envelope)) echo "  ".$envelope?></a></li>
		<li><a data-toggle="tab" href="#notifications">Benachrichtigungen</a></li>
		<li><a data-toggle="tab" href="#adminList">Admin-Liste</a></li>
		<li><a data-toggle="tab" href="#userProfiles">Nutzerprofile</a></li>
		<li><a data-toggle="tab" href="#notes">Meldungen Startseite</a></li>
		<li><a data-toggle="tab" href="#semproAds">SemPro-Werbung</a></li>
		<li><a data-toggle="tab" href="#update">Update</a></li>
		<?php if($userRow['super_admin'] == 1){ ?>
		<li><a data-toggle="tab" href="#spam">Spam</a></li>
		<?php } ?>
	</ul>

	<div class="tab-content">
		<div id="modifyData" class="tab-pane fade in active">
			<br>
			
			<p><i>Hier kann die Datenbank maßgeblich verändert werden. Vorsicht dabei, Backups und Validierungen gibt's quasi (noch) nicht :)</i></p>
			<p><i>Edit: Mittlerweile gibt es automatische Backups (ca. 1x pro Woche). Sie befinden sich auf dem Server im Ordner <b>studienfuehrer/database_backup</b>.</i></p>
			
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
							$result = mysqli_query($con, "SELECT * FROM messages WHERE message_type = '$types[$i]' AND processed = 0 ORDER BY time_stamp DESC");
							if(mysqli_num_rows($result) == 0){
								$noMessageOpen[$i] = "<br><i>Keine offenen Nachrichten vorhanden!</i>";
							}
							
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
									<span class="text">Empfangen: <?php echo substr($row['time_stamp'],0,strlen($row['time_stamp'])-3)." Uhr"?><span class="lastRead"><?php echo $lastRead ?></span></span>
									<span class="assignedToGlyphicon"> <?php echo $glyphicon2Line ?> </span>
									<span class="assignedTo"> <?php echo $assignedToLine ?> </span>
								</div>
								<?php
							}
							if(isset($noMessageOpen[$i])) echo $noMessageOpen[$i];
							?>
						</div> <!-- open END -->
						
						<div class="closedMessages" style="display:none"> <!-- closed START -->
							<p style="font-size:20px"><span class="open2" style="color:lightgrey; cursor: pointer; cursor: hand;">Offen</span> | <span class="closed2" style="font-weight:bold" >Bearbeitet</span></p>
								
							<?php
							$result = mysqli_query($con, "SELECT * FROM messages WHERE message_type = '$types[$i]' AND (processed = 1 OR processed = 2) ORDER BY time_stamp DESC");
							if(mysqli_num_rows($result) == 0){
								$noMessageClosed[$i] = "<br><i>Keine bearbeiteten Nachrichten vorhanden!</i>";
							}
							
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
									$answerDate = "<span class=\"text\">Antwort verschickt am:<br><strong>".substr($row['processed_time_stamp'],0,strlen($row['processed_time_stamp'])-3)." Uhr</strong></span>";
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
							if(isset($noMessageClosed[$i])) echo $noMessageClosed[$i];
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
							var output = "<span class=\"symbol glyphicon glyphicon-" + data.trim() + "\"></span>";
							$(this_save).find(".assignedToGlyphicon").html(output);
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
					$("#finishForm").submit(function(){
						$.ajax({
							url: "admin_finishMessage.php",
							type: "post",
							data: $("#finishForm").serialize() + "&message_id=" + m_id,
							success: function (data) {
								//alert(data);
								$(this_save).find("#finishModalBody").html("Super, weiter so!");
							},
							error: function(data) {
								alert(data);
							}
						});
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
			<br>
			<p><i>Diese Administratoren werden benachrichtigt, wenn neue Nachrichten empfangen werden oder wenn andere ihnen Nachrichten zuweisen:</i></p>
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
			<p <?php echo $messageDisplay ?>><i>(Admin hinzufügen deaktiviert: Es gibt keine weiteren Admins in der Datenbank, die nicht benachrichtigt werden.)</i></p>
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
				ORDER BY users.first_name
			";
			$result = mysqli_query($con, $sql);
			while($row = mysqli_fetch_assoc($result)){
				echo "<p style=\"font-size:20px; display:flex; align-items: center;\"><span id=\"".$row['admin_id']."\" class=\"glyphiconDelete glyphicon glyphicon-minus-sign\" title=\"Diesen Admin nicht mehr benachrichtigen\" style=\"color:lightgrey; cursor: pointer; cursor: hand;\"></span>&nbsp".$row['first_name']." ".$row['last_name']." (".$row['username'].")</p>";
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
		<div id="adminList" class="tab-pane fade">
		<br>
			<p><i>Wir haben zwei Arten von Administratoren: Admins und Super-Admins. Admins können grundsätzlich alles tun, was in diesem Admin-Bereich zur Auswahl steht (Daten verändern, Nachrichten bearbeiten etc.). Super-Admins können zusätzlich Admins und Super-Admins ernennen und diese Rechte auch wieder entziehen. Super-Admins können außerdem Nachrichten im Posteingang löschen (was eigentlich nicht vorgesehen ist, da Nachrichten bearbeitet werden sollen) und Kommentare+Bewertungen sämtlicher Benutzer löschen (was auch nicht vorgesehen ist). Super-Admins haben außerdem die Möglichkeit, Massenmails zu verschicken.<br><br> Er wird registriert, wann wer wem Rechte zuschreibt oder entzieht.</i></p>
			<div class="row">
				<!-- Admin-->
				<?php
				//Vorbereitung Super-Admins
				if($userRow['super_admin'] == 1){
					$displayDeleteGlyphicon = "";
				}else{
					$displayDeleteGlyphicon = "display:none";
				}
				?>
				
				
				<div class="col-md-6">
					<h3>Admins</h3>
					
					<?php if($userRow['super_admin'] == 1){ ?>
						<?php
						$sql = "
							SELECT *
							FROM users
							WHERE admin = 0
						";
						$result = mysqli_query($con, $sql);

						$options = "<option disabled selected value style=\"display:none\"> -- Bitte wählen -- </option>";
						while($row = mysqli_fetch_assoc($result)){
							$options .= "<option value=\"".$row['user_ID']."\">".$row['first_name']." ".$row['last_name']." (".$row['username'].")</option>";
						}
						?>
						
						<div>
							<form action="admin_addAdmin_submit.php" method="post" class="form-inline">
								<div class="form-group">
									<label> 
										<select name="user_id" class="form-control" required>
											<?php echo $options ?>
										</select>
									</label>	
								</div>
								<button class="btn btn-default" id="assignButton">Admin-Rechte zuweisen</button>
							</form>
						</div>
						<br>
					<?php } ?>
					
					<?php
						$sql = "
								SELECT *
								FROM users
								WHERE admin = 1 AND super_admin != 1
								ORDER BY first_name
						";
						$result = mysqli_query($con, $sql);
						while($row = mysqli_fetch_assoc($result)){
							echo "<p style=\"font-size:20px; display:flex; align-items: center;\"><span id=\"".$row['user_ID']."\" class=\"glyphiconDeleteAdmin glyphicon glyphicon-minus-sign\" title=\"Diesem Admin seine Rechte entziehen\" style=\"".$displayDeleteGlyphicon."; color:lightgrey; cursor: pointer; cursor: hand;\">&nbsp</span>".$row['first_name']." ".$row['last_name']." (".$row['username'].")</p>";
						}
					?>
					<script>
					$(document).ready(function(){
						$(".glyphicon-minus-sign").hover(function(){
							$(this).css("color","red");
						},
						function(){
							$(this).css("color","lightgrey");
						}); 
					});
					</script>
				</div>
				
				<!-- Super-Admin-->
				<div class="col-md-6">
					<h3>Super-Admins</h3>
					
					<?php if($userRow['super_admin'] == 1){ ?>
						<?php
						$sql = "
							SELECT *
							FROM users
							WHERE super_admin = 0 AND admin = 1
						";
						$result = mysqli_query($con, $sql);

						$options = "<option disabled selected value style=\"display:none\"> -- Bitte wählen -- </option>";
						while($row = mysqli_fetch_assoc($result)){
							$options .= "<option value=\"".$row['user_ID']."\">".$row['first_name']." ".$row['last_name']." (".$row['username'].")</option>";
						}
						?>
						
						<div>
							<form action="admin_addSuperAdmin_submit.php" method="post" class="form-inline">
								<div class="form-group">
									<label> 
										<select name="user_id" class="form-control" required>
											<?php echo $options ?>
										</select>
									</label>	
								</div>
								<button class="btn btn-default" id="assignButton">Super-Admin-Rechte zuweisen</button>
							</form>
						</div>
						<br>
					<?php } ?>
					
					<?php
						$sql = "
								SELECT *
								FROM users
								WHERE admin = 1 AND super_admin = 1
								ORDER BY first_name
						";
						$result = mysqli_query($con, $sql);
						while($row = mysqli_fetch_assoc($result)){
							echo "<p style=\"font-size:20px; display:flex; align-items: center;\"><span data-id=\"".$row['user_ID']."\" class=\"glyphiconDeleteSuperAdmin glyphicon glyphicon-minus-sign\" title=\"Diesem Admin seine Rechte entziehen\" style=\"".$displayDeleteGlyphicon."; color:lightgrey; cursor: pointer; cursor: hand;\">&nbsp</span>".$row['first_name']." ".$row['last_name']." (".$row['username'].")</p>";
						}
					?>
				</div>
			</div>
			
			<script>
			$(document).ready(function(){
				$(".glyphiconDeleteAdmin").click(function(){
					var help_this = this;
					$.ajax({
						url: "admin_admin_delete.php",
						type: "post",
						data: {user_id: $(this).attr('id')} ,
						success: function (data) {
							//alert(data);
							$(help_this).closest("p").fadeOut();
							location.reload();
						},
						error: function() {
						   alert("Beim Löschen ist ein Fehler aufgetreten!");
						}
					});
				});
				
				$(".glyphiconDeleteSuperAdmin").click(function(){
					var help_this = this;
					$("#superAdminId").val($(this).data('id'));
					$('#deleteSuperAdminModal').modal({show:true});
				});
							
				$("#deleteSuperAdminForm").submit(function(e){
					$.ajax({
						type: "POST",
						url: "admin_superadmin_delete_submit.php",
						data: $("#deleteSuperAdminForm").serialize(),
						success: function(data) {
							//alert(data);
							if(data.trim()=="erfolg"){
								location.reload();
							}else{
								alert("Bei der Aktualisierung der Datenbank ist ein Fehler aufgetreten!");
							}

						}
					});
					e.preventDefault();
				});
			});
			</script>
			
			<div id="deleteSuperAdminModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h2 class="modal-title">Rechte entziehen</h2>
				</div>
					<div class="modal-body">
						<form action="/" id="deleteSuperAdminForm">
							<input type="hidden" name ="superAdminId" id="superAdminId" />

							<p>Möchtest du diesem Nutzer nur die Rechte als Super-Admin oder auch die Rechte als normaler Admin entziehen?</p>
							
							<select class="form-control" name="deleteAim">
								<option value="deleteSuperOnly">Nur Super-Admin-Rechte entziehen</option>
								<option value="deleteBoth">Admin- und Super-Admin-Rechte entziehen</option>
							</select>
							
							<br>
							
							<button class="btn btn-primary" type="submit" >Rechte jetzt entziehen</button>

							
						</form>
					</div><!-- End of Modal body -->
				</div><!-- End of Modal content -->
				</div><!-- End of Modal dialog -->
			</div><!-- End of Modal -->
			
		</div>
		<div id="userProfiles" class="tab-pane fade">
			<br>
			<p><i>
			Hier kannst du die Profile aller Nutzer einsehen, die sich beim Studienführer registriert haben. Das kann beispielsweise hilfreich sein, wenn du einen Nutzer direkt per E-Mail kontaktieren möchtest. Außerdem kannst du so einem Nutzer die Aktivierungsmail erneut schicken, falls das Profil noch nicht aktiviert wurde und der Nutzer seine Mail nicht findet.<br><br>Bitte gehe verantwortungsvoll mit diesen Daten um!
			</i></p>
			
			
			
			<?php
			$sql = "SELECT * FROM users	ORDER BY last_name, first_name";
			$users_selection = "";
			$result = mysqli_query($con,$sql);

			while($row = mysqli_fetch_assoc($result)){
				$users_selection .= '<div class="item" data-value="'.$row['user_ID'].'">'.$row['last_name'].", ".$row['first_name']." (".$row['username'].")</div>";
			}
			?>
			<form id="viewUserForm">
				<div class="form-group">
					<div class="ui fluid search selection dropdown">
						<input class="form-control" type="hidden" required name="userID">
						<i class="dropdown icon"></i>
						<div class="default text">Nutzer wählen</div>
						<div class="menu">
						<?php echo $users_selection ?>
						</div>
					</div>
				</div>
			</form>
			
			<div>
				<button id="viewUser_submit" class="btn btn-primary">Nutzer ansehen</button>
			</div>
			
			<div id="userInfoTable"></div>
			
			<script>
			$('#viewUser_submit').click(function () {
				// AJAX code to submit form.
				$.ajax({
					type: "POST",
					url: "admin_viewUser_submit.php",
					data: $("#viewUserForm").serialize(),
					success: function(data) {
						if(data.trim() == "pleaseChoose"){
							alert("Bitte Nutzer im Dropdown auswählen.");
						}else{
							$("#userInfoTable").html(data);
						}
					}
				});
			});
			
			function reSendMail(userId){
				$.ajax({
					type: "POST",
					url: "reSendActivation.php",
					data: {userId: userId},
					success: function(data) {
						alert(data.trim());
					}
				});
			};
			
			$('.ui.dropdown')
			  .dropdown({
				fullTextSearch: true,
				useLabels: false
			  })
			;
			</script>
		</div>
		<div id="notes" class="tab-pane fade">
			<br>
			<p><i>Hier können die Meldungen, die auf der Startseite erscheinen, verändert werden. <span style="color:red">Vorsicht: Änderungen werden nur mit Klick auf "Änderung speichern" übernommen!</span></i></p>
			
			<?php
			$note = array();
			$color = array();
			$sql = "SELECT * FROM notes";
			$result = mysqli_query($con, $sql);
			while($row = mysqli_fetch_assoc($result)){
				$note[$row['name']] = $row['content'];
				$color[$row['name']] = $row['color'];
			}
			
			switch($color['noteLeft']) {
				case "blue":
					$colorLeft = "#e6f3ff";
					break;
				case "orange":
					$colorLeft = "#fff0e2";
					break;
				default:
					$colorLeft = "#ffffff";
			}
			
			switch($color['noteMiddle']) {
				case "blue":
					$colorMiddle = "#e6f3ff";
					break;
				case "orange":
					$colorMiddle = "#fff0e2";
					break;
				default:
					$colorMiddle = "#ffffff";
			}
			
			switch($color['noteRight']) {
				case "blue":
					$colorRight = "#e6f3ff";
					break;
				case "orange":
					$colorRight = "#fff0e2";
					break;
				default:
					$colorRight = "#ffffff";
			}

			?>
			
			<h2>So sieht das Ergebnis aus</h2>
			
			<hr>
			
			<div class="row">
				<div class="col-md-4">
					<div class="notes" style="background-color:<?php echo $colorLeft?>">
						<div class="innernote" id="noteLeft">
							<?php echo $note['noteLeft'];?>
						</div>
					</div>
				</div>
				
				<div class="col-md-4 notesTop">
					<div class="notes" style="background-color:<?php echo $colorMiddle?>">
						<div class="innernote" id="noteMiddle">
							<?php echo $note['noteMiddle'];?>
						</div>
					</div>
				</div>
				
				<div class="col-md-4">
					<div class="notes" style="background-color:<?php echo $colorRight?>">
						<div class="innernote" id="noteRight">
							<?php echo $note['noteRight'];?>
						</div>
					</div>
				</div>	
			</div>
			
			<hr>
			
			<h2>Hier wird der Inhalt bearbeitet</h2>
			
			<div class="row">
				<div class="col-md-6">

						<h4>Meldung Links</h4>
						<div class="form-group">
							<textarea class="form-control note-input" rows="5" id="noteLeftInput">
								<?php echo $note['noteLeft'];?>
							</textarea>
							<form class="form-inline" style="margin-top:5px">
								<label for="noteLeftColor">Farbe:</label>
								<select class="form-control note-color" id="noteLeftColor">
									<option value="blue" <?php if($color['noteLeft'] == "blue") echo "selected"?>>blau</option>
									<option value="white" <?php if($color['noteLeft'] == "white") echo "selected"?>>weiß</option>
									<option value="orange" <?php if($color['noteLeft'] == "orange") echo "selected"?>>orange</option>
								</select>
							</form>
						</div>
						<h4>Meldung Mitte</h4>
						<div class="form-group">
							<textarea class="form-control note-input" rows="5" id="noteMiddleInput">
								<?php echo $note['noteMiddle'];?>
							</textarea>
							<form class="form-inline" style="margin-top:5px">
								<label for="noteMiddleColor">Farbe:</label>
								<select class="form-control note-color" id="noteMiddleColor">
									<option value="blue" <?php if($color['noteMiddle'] == "blue") echo "selected"?>>blau</option>
									<option value="white" <?php if($color['noteMiddle'] == "white") echo "selected"?>>weiß</option>
									<option value="orange" <?php if($color['noteMiddle'] == "orange") echo "selected"?>>orange</option>
								</select>
							</form>
						</div>
						<h4>Meldung Rechts</h4>
						<div class="form-group">
							<textarea class="form-control note-input" rows="5" id="noteRightInput">
								<?php echo $note['noteRight'];?>
							</textarea>
							<form class="form-inline" style="margin-top:5px">
								<label for="noteRightColor">Farbe:</label>
								<select class="form-control note-color" id="noteRightColor">
									<option value="blue" <?php if($color['noteRight'] == "blue") echo "selected"?>>blau</option>
									<option value="white" <?php if($color['noteRight'] == "white") echo "selected"?>>weiß</option>
									<option value="orange" <?php if($color['noteRight'] == "orange") echo "selected"?>>orange</option>
								</select>
							</form>
						</div>
						<button id="changeNotesSubmit" class="btn btn-warning">Änderungen speichern</button>
				
					<script>
					$(document).ready(function(){
						//Text direkt in Meldungsblock schreiben
						$(".note-input").on("change paste keyup", function() {
							$('#' + $(this).attr('id').slice(0,-5)).html($(this).val());
						});
						
						//Farbe direkt in Meldungsblock ändern
						$(".note-color").change(function(){
							switch($(this).val()) {
								case "blue":
									bColor = "#e6f3ff";
									break;
								case "orange":
									bColor = "#fff0e2";
									break;
								default:
									bColor = "#ffffff";
							}

							$('#' + $(this).attr('id').slice(0,-5)).parent().css("background-color", bColor);
						});
						
						//Änderungen in Datenbank speichern
						$("#changeNotesSubmit").click(function(){
							noteLeftInput = $('#noteLeftInput').val();
							noteLeftColor = $('#noteLeftColor').val();
							noteMiddleInput = $('#noteMiddleInput').val();
							noteMiddleColor = $('#noteMiddleColor').val();
							noteRightInput = $('#noteRightInput').val();
							noteRightColor = $('#noteRightColor').val();
							
							$.ajax({
								url: "admin_changeNotes_submit.php",
								type: "post",
								data: {noteLeftInput: noteLeftInput, noteMiddleInput: noteMiddleInput, noteRightInput: noteRightInput, noteLeftColor: noteLeftColor, noteMiddleColor: noteMiddleColor, noteRightColor: noteRightColor} ,
								success: function (data) {
									alert(data);
								},
								error: function() {
								   alert("Es ist ein Fehler aufgetreten!");
								}
							});
						});
					});
					</script>
					
				</div>
				<div class="col-md-6">
					<h4>HTML-Tags benutzen</h4>
					<p>Für die Formatierung können HTML-Tags verwendet werden. Grundsätzliche HTML-Tags werden hier aufgeführt. Viele weitere finden sich im Netz.</p>
					<table class="table table-striped" style="table-layout: fixed;">
						<thead>
							<tr>
								<th>HTML-Code</th>
								<th>Ergebnis</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Das ist ein&lt;br&gt;Absatz</td>
								<td>Das ist ein<br>Absatz</td>
							</tr>
							<tr>
								<td>&lt;p&gt;Das ist Paragrapgh.&lt;/p&gt;<br>&lt;p&gt;Das auch.&lt;/p&gt;</td>
								<td><p>Das ist Paragrapgh.</p><p>Das auch.</p></td>
							</tr>
							<tr>
								<td>&lt;strong&gt;Fett&lt;/strong&gt;</td>
								<td><strong>Fett</strong></td>
							</tr>
							<tr>
								<td>&lt;i&gt;Kursiv&lt;/i&gt;</td>
								<td><i>Kursiv</i></td>
							</tr>
							<tr>
								<td>&lt;u&gt;Unterstichen&lt;/u&gt;</td>
								<td><u>Unterstichen</u></td>
							</tr>
							<tr>
								<td>&lt;h1&gt;Überschrift 1&lt;/h1&gt;</td>
								<td><h1>Überschrift 1</h1></td>
							</tr>
							<tr>
								<td>&lt;h2&gt;Überschrift 2&lt;/h2&gt;</td>
								<td><h2>Überschrift 2</h2></td>
							</tr>
							<tr>
								<td>&lt;h3&gt;Überschrift 3&lt;/h3&gt;</td>
								<td><h3>Überschrift 3</h3></td>
							</tr>
							<tr>
								<td>&lt;h4&gt;Überschrift 4&lt;/h4&gt;</td>
								<td><h4>Überschrift 4</h4></td>
							</tr>
							<tr>
								<td>&lt;a href="tree.php"&gt;Für Startseite hier klicken&lt;/a&gt;</td>
								<td><a href="tree.php">Für Startseite hier klicken</a></td>
							</tr>
							<tr>
								<td>&lt;a href="https://www.google.de/"&gt;Externer Link (Google)&lt;/a&gt;</td>
								<td><a href="https://www.google.de/">Externer Link (Google)</a></td>
							</tr>
							<tr>
								<td>&lt;a href="userProfile.php" class="btn btn-primary"&gt;Zum Profil&lt;/a&gt;</td>
								<td><a href="userProfile.php" class="btn btn-primary">Zum Profil</a></td>
							</tr>
						</tbody>
					</table>
				
				</div>
			</div>
		</div>
		
		<div id="semproAds" class="tab-pane fade">
			<br>
			<p>
				<i>
					Hier kannst du Werbung für kommende SemPro-Events auf Veranstaltungsseiten schalten. Pro Veranstaltung ist gleichzeitig nur eine Werbung möglich.
					<span style="color:red">Sind für eine Veranstaltung mehrere Events eingetragen, so wird nur das Event mit der nächsten <u>Anmeldungs</u>deadline beworben.</span>
					Nach Ablauf des SemPro-Events wird die geschaltete Werbung automatisch gelöscht.
				</i>
			</p>
			
			<h3>Kommende SemPro-Events:</h3>
			
			<?php
			/*Check for passed events and delete from database*/
			//Get upcoming event_ids
			$sql = "
				SELECT * FROM `jom_vwi_semesterprogramm`
				WHERE application_date >= now()
			";			
			$result = mysqli_query($con_hp, $sql);

			$ids = array(0);
			while($row = mysqli_fetch_assoc($result)) {
				$ids[] = $row['event_id'];
			}
			$upcoming_events_ids = implode(',', $ids);

			//Delete passed events
			mysqli_query($con, "DELETE FROM `sempro_ads` WHERE event_id NOT IN ($upcoming_events_ids)");
			?>
			
			<?php
			$sql = "
				SELECT * FROM `jom_vwi_semesterprogramm`
				WHERE application_date >= now()
				ORDER BY application_date
			";
			$result = mysqli_query($con_hp, $sql);
			
			$subject_selection = "<option value='' disabled selected>Veranstaltung auswählen</option>";
			$subject_selection_result = mysqli_query($con, "SELECT * FROM subjects");

			while($subject_selection_row = mysqli_fetch_assoc($subject_selection_result)){
				$subject_selection .= "<option value=".$subject_selection_row['ID'].">".$subject_selection_row['subject_name']."</option>";
			}
			
			?>

			<?php
			$i = 1;
			while($row = mysqli_fetch_assoc($result)){
				?>
				<div style="background-color:#F8F8F8; border-radius:3px; padding: 10px">
					<h4>
						<?php echo $row["event_name"]?>
					</h4>
					
					<form id="addSubjectForm<?php echo $i?>" method="POST">
						<div class="form-group">
							<select id="subject_selection" name="subject_selection" class="search ui fluid dropdown form-control" required>
								<?php echo $subject_selection ?>
							</select>
						</div>
						
						<div class="form-group" style="display:none">
							<input id="event_id" name="event_id" value="<?php echo $row['event_id']?>">
						</div>
						
						<button type="submit" class="btn btn-default">Veranstaltung hinzufügen</button>
					</form>
					
					<script>
					$(document).ready(function(){
						$("#addSubjectForm<?php echo $i?>").submit(function(e){
							$.ajax({
								type: "POST",
								url: "admin_addSubject_submit.php",
								data: $("#addSubjectForm<?php echo $i?>").serialize(),
								success: function(data) {
									if(data.trim() != ""){
										alert(data);
									}
								}
							});
							//e.preventDefault();
						});
					});	
					</script>
					
					<?php
					$sql = "
						SELECT * FROM `sempro_ads`
						JOIN subjects ON sempro_ads.subject_id = subjects.ID
						WHERE event_id = ".$row['event_id']."
					";
					$event_result = mysqli_query($con, $sql);
					
					if(mysqli_num_rows($event_result)>0) echo "<br>";
					
					while($ad_subject = mysqli_fetch_assoc($event_result)){
						//Mark subject that exists multiple times
						$color_result = mysqli_query($con, "SELECT * FROM `sempro_ads` WHERE subject_id = ".$ad_subject['ID']);
						if(mysqli_num_rows($color_result)>1){
							$show_warning = "";
						}else{
							$show_warning = "display:none";
						}
						
						echo "
							<p style=\"font-size:17px; display:flex; align-items: center;\">
								<span subject_id=".$ad_subject['ID']." event_id=".$row['event_id']." class=\"glyphiconRemoveSubject glyphicon glyphicon-minus-sign\" title=\"Auf dieser Veranstaltung nicht mehr werben\" style=\"color:lightgrey; cursor: pointer; cursor: hand;\">&nbsp</span>
								".$ad_subject['subject_name']."
								<span>&nbsp</span>
								<span style='$show_warning' class='glyphicon glyphicon-exclamation-sign' data-toggle='tooltip' title='Auf dieser Veranstaltung werden mehrere Events beworben; es wird nur das Event mit der nächsten Deadline angezeigt.'></span>
							</p>
						";
					}
					$i++;
					?>
				</div>
				<br>
				<?php
			}
			?>
			
			<script>		
			$(document).ready(function(){
				
				$('[data-toggle="tooltip"]').tooltip();
				
				$(".glyphiconRemoveSubject").click(function(){
					help_this = this;

					$.ajax({
						type: "POST",
						url: "admin_removeSubject_submit.php",
						data: {subject_id: help_this.getAttribute('subject_id'), event_id: help_this.getAttribute('event_id')},
						success: function(data) {
							$(help_this).closest("p").fadeOut();
							location.reload();
						}
					});
					
				});
				
				$(".glyphicon-minus-sign").hover(function(){
					$(this).css("color","red");
				},
				function(){
					$(this).css("color","lightgrey");
				}); 
			});
			</script>
		
			<script>
			$('.ui.dropdown')
				.dropdown({
					fullTextSearch: true,
					useLabels: true
				})
			;
			</script>
			

			
		</div>
		
		<div id="update" class="tab-pane fade">
			<br>
			<p><i>Hier können automatisch ermittelte Änderungen des Modulhandbuches eingepflegt werden.</i></p>
			<p><i>Der Prozess ist leider etwas fehleranfällig, da einige Schritte manuell durchgeführt werden müssen. Nimm dir darum genügend Zeit, um die Schritte gewissenhaft abzuarbeiten.</i></p>
			
			<h2>Schritt -1: Crawler durchlaufen lassen</h2>
			<p><i>Bevor losgelegt wird, muss der Crawler einmal durchlaufen.</i></p>
			
			<h2>Schritt 0: Backup erstellen</h2>
			<p><i>Sicherheitshalber ein Backup des jetzigen Standes des Studienführers erstellen. <a data-toggle="modal" data-target="#howToBackupModal">Wie nutze ich ein Backup?</a></i></p>
			
			<div class="modal fade" id="howToBackupModal" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Datenbank mit Backup zurücksetzen</h4>
						</div>
						<div class="modal-body">
							<p>
								Bisher gibt es keine Funktion im Studienführer, mit der man die Datenbank auf einen früheren Stand zurücksetzen könnte.
								Sollte tatsächlich ein Fall eintreten, der ein Zurücksetzen nötig macht, kannst du dich an diesen Schritten orientieren.
								Falls du dich überhaupt nicht mit Datenbanken auskennst, hol dir am besten jemanden dazu, der dir hilft.
							</p>
							<ol style="margin-left:10px;">
								<li>Mach am besten nochmal ein Backup der aktuellen Datenbank - man weiß ja nie.</li>
								<li>Logge dich bei 1&1 ein: <i>https://mein.ionos.de/hosting-overview.</i></li>
								<li>Lade im <b>Webspace</b> das Datenbank-Backup herunter, auf das du den Studienführer zurücksetzen willst. Du findest die Backups unter <b>studienfuehrer/database_backup</b>.</li>
								<li>Rufe unter <b>Datenbanken</b> die Studienführer-Datenbank auf (db680704532).</li>
								<li>Lösche alle Tabellen und erstelle sie erneut mit der Backup-Datei.</li>
							</ol>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
						</div>
					</div>
				  
				</div>
			</div>
			
			<button class="btn btn-primary" id="backup-button">Backup jetzt erstellen</button>
			
			<script>
			$( document ).ready(function() {	
				$("#backup-button").click(function(){
					$.ajax({
						type: "POST",
						url: "backupDatabase.php",
						success: function(data) {
							alert(data);
						}
					});
				});
			});
			</script>
			
			<h2>Schritt 1: Tabellen downloaden (Entitäten)</h2>
			<p><i>Alle Tabellen herunterladen und damit die Python-Skripe ausführen.</i></p>
			<?php

			$tables = array("subjects", "lecturers", "institutes", "modules");

			foreach ($tables as $table) {
				?>
				<button onclick="Export('<?php echo $table ?>')" class="btn btn-primary" style="margin:5px"><?php echo $table ?></button>
				<?php
			}
			?>
			
			<!--
			<div class="panel-group">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" href="#collapse1">Download Tabellen</a>
					</h4>
				</div>
				<div id="collapse1" class="panel-collapse collapse">
					<?php

					$tables = array("subjects", "lecturers", "institutes", "modules");

					foreach ($tables as $table) {
						?>
						<button onclick="Export('<?php echo $table ?>')" class="btn btn-default" style="margin:5px"><?php echo $table ?></button>
						<?php
					}
					?>
				</div>
			</div>
			</div>
			-->
			
			<!-- Upload SQL txt -->
			<?php
			$files = glob("uploads/*");
			$now   = time();
			
			foreach ($files as $file) {
				if (is_file($file)) {
					if ($now - filemtime($file) >= 60 * 60 * 24 * 3) { // 3 days
						unlink($file);
					}
				}
			}
			?> 
			
			<h2>Schritt 2: Änderungstabellen hochladen (Entitäten)</h2>
			<p><i>Die Ausgaben der Python-Skripte hochladen. Dateien nicht umbenennen. Dateien, die vor mehr als 3 Tagen hochgeladen wurden, werden automatisch gelöscht.</i></p>
			<div class="grey-border">
				<input id="fileToUpload" type="file" name="fileToUpload" style="margin-top:10px; margin-bottom:10px;"/>
				<button class="btn btn-primary uploadButton">Hochladen</button>
				<br>
				<hr>
				<p>Diese (relevanten) Dateien befinden sich derzeit auf dem Server:</p>
				<?php
				$files = glob("uploads/*");
				$validNames = array(
					"ADDED_SUBJECTS.txt", "CHANGED_SUBJECTS.txt", "DELETED_SUBJECTS.txt",
					"ADDED_MODULES.txt", "CHANGED_MODULES.txt", "DELETED_MODULES.txt",
					"ADDED_LECTURERS.txt", "CHANGED_LECTURERS.txt", "DELETED_LECTURERS.txt",
					"ADDED_INSTITUTES.txt", "CHANGED_INSTITUTES.txt", "DELETED_INSTITUTES.txt",
				);
				$numFiles = 0;
				foreach ($files as $file) {
					if(in_array(basename($file), $validNames)){
						if (is_file($file)) {
							echo "<li><b>".basename($file)."</b> (letzte Änderung: ".date("d.m.y H:i:s", filemtime($file)).")</li>";
							$numFiles++;
						}
					}
				}
				if ($numFiles == 0){
					echo "<i>Keine. Hinweis: Dateien, die vor mehr als 3 Tagen hochgeladen wurden, werden automatisch gelöscht.</i>";
				}
				?>
			</div>
			
			<h2>Schritt 3: Tabellen aktualisieren (Entitäten)</h2>
			<p><i>Es werden nur Buttons angezeigt, wenn eine entsprechende Datei hochgeladen wurde. Hierdruch wird nicht der Studienführer selbst aktualisiert, sondern es werden nur die vorgeschlagenen Änderungen geladen.</i></p>
			<?php
			$validNames = array(
				"ADDED_SUBJECTS.txt", "CHANGED_SUBJECTS.txt", "DELETED_SUBJECTS.txt",
				"ADDED_MODULES.txt", "CHANGED_MODULES.txt", "DELETED_MODULES.txt",
				"ADDED_LECTURERS.txt", "CHANGED_LECTURERS.txt", "DELETED_LECTURERS.txt",
				"ADDED_INSTITUTES.txt", "CHANGED_INSTITUTES.txt", "DELETED_INSTITUTES.txt",
			);
			foreach ($files as $file) {
				if(in_array(basename($file), $validNames)){
					if (is_file($file)) {
						$trimmedName = substr(basename($file), 0, -4);
						echo '<button style="margin:5px" class="sqlUpdate btn btn-primary" data-table="'.$trimmedName.'">'.$trimmedName.' aktualisieren</button>';
					}
				}
			}
			?>
			<!--<button style="margin:5px" class="sqlUpdateAll btn btn-primary" data-type="entities">ALLE aktualisieren</button>-->
			
			<h2>Schritt 4: Änderungen bestätigen (Entitäten)</h2>
			<p><i>Jede einzelne Änderung durch Klick auf den jeweiligen Button bestätigen. Der <span style="color:red">rote</span> Button löscht die jeweilige Zeile aus dieser Tabelle, sodass die Änderung nicht in die Datenbank des Studienführers übertragen wird. <span style="color:red">Das sollte eigentlich nicht vorkommen.</span> Der <span style="color:blue">blaue</span> Button überträgt die Änderung in den Studienführer. Die Kennung kann bei Veranstaltungen und Modulen nicht geändert werden, da die jeweilige Entität damit identifiziert wird.</i></p>
			
			<button class="btn btn-basic" id="close-all">Alle Listen schließen</button>
			<br><br>

			<div class="grey-border">
				<h3>Vorlesungen</h3>
				
				<!-- Changed -->
				<?php
				$sql = "
					SELECT subjects.subject_name AS subject_name, CHANGED_SUBJECTS.id AS id, CHANGED_SUBJECTS.identifier AS identifier, CHANGED_SUBJECTS.changed_value AS changed_value, CHANGED_SUBJECTS.value_old AS value_old, CHANGED_SUBJECTS.value_new AS value_new  FROM `CHANGED_SUBJECTS`
					JOIN subjects ON CHANGED_SUBJECTS.identifier = subjects.identifier
				";
				$result = mysqli_query($con,$sql);
				?>
				
				<h4>
					<span>Geänderte</span>
					<span style="margin-left:5px; margin-right:5px" class="badge badge-pill badge-primary"><?php echo mysqli_num_rows($result) ?></span>
					<a id="display_lectures_changed"><span id="display_lectures_changed_glyph" class="glyphicon glyphicon-chevron-down"></span></a>
				</h4>

				<table class="table table-striped table-bordered table-condensed update-table" id="table_lectures_changed" style="margin-top:15px; display:none">
					<tr>
						<th>Name</th>
						<th>Kennung</th>
						<th>Geändertes Feld</th>
						<th>Alter Wert</th>
						<th>Neuer Wert</th>
						<th>Bearbeiten</th>
						<th>Ändern</th>
						<th>Löschen</th>
					</tr>
				
				<?php
				while($row = mysqli_fetch_assoc($result)){
					?>
					<tr>
						<td><?php echo $row['subject_name'] ?></td>
						<td><?php echo $row['identifier'] ?></td>
						<td><?php echo $row['changed_value'] ?></td>
						<td><?php echo $row['value_old'] ?></td>
						<td><?php echo $row['value_new'] ?></td>
						<td>
							<button type="button" class="btn btn-default" data-toggle="modal" data-target="#editChangedSubjectModal"
								data-id="<?php echo $row['id'] ?>"
								data-subject_name="<?php echo $row['subject_name'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
								data-changed_value="<?php echo $row['changed_value'] ?>"
								data-value_old="<?php echo $row['value_old'] ?>"
								data-value_new="<?php echo $row['value_new'] ?>"
							>Bearbeiten</button>
						</td>
						<td>
							<button type="button" class="btn btn-primary changeSubject_confirmButton"
								data-id="<?php echo $row['id'] ?>"
								data-subject_name="<?php echo $row['subject_name'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
								data-changed_value="<?php echo $row['changed_value'] ?>"
								data-value_old="<?php echo $row['value_old'] ?>"
								data-value_new="<?php echo $row['value_new'] ?>"
							>Änderung bestätigen</button>
						</td>
						<td>
							<button type="button" class="btn btn-danger changeSubject_deleteButton"
								data-id="<?php echo $row['id'] ?>"
								data-subject_name="<?php echo $row['subject_name'] ?>"
							>Löschen</button>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
				
				<a href="#col_changed_subjects" data-toggle="collapse">SQL-Befehle anzeigen / ausblenden</a>
				<div id="col_changed_subjects" class="collapse">
					<p>
						Diese SQL-Befehle können kopiert und direkt in der Datenbank auf einmal ausgeführt werden. Das ist eher nicht empfohlen. Nach der Ausführung muss die jeweilige Tabelle manuell geleert werden.
					</p>
					<div class="update-collapsable">
						<?php
						$sql = "SELECT * FROM CHANGED_SUBJECTS";
						$result = mysqli_query($con, $sql);

						while($row = mysqli_fetch_assoc($result)){
							echo "
								UPDATE `subjects`
								SET `".$row['changed_value']."` = '".$row['value_new']."'
								WHERE `identifier` = '".$row['identifier']."';
								<br>
							";
						}
						?>
					</div>
				</div>
				
				<div class="modal fade" id="editChangedSubjectModal" tabindex="-1" role="dialog" aria-labelledby="editChangedSubjectModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-body" id="changedSubject_edit_modal-body">
							<form id="changedSubject_edit_form">
								<div class="form-group" style="display:none">
									<label class="col-form-label">ID:</label>
									<input type="text" class="form-control" name ="id" id="id">
								</div>
								<div class="form-group">
									<label class="col-form-label">Name:</label>
									<input type="text" class="form-control" name="subject_name" id="subject_name" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Kennung:</label>
									<input type="text" class="form-control" name="identifier" id="identifier" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Geändertes Feld:</label>
									<input type="text" class="form-control" name="changed_value" id="changed_value" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Alter Wert:</label>
									<input type="text" class="form-control" name="value_old" id="value_old" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Neuer Wert:</label>
									<input type="text" class="form-control" name="value_new" id="value_new">
								</div>
							</form>
						</div>
						<div class="modal-footer" id="changedSubject_edit_modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
							<button type="button" class="btn btn-primary" id="lecture-changed_save-changes-button">Änderungen speichern</button>
						</div>
					</div>
				  </div>
				</div>
				
				<script>
				$( document ).ready(function() {
					//Show and hide
					$("#display_lectures_changed").click(function() {
						if($("#table_lectures_changed").is(":visible")){
							$("#table_lectures_changed").hide();
							$("#display_lectures_changed_glyph").removeClass("glyphicon glyphicon-chevron-up");
							$("#display_lectures_changed_glyph").addClass("glyphicon glyphicon-chevron-down");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=subject_changed&value=0"});
						}else{
							$("#table_lectures_changed").show();
							$("#display_lectures_changed_glyph").removeClass("glyphicon glyphicon-chevron-down");
							$("#display_lectures_changed_glyph").addClass("glyphicon glyphicon-chevron-up");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=subject_changed&value=1"});
						}
					});
					
					//show modal
					$('#editChangedSubjectModal').on('show.bs.modal', function (event) {
						var button = $(event.relatedTarget) // Button that triggered the modal
						
						var id = button.data('id')
						var subject_name = button.data('subject_name')
						var identifier = button.data('identifier')
						var changed_value = button.data('changed_value')
						var value_old = button.data('value_old')
						var value_new = button.data('value_new')
						
						var modal = $(this)
						modal.find('.modal-body #id').val(id)
						modal.find('.modal-body #subject_name').val(subject_name)
						modal.find('.modal-body #identifier').val(identifier)
						modal.find('.modal-body #changed_value').val(changed_value)
						modal.find('.modal-body #value_old').val(value_old)
						modal.find('.modal-body #value_new').val(value_new)
					})
					
					//save changes
					$('#lecture-changed_save-changes-button').click(function() {
						$.ajax({
							type: "POST",
							url: "admin_updateLectureChanged_edit_submit.php",
							data: $("#changedSubject_edit_form").serialize(),
							success: function(data) {
								//alert(data);
								if(data.includes("erfolg")){
									$('#changedSubject_edit_modal-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Änderungen erfolgreich gespeichert!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" onClick=\"window.location.reload()\">Schließen & Seite neu laden</button>");
								}else{
									$('#changedSubject_edit_modal-body').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Beim Speichern ist womöglich ein Fehler aufgetreten! Bitte probiere es erneut (oftmals liegt es am Server, sodass es beim zweiten Mal klappt).</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen & Seite neu laden</button>");
								}
								$('#changedSubject_edit_modal-footer').hide();
							}
						});
					});
					
					//Change subject
					$('.changeSubject_confirmButton').click(function(){
						
						var id = $(this).data('id')
						var subject_name = $(this).data('subject_name')
						var identifier = $(this).data('identifier')
						var changed_value = $(this).data('changed_value')
						var value_old = $(this).data('value_old')
						var value_new = $(this).data('value_new')

						var result = confirm('Bei Veranstaltung "'+subject_name+'" wirklich den Wert von '+changed_value+' von '+value_old+' zu '+value_new+' ändern?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateLectureChanged_confirm_submit.php",
								data: "id=" + id + "&identifier=" + identifier + "&changed_value=" + changed_value + "&value_new=" + value_new,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Nichts wurde geändert.");
						}
					});
					
					//Delete subject
					$('.changeSubject_deleteButton').click(function(){
						var id = $(this).data('id')
						var subject_name = $(this).data('subject_name')
						
						var result = confirm('Änderungen bei Veranstaltung "' + subject_name + '" wirklich löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateLectureChanged_delete_submit.php",
								data: "id=" + id,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Veranstaltung wurde nicht gelöscht.");
						}
					});
				});
				</script>
				
				<!-- Added -->
				<?php
				$sql = "SELECT * FROM `ADDED_SUBJECTS`";
				$result = mysqli_query($con,$sql);
				?>
				
				<h4>
					<span>Hinzugekommene</span>
					<span style="margin-left:5px; margin-right:5px" class="badge badge-pill badge-primary"><?php echo mysqli_num_rows($result) ?></span>
					<a id="display_lectures_added"><span id="display_lectures_added_glyph" class="glyphicon glyphicon-chevron-down"></span></a>
				</h4>
				
				<table class="table table-striped table-bordered table-condensed update-table" id="table_lectures_added" style="margin-top:15px; display:none">
					<tr>
						<th>Name</th>
						<th>Kennung</th>
						<th>ECTS</th>
						<th>Semester</th>
						<th>Sprache</th>
						<th>Prüfungsart</th>
						<th>Voraussetzungen</th>
						<th>Bearbeiten</th>
						<th>Hinzufügen</th>
						<th>Löschen</th>
					</tr>
				
				<?php
				while($row = mysqli_fetch_assoc($result)){
					?>
					<tr>
						<td><?php echo $row['subject_name'] ?></td>
						<td><?php echo $row['identifier'] ?></td>
						<td><?php echo $row['ECTS'] ?></td>
						<td><?php echo $row['semester'] ?></td>
						<td><?php
							$language = $row['language'];
							$language = str_replace("nan", "k.A.", $language);
							echo $language
						?></td>
						<td><?php echo $row['exam_type'] ?></td>
						<td><?php echo $row['requirements'] ?></td>
						<td>
							<button type="button" class="btn btn-default" data-toggle="modal" data-target="#editAddedSubjectModal"
								data-id="<?php echo $row['ID'] ?>"
								data-subject_name="<?php echo $row['subject_name'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
								data-ects="<?php echo $row['ECTS'] ?>"
								data-semester="<?php echo $row['semester'] ?>"
								data-language="<?php echo $language ?>"
								data-exam_type="<?php echo $row['exam_type'] ?>"
								data-requirements="<?php echo $row['requirements'] ?>"
							>Bearbeiten</button>
						</td>
						<td>
							<button type="button" class="btn btn-primary addSubject_addButton"
								data-id="<?php echo $row['ID'] ?>"
								data-subject_name="<?php echo $row['subject_name'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
								data-ects="<?php echo $row['ECTS'] ?>"
								data-semester="<?php echo $row['semester'] ?>"
								data-language="<?php echo $language ?>"
								data-exam_type="<?php echo $row['exam_type'] ?>"
								data-requirements="<?php echo $row['requirements'] ?>"
								data-ilias="<?php echo $row['ilias'] ?>"
								data-modulebook="<?php echo $row['modulebook'] ?>"
							>Hinzufügen</button>
						</td>
						<td>
							<button type="button" class="btn btn-danger addSubject_deleteButton"
								data-id="<?php echo $row['ID'] ?>"
								data-subject_name="<?php echo $row['subject_name'] ?>"
							>Löschen</button>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
				
				<a href="#col_added_subjects" data-toggle="collapse">SQL-Befehle anzeigen / ausblenden</a>
				<div id="col_added_subjects" class="collapse">
					<p>
						Diese SQL-Befehle können kopiert und direkt in der Datenbank auf einmal ausgeführt werden. Das ist eher nicht empfohlen. Nach der Ausführung muss die jeweilige Tabelle manuell geleert werden.
					</p>
					<div class="update-collapsable">
						<?php
							$sql = "SELECT * FROM ADDED_SUBJECTS";
							$result = mysqli_query($con, $sql);

							while($row = mysqli_fetch_assoc($result)){
								$language = $row['language'];
								$language = str_replace("nan", "k.A.", $language);
								$requirements = $row['requirements'];
								$requirements = str_replace("nan", "", $requirements);
								$ilias = $row['ilias'];
								$ilias = str_replace("nan", "", $ilias);
								echo "
									INSERT INTO `subjects`(`subject_name`, `identifier`, `ECTS`, `semester`, `language`, `exam_type`, `requirements`, `ilias`, `modulebook`, `createdBy_ID`, `time_stamp`, `active`)
									VALUES ('".$row['subject_name']."', '".$row['identifier']."', '".$row['ECTS']."', '".$row['semester']."', '".$language."',  '".$row['exam_type']."', '".$requirements."', '".$ilias."', '".$row['modulebook']."',".$userRow['user_ID'].", now(), 1);
									<br>
								";
							}
						?>
					</div>
				</div>
				
				<div class="modal fade" id="editAddedSubjectModal" tabindex="-1" role="dialog" aria-labelledby="editAddedSubjectModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-body" id="addedSubject_edit_modal-body">
							<form id="addedSubject_edit_form">
								<div class="form-group" style="display:none">
									<label class="col-form-label">ID:</label>
									<input type="text" class="form-control" name ="id" id="id">
								</div>
								<div class="form-group">
									<label class="col-form-label">Name:</label>
									<input type="text" class="form-control" name="subject_name" id="subject_name">
								</div>
								<div class="form-group">
									<label class="col-form-label">Kennung:</label>
									<input type="text" class="form-control" name="identifier" id="identifier" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">ECTS:</label>
									<input type="text" class="form-control" name="ECTS" id="ECTS">
								</div>
								<div class="form-group">
									<label class="col-form-label">Semester:</label>
									<input type="text" class="form-control" name="semester" id="semester">
								</div>
								<div class="form-group">
									<label class="col-form-label">Sprache:</label>
									<input type="text" class="form-control" name="language" id="language">
								</div>
								<div class="form-group">
									<label class="col-form-label">Prüfungsart:</label>
									<input type="text" class="form-control" name="exam_type" id="exam_type">
								</div>
								<div class="form-group">
									<label class="col-form-label">Voraussetzungen:</label>
									<input type="text" class="form-control" name="requirements" id="requirements">
								</div>
							</form>
						</div>
						<div class="modal-footer" id="addedSubject_edit_modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
							<button type="button" class="btn btn-primary" id="lecture-added_save-changes-button">Änderungen speichern</button>
						</div>
					</div>
				  </div>
				</div>
				
				<script>
				$( document ).ready(function() {
					//Show and hide
					$("#display_lectures_added").click(function() {
						if($("#table_lectures_added").is(":visible")){
							$("#table_lectures_added").hide();
							$("#display_lectures_added_glyph").removeClass("glyphicon glyphicon-chevron-up");
							$("#display_lectures_added_glyph").addClass("glyphicon glyphicon-chevron-down");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=subject_added&value=0"});
						}else{
							$("#table_lectures_added").show();
							$("#display_lectures_added_glyph").removeClass("glyphicon glyphicon-chevron-down");
							$("#display_lectures_added_glyph").addClass("glyphicon glyphicon-chevron-up");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=subject_added&value=1"});
						}
						
					});
					
					//show modal
					$('#editAddedSubjectModal').on('show.bs.modal', function (event) {
						var button = $(event.relatedTarget) // Button that triggered the modal
						
						var id = button.data('id')
						var subject_name = button.data('subject_name')
						var identifier = button.data('identifier')
						var ECTS = button.data('ects')
						var semester = button.data('semester')
						var language = button.data('language')
						var exam_type = button.data('exam_type')
						var requirements = button.data('requirements')
						
						var modal = $(this)
						modal.find('.modal-body #id').val(id)
						modal.find('.modal-body #subject_name').val(subject_name)
						modal.find('.modal-body #identifier').val(identifier)
						modal.find('.modal-body #ECTS').val(ECTS)
						modal.find('.modal-body #semester').val(semester)
						modal.find('.modal-body #language').val(language)
						modal.find('.modal-body #exam_type').val(exam_type)
						modal.find('.modal-body #requirements').val(requirements)
					})
					
					//save changes
					$('#lecture-added_save-changes-button').click(function() {
						$.ajax({
							type: "POST",
							url: "admin_updateLectureAdded_edit_submit.php",
							data: $("#addedSubject_edit_form").serialize(),
							success: function(data) {
								//alert(data);
								if(data.includes("erfolg")){
									$('#addedSubject_edit_modal-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Änderungen erfolgreich gespeichert!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" onClick=\"window.location.reload()\">Schließen & Seite neu laden</button>");
								}else{
									$('#addedSubject_edit_modal-body').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Beim Speichern ist womöglich ein Fehler aufgetreten! Bitte probiere es erneut (oftmals liegt es am Server, sodass es beim zweiten Mal klappt).</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen & Seite neu laden</button>");
								}
								$('#addedSubject_edit_modal-footer').hide();
							}
						});
					});
					
					//Add subject
					$('.addSubject_addButton').click(function(){
						var id = $(this).data('id')
						var subject_name = $(this).data('subject_name')
						var identifier = $(this).data('identifier')
						var ECTS = $(this).data('ects')
						var semester = $(this).data('semester')
						var language = $(this).data('language')
						var exam_type = $(this).data('exam_type')
						var ilias = $(this).data('ilias')
						var modulebook = $(this).data('modulebook')
						
						var result = confirm('Veranstaltung "' + subject_name + '" wirklich hinzufügen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateLectureAdded_add_submit.php",
								data: "id=" + id + "&subject_name=" + subject_name + "&identifier=" + identifier + "&ECTS=" + ECTS + "&semester=" + semester + "&language=" + language + "&exam_type=" + exam_type + "&ilias=" + ilias + "&modulebook=" + modulebook,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Veranstaltung wurde nicht hinzugefügt.");
						}
					});
					
					//Delete subject
					$('.addSubject_deleteButton').click(function(){
						var id = $(this).data('id')
						var subject_name = $(this).data('subject_name')
						
						var result = confirm('Veranstaltung "' + subject_name + '" wirklich löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateLectureAdded_delete_submit.php",
								data: "id=" + id,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Veranstaltung wurde nicht gelöscht.");
						}
					});
				});
				</script>
				
				<!-- Deleted -->
				<?php
				$sql = "SELECT *, name AS subject_name FROM `DELETED_SUBJECTS`";
				$result = mysqli_query($con,$sql);
				?>
				
				<h4>
					<span>Gelöschte</span>
					<span style="margin-left:5px; margin-right:5px" class="badge badge-pill badge-primary"><?php echo mysqli_num_rows($result) ?></span>
					<a id="display_lectures_deleted"><span id="display_lectures_deleted_glyph" class="glyphicon glyphicon-chevron-down"></span></a>
				</h4>
				
				<table class="table table-striped table-bordered table-condensed update-table" id="table_lectures_deleted" style="margin-top:15px; display:none">
					<tr>
						<th>Name</th>
						<th>Kennung</th>
						<th>Aus Studi löschen</th>
						<th>Hier löschen</th>
					</tr>
				
				<?php
				while($row = mysqli_fetch_assoc($result)){
					?>
					<tr>
						<td><?php echo $row['subject_name'] ?></td>
						<td><?php echo $row['identifier'] ?></td>
						<td>
							<button type="button" class="btn btn-primary deleteSubject_deleteInStudiButton"
								data-id="<?php echo $row['id'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
								data-subject_name="<?php echo $row['subject_name'] ?>"
							>Aus Studi löschen</button>
						</td>
						<td>
							<button type="button" class="btn btn-danger deleteSubject_deleteButton"
								data-id="<?php echo $row['id'] ?>"
								data-subject_name="<?php echo $row['subject_name'] ?>"
							>Hier löschen</button>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
				
				<a href="#col_deleted_subjects" data-toggle="collapse">SQL-Befehle anzeigen / ausblenden</a>
				<div id="col_deleted_subjects" class="collapse">
					<p>
						Diese SQL-Befehle können kopiert und direkt in der Datenbank auf einmal ausgeführt werden. Das ist eher nicht empfohlen. Nach der Ausführung muss die jeweilige Tabelle manuell geleert werden.
					</p>
					<div class="update-collapsable">
						<?php
						$sql = "SELECT * FROM DELETED_SUBJECTS";
						$result = mysqli_query($con, $sql);

						while($row = mysqli_fetch_assoc($result)){
							echo "
								UPDATE `subjects` SET `active`= 0
								WHERE `identifier` = '".$row['identifier']."';
								<br>
							";
						}
						?>
					</div>
				</div>
				
				<script>
				$( document ).ready(function() {
					//Show and hide
					$("#display_lectures_deleted").click(function() {
						if($("#table_lectures_deleted").is(":visible")){
							$("#table_lectures_deleted").hide();
							$("#display_lectures_deleted_glyph").removeClass("glyphicon glyphicon-chevron-up");
							$("#display_lectures_deleted_glyph").addClass("glyphicon glyphicon-chevron-down");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=subject_deleted&value=0"});
						}else{
							$("#table_lectures_deleted").show();
							$("#display_lectures_deleted_glyph").removeClass("glyphicon glyphicon-chevron-down");
							$("#display_lectures_deleted_glyph").addClass("glyphicon glyphicon-chevron-up");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=subject_deleted&value=1"});
						}
						
					});
					
					//Delete subject in studi
					$('.deleteSubject_deleteInStudiButton').click(function(){
						var id = $(this).data('id')
						var identifier = $(this).data('identifier')
						var subject_name = $(this).data('subject_name')
						
						var result = confirm('Veranstaltung "' + subject_name + '" wirklich aus dem Studienführer löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateLectureDeleted_deleteFromStudi_submit.php",
								data: "id=" + id + "&identifier=" + identifier + "&subject_name=" + subject_name,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Nichts ist passiert.");
						}
					});
					
					//Delete subject
					$('.deleteSubject_deleteButton').click(function(){
						var id = $(this).data('id')
						var subject_name = $(this).data('subject_name')
						
						var result = confirm('Veranstaltung "' + subject_name + '" wirklich aus dieser Liste löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateLectureDeleted_delete_submit.php",
								data: "id=" + id,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Nichts ist passiert.");
						}
					});
				});
				</script>
			</div>	
			
			<br>
			
			<div class="grey-border">
				<h3>Module</h3>
				
				<!-- Changed -->
				<?php
				$sql = "
					SELECT modules.name AS name, CHANGED_MODULES.id AS id, CHANGED_MODULES.identifier AS identifier, CHANGED_MODULES.changed_value AS changed_value, CHANGED_MODULES.value_old AS value_old, CHANGED_MODULES.value_new AS value_new FROM `CHANGED_MODULES`
					JOIN modules ON CHANGED_MODULES.identifier = modules.code COLLATE utf8_unicode_ci
				";
				$result = mysqli_query($con,$sql);
				?>
				
				<h4>
					<span>Geänderte</span>
					<span style="margin-left:5px; margin-right:5px" class="badge badge-pill badge-primary"><?php echo mysqli_num_rows($result) ?></span>
					<a id="display_modules_changed"><span id="display_modules_changed_glyph" class="glyphicon glyphicon-chevron-down"></span></a>
				</h4>
				
				<table class="table table-striped table-bordered table-condensed update-table" id="table_modules_changed" style="margin-top:15px; display:none">
					<tr>
						<th>Name</th>
						<th>Kennung</th>
						<th>Geändertes Feld</th>
						<th>Alter Wert</th>
						<th>Neuer Wert</th>
						<th>Bearbeiten</th>
						<th>Ändern</th>
						<th>Löschen</th>
					</tr>
				
				<?php
				while($row = mysqli_fetch_assoc($result)){
					?>
					<tr>
						<td><?php echo $row['name'] ?></td>
						<td><?php echo $row['identifier'] ?></td>
						<td><?php echo $row['changed_value'] ?></td>
						<td><?php echo $row['value_old'] ?></td>
						<td><?php echo $row['value_new'] ?></td>
						<td>
							<button type="button" class="btn btn-default" data-toggle="modal" data-target="#editChangedModuleModal"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
								data-changed_value="<?php echo $row['changed_value'] ?>"
								data-value_old="<?php echo $row['value_old'] ?>"
								data-value_new="<?php echo $row['value_new'] ?>"
							>Bearbeiten</button>
						</td>
						<td>
							<button type="button" class="btn btn-primary changeModule_confirmButton"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
								data-changed_value="<?php echo $row['changed_value'] ?>"
								data-value_old="<?php echo $row['value_old'] ?>"
								data-value_new="<?php echo $row['value_new'] ?>"
							>Änderung bestätigen</button>
						</td>
						<td>
							<button type="button" class="btn btn-danger changeModule_deleteButton"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
							>Löschen</button>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
				
				<a href="#col_changed_modules" data-toggle="collapse">SQL-Befehle anzeigen / ausblenden</a>
				<div id="col_changed_modules" class="collapse">
					<p>
						Diese SQL-Befehle können kopiert und direkt in der Datenbank auf einmal ausgeführt werden. Das ist eher nicht empfohlen. Nach der Ausführung muss die jeweilige Tabelle manuell geleert werden.
					</p>
					<div class="update-collapsable">
						<?php
						$sql = "SELECT * FROM CHANGED_MODULES";
						$result = mysqli_query($con, $sql);

						while($row = mysqli_fetch_assoc($result)){
							echo "
								UPDATE `modules`
								SET `".$row['changed_value']."` = '".$row['value_new']."'
								WHERE `code` = '".$row['identifier']."';
								<br>
							";
						}
						?>
					</div>
				</div>
				
				<div class="modal fade" id="editChangedModuleModal" tabindex="-1" role="dialog" aria-labelledby="editChangedModuleModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-body" id="changedModule_edit_modal-body">
							<form id="changedModule_edit_form">
								<div class="form-group" style="display:none">
									<label class="col-form-label">ID:</label>
									<input type="text" class="form-control" name ="id" id="id">
								</div>
								<div class="form-group">
									<label class="col-form-label">Name:</label>
									<input type="text" class="form-control" name="name" id="name" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Kennung:</label>
									<input type="text" class="form-control" name="identifier" id="identifier" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Geändertes Feld:</label>
									<input type="text" class="form-control" name="changed_value" id="changed_value" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Alter Wert:</label>
									<input type="text" class="form-control" name="value_old" id="value_old" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Neuer Wert:</label>
									<input type="text" class="form-control" name="value_new" id="value_new">
								</div>
							</form>
						</div>
						<div class="modal-footer" id="changedModule_edit_modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
							<button type="button" class="btn btn-primary" id="module-changed_save-changes-button">Änderungen speichern</button>
						</div>
					</div>
				  </div>
				</div>
				
				<script>
				$( document ).ready(function() {
					//Show and hide
					$("#display_modules_changed").click(function() {
						if($("#table_modules_changed").is(":visible")){
							$("#table_modules_changed").hide();
							$("#display_modules_changed_glyph").removeClass("glyphicon glyphicon-chevron-up");
							$("#display_modules_changed_glyph").addClass("glyphicon glyphicon-chevron-down");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=module_changed&value=0"});
						}else{
							$("#table_modules_changed").show();
							$("#display_modules_changed_glyph").removeClass("glyphicon glyphicon-chevron-down");
							$("#display_modules_changed_glyph").addClass("glyphicon glyphicon-chevron-up");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=module_changed&value=1"});
						}
					});
					
					//show modal
					$('#editChangedModuleModal').on('show.bs.modal', function (event) {
						var button = $(event.relatedTarget) // Button that triggered the modal
						
						var id = button.data('id')
						var name = button.data('name')
						var identifier = button.data('identifier')
						var changed_value = button.data('changed_value')
						var value_old = button.data('value_old')
						var value_new = button.data('value_new')
						
						var modal = $(this)
						modal.find('.modal-body #id').val(id)
						modal.find('.modal-body #name').val(name)
						modal.find('.modal-body #identifier').val(identifier)
						modal.find('.modal-body #changed_value').val(changed_value)
						modal.find('.modal-body #value_old').val(value_old)
						modal.find('.modal-body #value_new').val(value_new)
					})
					
					//save changes
					$('#module-changed_save-changes-button').click(function() {
						$.ajax({
							type: "POST",
							url: "admin_updateModuleChanged_edit_submit.php",
							data: $("#changedModule_edit_form").serialize(),
							success: function(data) {
								//alert(data);
								if(data.includes("erfolg")){
									$('#changedModule_edit_modal-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Änderungen erfolgreich gespeichert!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" onClick=\"window.location.reload()\">Schließen & Seite neu laden</button>");
								}else{
									$('#changedModule_edit_modal-body').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Beim Speichern ist womöglich ein Fehler aufgetreten! Bitte probiere es erneut (oftmals liegt es am Server, sodass es beim zweiten Mal klappt).</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen & Seite neu laden</button>");
								}
								$('#changedModule_edit_modal-footer').hide();
							}
						});
					});
					
					//Change module
					$('.changeModule_confirmButton').click(function(){
						
						var id = $(this).data('id')
						var name = $(this).data('name')
						var identifier = $(this).data('identifier')
						var changed_value = $(this).data('changed_value')
						var value_old = $(this).data('value_old')
						var value_new = $(this).data('value_new')

						var result = confirm('Bei Modul "'+name+'" wirklich den Wert von '+changed_value+' von '+value_old+' zu '+value_new+' ändern?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateModuleChanged_confirm_submit.php",
								data: "id=" + id + "&identifier=" + identifier + "&changed_value=" + changed_value + "&value_new=" + value_new,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Nichts wurde geändert.");
						}
					});
					
					//Delete subject
					$('.changeModule_deleteButton').click(function(){
						var id = $(this).data('id')
						var name = $(this).data('name')
						
						var result = confirm('Änderungen bei Modul "' + name + '" wirklich löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateModuleChanged_delete_submit.php",
								data: "id=" + id,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Modul wurde nicht gelöscht.");
						}
					});
				});
				</script>
				
				<!-- Added -->
				<?php
				$sql = "SELECT * FROM `ADDED_MODULES`";
				$result = mysqli_query($con,$sql);
				?>
				
				<h4>
					<span>Hinzugekommene</span>
					<span style="margin-left:5px; margin-right:5px" class="badge badge-pill badge-primary"><?php echo mysqli_num_rows($result) ?></span>
					<a id="display_modules_added"><span id="display_modules_added_glyph" class="glyphicon glyphicon-chevron-down"></span></a>
				</h4>
				
				<table class="table table-striped table-bordered table-condensed update-table" id="table_modules_added" style="margin-top:15px; display:none">
					<tr>
						<th>Name</th>
						<th>Kennung</th>
						<th>Typ</th>
						<th>ECTS</th>
						<th>Voraussetzungen</th>
						<th>Bearbeiten</th>
						<th>Hinzufügen</th>
						<th>Löschen</th>
					</tr>
				
				<?php
				while($row = mysqli_fetch_assoc($result)){
					?>
					<tr>
						<td><?php echo $row['name'] ?></td>
						<td><?php echo $row['code'] ?></td>
						<td><?php echo $row['type'] ?></td>
						<td><?php echo $row['ects'] ?></td>
						<td><?php echo $row['requirements'] ?></td>
						<td>
							<button type="button" class="btn btn-default" data-toggle="modal" data-target="#editAddedModuleModal"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
								data-code="<?php echo $row['code'] ?>"
								data-type="<?php echo $row['type'] ?>"
								data-ects="<?php echo $row['ects'] ?>"
								data-requirements="<?php echo $row['requirements'] ?>"
							>Bearbeiten</button>
						</td>
						<td>
							<button type="button" class="btn btn-primary addModule_addButton"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
								data-code="<?php echo $row['code'] ?>"
								data-type="<?php echo $row['type'] ?>"
								data-ects="<?php echo $row['ects'] ?>"
								data-requirements="<?php echo $row['requirements'] ?>"
							>Hinzufügen</button>
						</td>
						<td>
							<button type="button" class="btn btn-danger addModule_deleteButton"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
							>Löschen</button>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
				
				<a href="#col_added_modules" data-toggle="collapse">SQL-Befehle anzeigen / ausblenden</a>
				<div id="col_added_modules" class="collapse">
					<p>
						Diese SQL-Befehle können kopiert und direkt in der Datenbank auf einmal ausgeführt werden. Das ist eher nicht empfohlen. Nach der Ausführung muss die jeweilige Tabelle manuell geleert werden.
					</p>
					<div class="update-collapsable">
						<?php
						$sql = "SELECT * FROM ADDED_MODULES";
						$result = mysqli_query($con, $sql);

						while($row = mysqli_fetch_assoc($result)){
							echo "
								INSERT INTO `modules`(`code`, `name`, `type`, `ects`, `requirements`, `user_ID`, `time_stamp`, `active`)
								VALUES ('".$row['code']."', '".$row['name']."', '".$row['type']."', '".$row['ects']."', '".$row['requirements']."', ".$userRow['user_ID'].", now(), 1);
								<br>
							";
						}
						?>
					</div>
				</div>
				
				<div class="modal fade" id="editAddedModuleModal" tabindex="-1" role="dialog" aria-labelledby="editAddedModuleModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-body" id="addedModule_edit_modal-body">
							<form id="addedModule_edit_form">
								<div class="form-group" style="display:none">
									<label class="col-form-label">id:</label>
									<input type="text" class="form-control" name ="id" id="id">
								</div>
								<div class="form-group">
									<label class="col-form-label">Name:</label>
									<input type="text" class="form-control" name="name" id="name">
								</div>
								<div class="form-group">
									<label class="col-form-label">Kennung:</label>
									<input type="text" class="form-control" name="code" id="code" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Typ:</label>
									<input type="text" class="form-control" name="type" id="type">
								</div>
								<div class="form-group">
									<label class="col-form-label">ECTS:</label>
									<input type="text" class="form-control" name="ects" id="ects">
								</div>
								<div class="form-group">
									<label class="col-form-label">Voraussetzungen:</label>
									<input type="text" class="form-control" name="requirements" id="requirements">
								</div>
							</form>
						</div>
						<div class="modal-footer" id="addedModule_edit_modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
							<button type="button" class="btn btn-primary" id="module-added_save-changes-button">Änderungen speichern</button>
						</div>
					</div>
				  </div>
				</div>
				
				<script>
				$( document ).ready(function() {
					//Show and hide
					$("#display_modules_added").click(function() {
						if($("#table_modules_added").is(":visible")){
							$("#table_modules_added").hide();
							$("#display_modules_added_glyph").removeClass("glyphicon glyphicon-chevron-up");
							$("#display_modules_added_glyph").addClass("glyphicon glyphicon-chevron-down");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=module_added&value=0"});
						}else{
							$("#table_modules_added").show();
							$("#display_modules_added_glyph").removeClass("glyphicon glyphicon-chevron-down");
							$("#display_modules_added_glyph").addClass("glyphicon glyphicon-chevron-up");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=module_added&value=1"});
						}
						
					});
					
					//show modal
					$('#editAddedModuleModal').on('show.bs.modal', function (event) {
						var button = $(event.relatedTarget) // Button that triggered the modal
						
						var id = button.data('id')
						var name = button.data('name')
						var code = button.data('code')
						var type = button.data('type')
						var ects = button.data('ects')
						var requirements = button.data('requirements')
						
						var modal = $(this)
						modal.find('.modal-body #id').val(id)
						modal.find('.modal-body #name').val(name)
						modal.find('.modal-body #code').val(code)
						modal.find('.modal-body #type').val(type)
						modal.find('.modal-body #ects').val(ects)
						modal.find('.modal-body #requirements').val(requirements)
					})
					
					//save changes
					$('#module-added_save-changes-button').click(function() {
						$.ajax({
							type: "POST",
							url: "admin_updateModuleAdded_edit_submit.php",
							data: $("#addedModule_edit_form").serialize(),
							success: function(data) {
								//alert(data);
								console.log(data);
								if(data.includes("erfolg")){
									$('#addedModule_edit_modal-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Änderungen erfolgreich gespeichert!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" onClick=\"window.location.reload()\">Schließen & Seite neu laden</button>");
								}else{
									$('#addedModule_edit_modal-body').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Beim Speichern ist womöglich ein Fehler aufgetreten! Bitte probiere es erneut (oftmals liegt es am Server, sodass es beim zweiten Mal klappt).</div><button type=\"button\" class=\"btn btn-primary\" onClick=\"window.location.reload()\" data-dismiss=\"modal\">Schließen & Seite neu laden</button>");
								}
								$('#addedModule_edit_modal-footer').hide();
							}
						});
					});
					
					//Add module
					$('.addModule_addButton').click(function(){
						var id = $(this).data('id')
						var name = $(this).data('name')
						var code = $(this).data('code')
						var type = $(this).data('type')
						var ects = $(this).data('ects')
						
						var result = confirm('Modul "' + name + '" wirklich hinzufügen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateModuleAdded_add_submit.php",
								data: "id=" + id + "&name=" + name + "&code=" + code + "&type=" + type + "&ects=" + ects,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Modul wurde nicht hinzugefügt.");
						}
					});
					
					//Delete subject
					$('.addModule_deleteButton').click(function(){
						var id = $(this).data('id')
						var name = $(this).data('name')
						
						var result = confirm('Modul "' + name + '" wirklich löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateModuleAdded_delete_submit.php",
								data: "id=" + id,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Veranstaltung wurde nicht gelöscht.");
						}
					});
				});
				</script>
				
				<!-- Deleted -->
				<?php
				$sql = "SELECT * FROM `DELETED_MODULES`";
				$result = mysqli_query($con,$sql);
				?>
				
				<h4>
					<span>Gelöschte</span>
					<span style="margin-left:5px; margin-right:5px" class="badge badge-pill badge-primary"><?php echo mysqli_num_rows($result) ?></span>
					<a id="display_modules_deleted"><span id="display_modules_deleted_glyph" class="glyphicon glyphicon-chevron-down"></span></a>
				</h4>
				
				<table class="table table-striped table-bordered table-condensed update-table" id="table_modules_deleted" style="margin-top:15px; display:none">
					<tr>
						<th>Name</th>
						<th>Kennung</th>
						<th>Aus Studi löschen</th>
						<th>Hier löschen</th>
					</tr>
				
				<?php
				while($row = mysqli_fetch_assoc($result)){
					?>
					<tr>
						<td><?php echo $row['name'] ?></td>
						<td><?php echo $row['identifier'] ?></td>
						<td>
							<button type="button" class="btn btn-primary deleteModule_deleteInStudiButton"
								data-id="<?php echo $row['id'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
								data-name="<?php echo $row['name'] ?>"
							>Aus Studi löschen</button>
						</td>
						<td>
							<button type="button" class="btn btn-danger deleteModule_deleteButton"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
							>Hier löschen</button>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
				
				<a href="#col_deleted_modules" data-toggle="collapse">SQL-Befehle anzeigen / ausblenden</a>
				<div id="col_deleted_modules" class="collapse">
					<p>
						Diese SQL-Befehle können kopiert und direkt in der Datenbank auf einmal ausgeführt werden. Das ist eher nicht empfohlen. Nach der Ausführung muss die jeweilige Tabelle manuell geleert werden.
					</p>
					<div class="update-collapsable">
						<?php
						$sql = "SELECT * FROM DELETED_MODULES";
						$result = mysqli_query($con, $sql);

						while($row = mysqli_fetch_assoc($result)){
							echo "
								UPDATE `modules` SET `active`= 0
								WHERE `code` = '".$row['identifier']."';
								<br>
							";
						}
						?>
					</div>
				</div>
				
				
				<script>
				$( document ).ready(function() {
					//Show and hide
					$("#display_modules_deleted").click(function() {
						if($("#table_modules_deleted").is(":visible")){
							$("#table_modules_deleted").hide();
							$("#display_modules_deleted_glyph").removeClass("glyphicon glyphicon-chevron-up");
							$("#display_modules_deleted_glyph").addClass("glyphicon glyphicon-chevron-down");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=module_deleted&value=0"});
						}else{
							$("#table_modules_deleted").show();
							$("#display_modules_deleted_glyph").removeClass("glyphicon glyphicon-chevron-down");
							$("#display_modules_deleted_glyph").addClass("glyphicon glyphicon-chevron-up");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=module_deleted&value=1"});
						}
						
					});
					
					//Delete subject in studi
					$('.deleteModule_deleteInStudiButton').click(function(){
						var id = $(this).data('id')
						var identifier = $(this).data('identifier')
						var name = $(this).data('name')
						
						var result = confirm('Modul "' + name + '" wirklich aus dem Studienführer löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateModuleDeleted_deleteFromStudi_submit.php",
								data: "id=" + id + "&identifier=" + identifier + "&name=" + name,
								success: function(data) {
									//console.log(data);
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Nichts ist passiert.");
						}
					});
					
					//Delete subject
					$('.deleteModule_deleteButton').click(function(){
						var id = $(this).data('id')
						var name = $(this).data('name')
						
						var result = confirm('Modul "' + name + '" wirklich aus dieser Liste löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateModuleDeleted_delete_submit.php",
								data: "id=" + id,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Nichts ist passiert.");
						}
					});
				});
				</script>
			</div>
			
			<br>
			
			<div class="grey-border">
			

				<h3>Dozenten</h3>
				
				<!-- Changed -->
				<?php
				$sql = "
					SELECT * FROM `CHANGED_LECTURERS`
				";
				$result = mysqli_query($con,$sql);
				?>
				
				<h4>
					<span>Geänderte</span>
					<span style="margin-left:5px; margin-right:5px" class="badge badge-pill badge-primary"><?php echo mysqli_num_rows($result) ?></span>
					<a id="display_lecturers_changed"><span id="display_lecturers_changed_glyph" class="glyphicon glyphicon-chevron-down"></span></a>
				</h4>
				
				<table class="table table-striped table-bordered table-condensed update-table" id="table_lecturers_changed" style="margin-top:15px; display:none">
					<tr>
						<th>Name</th>
						<th>Geändertes Feld</th>
						<th>Alter Wert</th>
						<th>Neuer Wert</th>
						<th>Ändern</th>
						<th>Löschen</th>
					</tr>
				
				<?php
				while($row = mysqli_fetch_assoc($result)){
					?>
					<tr>
						<td><?php echo $row['identifier'] ?></td>
						<td><?php echo $row['changed_value'] ?></td>
						<td><?php echo $row['value_old'] ?></td>
						<td><?php echo $row['value_new'] ?></td>
						<td>
							<button type="button" class="btn btn-primary changeLecturer_confirmButton"
								data-id="<?php echo $row['id'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
								data-changed_value="<?php echo $row['changed_value'] ?>"
								data-value_old="<?php echo $row['value_old'] ?>"
								data-value_new="<?php echo $row['value_new'] ?>"
							>Änderung bestätigen</button>
						</td>
						<td>
							<button type="button" class="btn btn-danger changeLecturer_deleteButton"
								data-id="<?php echo $row['id'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
							>Löschen</button>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
				
				<a href="#col_changed_lecturers" data-toggle="collapse">SQL-Befehle anzeigen / ausblenden</a>
				<div id="col_changed_lecturers" class="collapse">
					<p>
						Diese SQL-Befehle können kopiert und direkt in der Datenbank auf einmal ausgeführt werden. Das ist eher nicht empfohlen. Nach der Ausführung muss die jeweilige Tabelle manuell geleert werden.
					</p>
					<div class="update-collapsable">
						<?php
						$sql = "SELECT * FROM CHANGED_LECTURERS";
						$result = mysqli_query($con, $sql);

						while($row = mysqli_fetch_assoc($result)){
							echo "
								UPDATE `lecturers`
								SET `".$row['changed_value']."` = '".$row['value_new']."'
								WHERE `identifier` = '".$row['identifier']."';
								<br>
							";
						}
						?>
					</div>
				</div>
				
				<div class="modal fade" id="editChangedlecturerModal" tabindex="-1" role="dialog" aria-labelledby="editChangedlecturerModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-body" id="changedlecturer_edit_modal-body">
							<form id="changedlecturer_edit_form">
								<div class="form-group" style="display:none">
									<label class="col-form-label">ID:</label>
									<input type="text" class="form-control" name ="id" id="id">
								</div>
								<div class="form-group">
									<label class="col-form-label">Name:</label>
									<input type="text" class="form-control" name="identifier" id="identifier" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Geändertes Feld:</label>
									<input type="text" class="form-control" name="changed_value" id="changed_value" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Alter Wert:</label>
									<input type="text" class="form-control" name="value_old" id="value_old" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Neuer Wert:</label>
									<input type="text" class="form-control" name="value_new" id="value_new">
								</div>
							</form>
						</div>
						<div class="modal-footer" id="changedlecturer_edit_modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
							<button type="button" class="btn btn-primary" id="lecturer-changed_save-changes-button">Änderungen speichern</button>
						</div>
					</div>
				  </div>
				</div>
				
				<script>
				$( document ).ready(function() {
					//Show and hide
					$("#display_lecturers_changed").click(function() {
						if($("#table_lecturers_changed").is(":visible")){
							$("#table_lecturers_changed").hide();
							$("#display_lecturers_changed_glyph").removeClass("glyphicon glyphicon-chevron-up");
							$("#display_lecturers_changed_glyph").addClass("glyphicon glyphicon-chevron-down");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=institute_changed&value=0"});
						}else{
							$("#table_lecturers_changed").show();
							$("#display_lecturers_changed_glyph").removeClass("glyphicon glyphicon-chevron-down");
							$("#display_lecturers_changed_glyph").addClass("glyphicon glyphicon-chevron-up");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=institute_changed&value=1"});
						}
					});
					
					//Change module
					$('.changeLecturer_confirmButton').click(function(){
						
						var id = $(this).data('id')
						var identifier = $(this).data('identifier')
						var changed_value = $(this).data('changed_value')
						var value_old = $(this).data('value_old')
						var value_new = $(this).data('value_new')

						var result = confirm('Bei Dozent "'+identifier+'" wirklich den Wert von '+changed_value+' von '+value_old+' zu '+value_new+' ändern?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateLecturerChanged_confirm_submit.php",
								data: "id=" + id + "&identifier=" + identifier + "&changed_value=" + changed_value + "&value_new=" + value_new,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Nichts wurde geändert.");
						}
					});
					
					//Delete subject
					$('.changeLecturer_deleteButton').click(function(){
						var id = $(this).data('id')
						var identifier = $(this).data('identifier')
						
						var result = confirm('Änderungen bei Dozent "' + identifier + '" wirklich löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateLecturerChanged_delete_submit.php",
								data: "id=" + id,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Dozent wurde nicht gelöscht.");
						}
					});
				});
				</script>
					
				<!-- Added lecturers-->
				<?php
				$sql = "SELECT * FROM `ADDED_LECTURERS`";
				$result = mysqli_query($con,$sql);
				?>
				
				<h4>
					<span>Hinzugekommene</span>
					<span style="margin-left:5px; margin-right:5px" class="badge badge-pill badge-primary"><?php echo mysqli_num_rows($result) ?></span>
					<a id="display_lecturers_added"><span id="display_lecturers_added_glyph" class="glyphicon glyphicon-chevron-down"></span></a>
				</h4>
				
				<table class="table table-striped table-bordered table-condensed update-table" id="table_lecturers_added" style="margin-top:15px; display:none">
					<tr>
						<th>Name</th>
						<th>Hinzufügen</th>
						<th>Löschen</th>
					</tr>
				
				<?php
				while($row = mysqli_fetch_assoc($result)){
					?>
					<tr>
						<td><?php echo $row['name'] ?></td>
						<td>
							<button type="button" class="btn btn-primary addLecturer_addButton"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
							>Hinzufügen</button>
						</td>
						<td>
							<button type="button" class="btn btn-danger addLecturer_deleteButton"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
							>Löschen</button>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
				
				<a href="#col_added_lecturers" data-toggle="collapse">SQL-Befehle anzeigen / ausblenden</a>
				<div id="col_added_lecturers" class="collapse">
					<p>
						Diese SQL-Befehle können kopiert und direkt in der Datenbank auf einmal ausgeführt werden. Das ist eher nicht empfohlen. Nach der Ausführung muss die jeweilige Tabelle manuell geleert werden.
					</p>
					<div class="update-collapsable">
						<?php
						$sql = "SELECT * FROM ADDED_LECTURERS";
						$result = mysqli_query($con, $sql);

						while($row = mysqli_fetch_assoc($result)){
							echo "
								INSERT INTO `lecturers`(`name`, `user_ID`, `time_stamp`, `active`)
								VALUES ('".$row['name']."', ".$userRow['user_ID'].", now(), 1);
								<br>
							";
						}
						?>
					</div>
				</div>
				
				<script>
				$( document ).ready(function() {
					//Show and hide
					$("#display_lecturers_added").click(function() {
						if($("#table_lecturers_added").is(":visible")){
							$("#table_lecturers_added").hide();
							$("#display_lecturers_added_glyph").removeClass("glyphicon glyphicon-chevron-up");
							$("#display_lecturers_added_glyph").addClass("glyphicon glyphicon-chevron-down");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=lecturer_added&value=0"});
						}else{
							$("#table_lecturers_added").show();
							$("#display_lecturers_added_glyph").removeClass("glyphicon glyphicon-chevron-down");
							$("#display_lecturers_added_glyph").addClass("glyphicon glyphicon-chevron-up");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=lecturer_added&value=1"});
						}
						
					});
					
					//Add lecturer
					$('.addLecturer_addButton').click(function(){
						var id = $(this).data('id')
						var name = $(this).data('name')
						
						var result = confirm('Dozent "' + name + '" wirklich hinzufügen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateLecturerAdded_add_submit.php",
								data: "id=" + id + "&name=" + name,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Dozent wurde nicht hinzugefügt.");
						}
					});
					
					//Delete lecturer
					$('.addLecturer_deleteButton').click(function(){
						var id = $(this).data('id')
						var name = $(this).data('name')
						
						var result = confirm('Dozent "' + name + '" wirklich löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateLecturerAdded_delete_submit.php",
								data: "id=" + id,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Dozent wurde nicht gelöscht.");
						}
					});
				});
				</script>
				
				<!-- Deleted -->
				<?php
				$sql = "SELECT * FROM `DELETED_LECTURERS`";
				$result = mysqli_query($con,$sql);
				?>
				
				<h4>
					<span>Gelöschte</span>
					<span style="margin-left:5px; margin-right:5px" class="badge badge-pill badge-primary"><?php echo mysqli_num_rows($result) ?></span>
					<a id="display_lecturers_deleted"><span id="display_lecturers_deleted_glyph" class="glyphicon glyphicon-chevron-down"></span></a>
				</h4>
				
				<table class="table table-striped table-bordered table-condensed update-table" id="table_lecturers_deleted" style="margin-top:15px; display:none">
					<tr>
						<th>Name</th>
						<th>Aus Studi löschen</th>
						<th>Hier löschen</th>
					</tr>
				
				<?php
				while($row = mysqli_fetch_assoc($result)){
					?>
					<tr>
						<td><?php echo $row['name'] ?></td>
						<td>
							<button type="button" class="btn btn-primary deleteLecturer_deleteInStudiButton"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
							>Aus Studi löschen</button>
						</td>
						<td>
							<button type="button" class="btn btn-danger deleteLecturer_deleteButton"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
							>Hier löschen</button>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
				
				<a href="#col_deleted_lecturers" data-toggle="collapse">SQL-Befehle anzeigen / ausblenden</a>
				<div id="col_deleted_lecturers" class="collapse">
					<p>
						Diese SQL-Befehle können kopiert und direkt in der Datenbank auf einmal ausgeführt werden. Das ist eher nicht empfohlen. Nach der Ausführung muss die jeweilige Tabelle manuell geleert werden.
					</p>
					<div class="update-collapsable">
						<?php
						$sql = "SELECT * FROM DELETED_LECTURERS";
						$result = mysqli_query($con, $sql);

						while($row = mysqli_fetch_assoc($result)){
							echo "
								UPDATE `lecturers` SET `active`= 0
								WHERE `name` = '".$row['identifier']."';
								<br>
							";
						}
						?>
					</div>
				</div>
				
				<script>
				$( document ).ready(function() {
					//Show and hide
					$("#display_lecturers_deleted").click(function() {
						if($("#table_lecturers_deleted").is(":visible")){
							$("#table_lecturers_deleted").hide();
							$("#display_lecturers_deleted_glyph").removeClass("glyphicon glyphicon-chevron-up");
							$("#display_lecturers_deleted_glyph").addClass("glyphicon glyphicon-chevron-down");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=lecturer_deleted&value=0"});
						}else{
							$("#table_lecturers_deleted").show();
							$("#display_lecturers_deleted_glyph").removeClass("glyphicon glyphicon-chevron-down");
							$("#display_lecturers_deleted_glyph").addClass("glyphicon glyphicon-chevron-up");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=lecturer_deleted&value=1"});
						}
						
					});
					
					//Delete subject in studi
					$('.deleteLecturer_deleteInStudiButton').click(function(){
						var id = $(this).data('id')
						var name = $(this).data('name')
						
						var result = confirm('Dozent "' + name + '" wirklich aus dem Studienführer löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateLecturerDeleted_deleteFromStudi_submit.php",
								data: "id=" + id + "&name=" + name,
								success: function(data) {
									//console.log(data);
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Nichts ist passiert.");
						}
					});
					
					//Delete subject
					$('.deleteLecturer_deleteButton').click(function(){
						var id = $(this).data('id')
						var name = $(this).data('name')
						
						var result = confirm('Dozent "' + name + '" wirklich aus dieser Liste löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateLecturerDeleted_delete_submit.php",
								data: "id=" + id,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Nichts ist passiert.");
						}
					});
				});
				</script>
			</div>
			
			<br>
			
			<div class="grey-border">
				<h3>Institute</h3>
				
				<!-- Changed -->
				<?php
				$sql = "
					SELECT * FROM `CHANGED_INSTITUTES`
				";
				$result = mysqli_query($con,$sql);
				?>
				
				<h4>
					<span>Geänderte</span>
					<span style="margin-left:5px; margin-right:5px" class="badge badge-pill badge-primary"><?php echo mysqli_num_rows($result) ?></span>
					<a id="display_institutes_changed"><span id="display_institutes_changed_glyph" class="glyphicon glyphicon-chevron-down"></span></a>
				</h4>
				
				<table class="table table-striped table-bordered table-condensed update-table" id="table_institutes_changed" style="margin-top:15px; display:none">
					<tr>
						<th>Name</th>
						<th>Geändertes Feld</th>
						<th>Alter Wert</th>
						<th>Neuer Wert</th>
						<th>Bearbeiten</th>
						<th>Ändern</th>
						<th>Löschen</th>
					</tr>
				
				<?php
				while($row = mysqli_fetch_assoc($result)){
					?>
					<tr>
						<td><?php echo $row['identifier'] ?></td>
						<td><?php echo $row['changed_value'] ?></td>
						<td><?php echo $row['value_old'] ?></td>
						<td><?php echo $row['value_new'] ?></td>
						<td>
							<button type="button" class="btn btn-default" data-toggle="modal" data-target="#editChangedInstituteModal"
								data-id="<?php echo $row['id'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
								data-changed_value="<?php echo $row['changed_value'] ?>"
								data-value_old="<?php echo $row['value_old'] ?>"
								data-value_new="<?php echo $row['value_new'] ?>"
							>Bearbeiten</button>
						</td>
						<td>
							<button type="button" class="btn btn-primary changeInstitute_confirmButton"
								data-id="<?php echo $row['id'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
								data-changed_value="<?php echo $row['changed_value'] ?>"
								data-value_old="<?php echo $row['value_old'] ?>"
								data-value_new="<?php echo $row['value_new'] ?>"
							>Änderung bestätigen</button>
						</td>
						<td>
							<button type="button" class="btn btn-danger changeInstitute_deleteButton"
								data-id="<?php echo $row['id'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
							>Löschen</button>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
				
				<a href="#col_changed_institutes" data-toggle="collapse">SQL-Befehle anzeigen / ausblenden</a>
				<div id="col_changed_institutes" class="collapse">
					<p>
						Diese SQL-Befehle können kopiert und direkt in der Datenbank auf einmal ausgeführt werden. Das ist eher nicht empfohlen. Nach der Ausführung muss die jeweilige Tabelle manuell geleert werden.
					</p>
					<div class="update-collapsable">
						<?php
						$sql = "SELECT * FROM CHANGED_INSTITUTES";
						$result = mysqli_query($con, $sql);

						while($row = mysqli_fetch_assoc($result)){
							echo "
								UPDATE `institutes`
								SET `".$row['changed_value']."` = '".$row['value_new']."'
								WHERE `abbr` = '".$row['identifier']."';
								<br>
							";
						}
						?>
					</div>
				</div>
				
				<div class="modal fade" id="editChangedInstituteModal" tabindex="-1" role="dialog" aria-labelledby="editChangedInstituteModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-body" id="changedInstitute_edit_modal-body">
							<form id="changedInstitute_edit_form">
								<div class="form-group" style="display:none">
									<label class="col-form-label">ID:</label>
									<input type="text" class="form-control" name ="id" id="id">
								</div>
								<div class="form-group">
									<label class="col-form-label">Name:</label>
									<input type="text" class="form-control" name="identifier" id="identifier" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Geändertes Feld:</label>
									<input type="text" class="form-control" name="changed_value" id="changed_value" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Alter Wert:</label>
									<input type="text" class="form-control" name="value_old" id="value_old" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Neuer Wert:</label>
									<input type="text" class="form-control" name="value_new" id="value_new">
								</div>
							</form>
						</div>
						<div class="modal-footer" id="changedInstitute_edit_modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
							<button type="button" class="btn btn-primary" id="institute-changed_save-changes-button">Änderungen speichern</button>
						</div>
					</div>
				  </div>
				</div>
				
				<script>
				$( document ).ready(function() {
					//Show and hide
					$("#display_institutes_changed").click(function() {
						if($("#table_institutes_changed").is(":visible")){
							$("#table_institutes_changed").hide();
							$("#display_institutes_changed_glyph").removeClass("glyphicon glyphicon-chevron-up");
							$("#display_institutes_changed_glyph").addClass("glyphicon glyphicon-chevron-down");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=institute_changed&value=0"});
						}else{
							$("#table_institutes_changed").show();
							$("#display_institutes_changed_glyph").removeClass("glyphicon glyphicon-chevron-down");
							$("#display_institutes_changed_glyph").addClass("glyphicon glyphicon-chevron-up");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=institute_changed&value=1"});
						}
					});
					
					//show modal
					$('#editChangedInstituteModal').on('show.bs.modal', function (event) {
						var button = $(event.relatedTarget) // Button that triggered the modal
						
						var id = button.data('id')
						var identifier = button.data('identifier')
						var changed_value = button.data('changed_value')
						var value_old = button.data('value_old')
						var value_new = button.data('value_new')
						
						var modal = $(this)
						modal.find('.modal-body #id').val(id)
						modal.find('.modal-body #identifier').val(identifier)
						modal.find('.modal-body #changed_value').val(changed_value)
						modal.find('.modal-body #value_old').val(value_old)
						modal.find('.modal-body #value_new').val(value_new)
					})
					
					//save changes
					$('#institute-changed_save-changes-button').click(function() {
						$.ajax({
							type: "POST",
							url: "admin_updateInstituteChanged_edit_submit.php",
							data: $("#changedInstitute_edit_form").serialize(),
							success: function(data) {
								//alert(data);
								if(data.includes("erfolg")){
									$('#changedInstitute_edit_modal-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Änderungen erfolgreich gespeichert!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" onClick=\"window.location.reload()\">Schließen & Seite neu laden</button>");
								}else{
									$('#changedInstitute_edit_modal-body').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Beim Speichern ist womöglich ein Fehler aufgetreten! Bitte probiere es erneut (oftmals liegt es am Server, sodass es beim zweiten Mal klappt).</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen & Seite neu laden</button>");
								}
								$('#changedInstitute_edit_modal-footer').hide();
							}
						});
					});
					
					//Change module
					$('.changeInstitute_confirmButton').click(function(){
						
						var id = $(this).data('id')
						var identifier = $(this).data('identifier')
						var changed_value = $(this).data('changed_value')
						var value_old = $(this).data('value_old')
						var value_new = $(this).data('value_new')

						var result = confirm('Bei Institut "'+identifier+'" wirklich den Wert von '+changed_value+' von '+value_old+' zu '+value_new+' ändern?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateInstituteChanged_confirm_submit.php",
								data: "id=" + id + "&identifier=" + identifier + "&changed_value=" + changed_value + "&value_new=" + value_new,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Nichts wurde geändert.");
						}
					});
					
					//Delete subject
					$('.changeInstitute_deleteButton').click(function(){
						var id = $(this).data('id')
						var identifier = $(this).data('identifier')
						
						var result = confirm('Änderungen bei Institut "' + identifier + '" wirklich löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateInstituteChanged_delete_submit.php",
								data: "id=" + id,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Institut wurde nicht gelöscht.");
						}
					});
				});
				</script>
				
				<!--Added-->
				<?php
				$sql = "SELECT * FROM `ADDED_INSTITUTES`";
				$result = mysqli_query($con,$sql);
				?>
				
				<h4>
					<span>Hinzugekommene</span>
					<span style="margin-left:5px; margin-right:5px" class="badge badge-pill badge-primary"><?php echo mysqli_num_rows($result) ?></span>
					<a id="display_institutes_added"><span id="display_institutes_added_glyph" class="glyphicon glyphicon-chevron-down"></span></a>
				</h4>
				
				<table class="table table-striped table-bordered table-condensed update-table" id="table_institutes_added" style="margin-top:15px; display:none">
					<tr>
						<th>Name</th>
						<th>Abkürzung</th>
						<th>Hinzufügen</th>
						<th>Löschen</th>
					</tr>
				
				<?php
				while($row = mysqli_fetch_assoc($result)){
					?>
					<tr>
						<td><?php echo $row['name'] ?></td>
						<td><?php echo $row['abbr'] ?></td>
						<td>
							<button type="button" class="btn btn-primary addInstitute_addButton"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
								data-abbr="<?php echo $row['abbr'] ?>"
							>Hinzufügen</button>
						</td>
						<td>
							<button type="button" class="btn btn-danger addInstitute_deleteButton"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
							>Löschen</button>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
				
				<a href="#col_added_institutes" data-toggle="collapse">SQL-Befehle anzeigen / ausblenden</a>
				<div id="col_added_institutes" class="collapse">
					<p>
						Diese SQL-Befehle können kopiert und direkt in der Datenbank auf einmal ausgeführt werden. Das ist eher nicht empfohlen. Nach der Ausführung muss die jeweilige Tabelle manuell geleert werden.
					</p>
					<div class="update-collapsable">
						<?php
						$sql = "SELECT * FROM ADDED_INSTITUTES";
						$result = mysqli_query($con, $sql);

						while($row = mysqli_fetch_assoc($result)){
							echo "
								INSERT INTO `institutes`(`name`, `abbr`, `user_ID`, `time_stamp`, `active`)
								VALUES ('".$row['name']."', '".$row['abbr']."', ".$userRow['user_ID'].", now(), 1);
								<br>
							";
						}
						?>
					</div>
				</div>
				
				<script>
				$( document ).ready(function() {
					//Show and hide
					$("#display_institutes_added").click(function() {
						if($("#table_institutes_added").is(":visible")){
							$("#table_institutes_added").hide();
							$("#display_institutes_added_glyph").removeClass("glyphicon glyphicon-chevron-up");
							$("#display_institutes_added_glyph").addClass("glyphicon glyphicon-chevron-down");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=institute_added&value=0"});
						}else{
							$("#table_institutes_added").show();
							$("#display_institutes_added_glyph").removeClass("glyphicon glyphicon-chevron-down");
							$("#display_institutes_added_glyph").addClass("glyphicon glyphicon-chevron-up");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=institute_added&value=1"});
						}
						
					});
					
					//Add institute
					$('.addInstitute_addButton').click(function(){
						var id = $(this).data('id')
						var name = $(this).data('name')
						var abbr = $(this).data('abbr')
						
						var result = confirm('Institut "' + name + '" wirklich hinzufügen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateInstituteAdded_add_submit.php",
								data: "id=" + id + "&name=" + name + "&abbr=" + abbr,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Institut wurde nicht hinzugefügt.");
						}
					});
					
					//Delete institute
					$('.addInstitute_deleteButton').click(function(){
						var id = $(this).data('id')
						var name = $(this).data('name')
						
						var result = confirm('Institut "' + name + '" wirklich löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateInstituteAdded_delete_submit.php",
								data: "id=" + id,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Institut wurde nicht gelöscht.");
						}
					});
				});
				</script>
				
				<!-- Deleted -->
				<?php
				$sql = "SELECT * FROM `DELETED_INSTITUTES`";
				$result = mysqli_query($con,$sql);
				?>
				
				<h4>
					<span>Gelöschte</span>
					<span style="margin-left:5px; margin-right:5px" class="badge badge-pill badge-primary"><?php echo mysqli_num_rows($result) ?></span>
					<a id="display_institutes_deleted"><span id="display_institutes_deleted_glyph" class="glyphicon glyphicon-chevron-down"></span></a>
				</h4>
				
				<table class="table table-striped table-bordered table-condensed update-table" id="table_institutes_deleted" style="margin-top:15px; display:none">
					<tr>
						<th>Name</th>
						<th>Aus Studi löschen</th>
						<th>Hier löschen</th>
					</tr>
				
				<?php
				while($row = mysqli_fetch_assoc($result)){
					?>
					<tr>
						<td><?php echo $row['name'] ?></td>
						<td>
							<button type="button" class="btn btn-primary deleteInstitute_deleteInStudiButton"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
							>Aus Studi löschen</button>
						</td>
						<td>
							<button type="button" class="btn btn-danger deleteInstitute_deleteButton"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
							>Hier löschen</button>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
				
				<a href="#col_deleted_institutes" data-toggle="collapse">SQL-Befehle anzeigen / ausblenden</a>
				<div id="col_deleted_institutes" class="collapse">
					<p>
						Diese SQL-Befehle können kopiert und direkt in der Datenbank auf einmal ausgeführt werden. Das ist eher nicht empfohlen. Nach der Ausführung muss die jeweilige Tabelle manuell geleert werden.
					</p>
					<div class="update-collapsable">
						<?php
						$sql = "SELECT * FROM DELETED_INSTITUTES";
						$result = mysqli_query($con, $sql);

						while($row = mysqli_fetch_assoc($result)){
							echo "
								UPDATE `institutes` SET `active`= 0
								WHERE `abbr` = '".$row['identifier']."';
								<br>
							";
						}
						?>
					</div>
				</div>
				
				<script>
				$( document ).ready(function() {
					//Show and hide
					$("#display_institutes_deleted").click(function() {
						if($("#table_institutes_deleted").is(":visible")){
							$("#table_institutes_deleted").hide();
							$("#display_institutes_deleted_glyph").removeClass("glyphicon glyphicon-chevron-up");
							$("#display_institutes_deleted_glyph").addClass("glyphicon glyphicon-chevron-down");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=institute_deleted&value=0"});
						}else{
							$("#table_institutes_deleted").show();
							$("#display_institutes_deleted_glyph").removeClass("glyphicon glyphicon-chevron-down");
							$("#display_institutes_deleted_glyph").addClass("glyphicon glyphicon-chevron-up");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=institute_deleted&value=1"});
						}
						
					});
					
					//Delete subject in studi
					$('.deleteInstitute_deleteInStudiButton').click(function(){
						var id = $(this).data('id')
						var name = $(this).data('name')
						
						var result = confirm('Institut "' + name + '" wirklich aus dem Studienführer löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateInstituteDeleted_deleteFromStudi_submit.php",
								data: "id=" + id + "&name=" + name,
								success: function(data) {
									//console.log(data);
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Nichts ist passiert.");
						}
					});
					
					//Delete subject
					$('.deleteInstitute_deleteButton').click(function(){
						var id = $(this).data('id')
						var name = $(this).data('name')
						
						var result = confirm('Dozent "' + name + '" wirklich aus dieser Liste löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateInstituteDeleted_delete_submit.php",
								data: "id=" + id,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Nichts ist passiert.");
						}
					});
				});
				</script>
			</div>
				
			<!-- Set table visabilities -->
			<!-- subject_changed -->
			<?php
			$sql = "SELECT * FROM `admin_update_settings` WHERE name = 'subject_changed'";
			$result = mysqli_query($con, $sql);
			$row = mysqli_fetch_array($result);
			$subject_changed = $row['value'];
			
			$sql = "SELECT * FROM `admin_update_settings` WHERE name = 'subject_added'";
			$result = mysqli_query($con, $sql);
			$row = mysqli_fetch_array($result);
			$subject_added = $row['value'];
			
			$sql = "SELECT * FROM `admin_update_settings` WHERE name = 'subject_deleted'";
			$result = mysqli_query($con, $sql);
			$row = mysqli_fetch_array($result);
			$subject_deleted = $row['value'];
			
			$sql = "SELECT * FROM `admin_update_settings` WHERE name = 'module_changed'";
			$result = mysqli_query($con, $sql);
			$row = mysqli_fetch_array($result);
			$module_changed = $row['value'];
			
			$sql = "SELECT * FROM `admin_update_settings` WHERE name = 'module_added'";
			$result = mysqli_query($con, $sql);
			$row = mysqli_fetch_array($result);
			$module_added = $row['value'];
			
			$sql = "SELECT * FROM `admin_update_settings` WHERE name = 'module_deleted'";
			$result = mysqli_query($con, $sql);
			$row = mysqli_fetch_array($result);
			$module_deleted = $row['value'];
			
			$sql = "SELECT * FROM `admin_update_settings` WHERE name = 'lecturer_changed'";
			$result = mysqli_query($con, $sql);
			$row = mysqli_fetch_array($result);
			$lecturer_changed = $row['value'];
			
			$sql = "SELECT * FROM `admin_update_settings` WHERE name = 'lecturer_added'";
			$result = mysqli_query($con, $sql);
			$row = mysqli_fetch_array($result);
			$lecturer_added = $row['value'];
			
			$sql = "SELECT * FROM `admin_update_settings` WHERE name = 'lecturer_deleted'";
			$result = mysqli_query($con, $sql);
			$row = mysqli_fetch_array($result);
			$lecturer_deleted = $row['value'];
			
			$sql = "SELECT * FROM `admin_update_settings` WHERE name = 'institute_changed'";
			$result = mysqli_query($con, $sql);
			$row = mysqli_fetch_array($result);
			$institute_changed = $row['value'];
			
			$sql = "SELECT * FROM `admin_update_settings` WHERE name = 'institute_added'";
			$result = mysqli_query($con, $sql);
			$row = mysqli_fetch_array($result);
			$institute_added = $row['value'];
			
			$sql = "SELECT * FROM `admin_update_settings` WHERE name = 'institute_deleted'";
			$result = mysqli_query($con, $sql);
			$row = mysqli_fetch_array($result);
			$institute_deleted = $row['value'];
			?>
			<script>
			$( document ).ready(function() {
				$('#close-all').click(function(){
					$.ajax({type: "POST", url: "admin_update_closeAll.php", success: function(){window.location.reload(true)}});
				});
				
				if (<?php echo $subject_changed ?> == 1){
					$('#table_lectures_changed').show();
					$("#display_lectures_changed_glyph").removeClass("glyphicon glyphicon-chevron-down");
					$("#display_lectures_changed_glyph").addClass("glyphicon glyphicon-chevron-up");
				}
				
				if (<?php echo $subject_added ?> == 1){
					$('#table_lectures_added').show();
					$("#display_lectures_added_glyph").removeClass("glyphicon glyphicon-chevron-down");
					$("#display_lectures_added_glyph").addClass("glyphicon glyphicon-chevron-up");
				}
				
				if (<?php echo $subject_deleted ?> == 1){
					$('#table_lectures_deleted').show();
					$("#display_lectures_deleted_glyph").removeClass("glyphicon glyphicon-chevron-down");
					$("#display_lectures_deleted_glyph").addClass("glyphicon glyphicon-chevron-up");
				}
				
				if (<?php echo $module_changed ?> == 1){
					$('#table_modules_changed').show();
					$("#display_modules_changed_glyph").removeClass("glyphicon glyphicon-chevron-down");
					$("#display_modules_changed_glyph").addClass("glyphicon glyphicon-chevron-up");
				}
				
				if (<?php echo $module_added ?> == 1){
					$('#table_modules_added').show();
					$("#display_modules_added_glyph").removeClass("glyphicon glyphicon-chevron-down");
					$("#display_modules_added_glyph").addClass("glyphicon glyphicon-chevron-up");
				}
				
				if (<?php echo $module_deleted ?> == 1){
					$('#table_modules_deleted').show();
					$("#display_modules_deleted_glyph").removeClass("glyphicon glyphicon-chevron-down");
					$("#display_modules_deleted_glyph").addClass("glyphicon glyphicon-chevron-up");
				}
				
				if (<?php echo $lecturer_changed ?> == 1){
					$('#table_lecturers_changed').show();
					$("#display_lecturers_changed_glyph").removeClass("glyphicon glyphicon-chevron-down");
					$("#display_lecturers_changed_glyph").addClass("glyphicon glyphicon-chevron-up");
				}
				
				if (<?php echo $lecturer_added ?> == 1){
					$('#table_lecturers_added').show();
					$("#display_lecturers_added_glyph").removeClass("glyphicon glyphicon-chevron-down");
					$("#display_lecturers_added_glyph").addClass("glyphicon glyphicon-chevron-up");
				}
				
				if (<?php echo $lecturer_deleted ?> == 1){
					$('#table_lecturers_deleted').show();
					$("#display_lecturers_deleted_glyph").removeClass("glyphicon glyphicon-chevron-down");
					$("#display_lecturers_deleted_glyph").addClass("glyphicon glyphicon-chevron-up");
				}
				
				if (<?php echo $institute_changed ?> == 1){
					$('#table_institutes_changed').show();
					$("#display_institutes_changed_glyph").removeClass("glyphicon glyphicon-chevron-down");
					$("#display_institutes_changed_glyph").addClass("glyphicon glyphicon-chevron-up");
				}
				
				if (<?php echo $institute_added ?> == 1){
					$('#table_institutes_added').show();
					$("#display_institutes_added_glyph").removeClass("glyphicon glyphicon-chevron-down");
					$("#display_institutes_added_glyph").addClass("glyphicon glyphicon-chevron-up");
				}
				
				if (<?php echo $institute_deleted ?> == 1){
					$('#table_institutes_deleted').show();
					$("#display_institutes_deleted_glyph").removeClass("glyphicon glyphicon-chevron-down");
					$("#display_institutes_deleted_glyph").addClass("glyphicon glyphicon-chevron-up");
				}
			});
			</script>
			
			
			<h2>Schritt 5: Tabellen downloaden (Neue Entitäten, Matching)</h2>
			<p><i>Tabellen herunterladen und damit die Python-Skripe ausführen.</i></p>
			<?php

			$tables = array("subjects", "lecturers", "institutes", "modules", "lecturers_institutes", "modules_levels", "subjects_lecturers", "subjects_modules");

			foreach ($tables as $table) {
				?>
				<button onclick="Export('<?php echo $table ?>')" class="btn btn-primary" style="margin:5px"><?php echo $table ?></button>
				<?php
			}
			?>
			
			<script>
			function Export(table){
				window.open("export.php?table_name="+table, '_blank');
			}
			</script>
			
			<h2>Schritt 6: Änderungstabellen hochladen (Matching)</h2>
			<p><i>Die Ausgaben der Python-Skripte hochladen. Dateien nicht umbenennen. Dateien, die vor mehr als 3 Tagen hochgeladen wurden, werden automatisch gelöscht.</i></p>
			<div class="grey-border">
				<input class="fileToUpload" type="file" name="fileToUpload" style="margin-top:10px; margin-bottom:10px;"/>
				<button class="btn btn-primary uploadButton">Hochladen</button>
				<br>
				<hr>
				<p>Diese (relevanten) Dateien befinden sich derzeit auf dem Server:</p>
				<?php
				$files = glob("uploads/*");
				$validNames = array(
					"LECTURERS_INSTITUTES.txt", "MODULES_LEVELS.txt", "SUBJECTS_LECTURERS.txt", "SUBJECTS_MODULES.txt"
				);
				$numFiles = 0;
				foreach ($files as $file) {
					if(in_array(basename($file), $validNames)){
						if (is_file($file)) {
							echo "<li><b>".basename($file)."</b> (letzte Änderung: ".date("d.m.y H:i:s", filemtime($file)).")</li>";
							$numFiles++;
						}
					}
				}
				if ($numFiles == 0){
					echo "<i>Keine. Hinweis: Dateien, die vor mehr als 3 Tagen hochgeladen wurden, werden automatisch gelöscht.</i>";
				}
				?>
			</div>
			
			<script>
			$( document ).ready(function() {
				$('.uploadButton').click(function(){

					//var file_data = $('#fileToUpload').prop('files')[0];
					var file_data = $(this).closest('div').find("input[name='fileToUpload']").prop('files')[0];				
					var form_data = new FormData();                  
					form_data.append('file', file_data);
					
					$.ajax({
						url: 'upload.php', // point to server-side PHP script 
						dataType: 'text',  // what to expect back from the PHP script, if anything
						cache: false,
						contentType: false,
						processData: false,
						data: form_data,                         
						type: 'post',
						success: function(data){
							alert(data); // display response from the PHP script, if any
							if(data.includes("erfolg")){
								window.location.reload();
							}else{
								$('.fileToUpload').val("");
							}							
						}
					});
				});
			});			
			</script>
			
			<h2>Schritt 7: Änderungen bestätigen (Matching)</h2>
			<p><i>Schwer bzw. zeitaufwendig zu überprüfen, ob Änderungen korrekt sind. Daher nur Prüfung auf Komisches, wie bspw. Nullen überall.</i></p>
			
			<?php
			$files = glob("uploads/*");
			if (empty($files)){
				echo "<i>Keine. Hinweis: Dateien, die vor mehr als 3 Tagen hochgeladen wurden, werden automatisch gelöscht.</i>";
			}
			$validNames = array(
				"LECTURERS_INSTITUTES.txt", "MODULES_LEVELS.txt", "SUBJECTS_LECTURERS.txt", "SUBJECTS_MODULES.txt"
			);
			foreach ($files as $file) {
				if(in_array(basename($file), $validNames)){
					if (is_file($file)) {
						$myfile = fopen($file, "r") or die("Unable to open file!");
						$content = fread($myfile,filesize($file));
						$content = str_replace(";", ";<br>", $content);
						echo "<p style='margin-top:15px; margin-bottom:5px;'><b>".basename($file)."</b></p>";
						echo "<div class='grey-border' style='max-height:300px; overflow:auto'>".$content."</div>";
						fclose($myfile);
					}
				}
			}
			?>
			
			<h2>Schritt 8: Tabellen aktualisieren (Matching)</h2>
			<p><i>Es werden nur Buttons angezeigt, wenn eine entsprechende Datei hochgeladen wurde.</i></p>
			<?php
			$validNames = array(
				"LECTURERS_INSTITUTES.txt", "MODULES_LEVELS.txt", "SUBJECTS_LECTURERS.txt", "SUBJECTS_MODULES.txt"
			);
			foreach ($files as $file) {
				if(in_array(basename($file), $validNames)){
					if (is_file($file)) {
						$trimmedName = substr(basename($file), 0, -4);
						echo '<button style="margin:5px" class="sqlUpdate btn btn-primary" data-table="'.$trimmedName.'">'.$trimmedName.' aktualisieren</button>';
					}
				}
			}
			?>
			<!--<button style="margin:5px" class="sqlUpdateAll btn btn-primary" data-type="matchings">ALLE aktualisieren</button>-->
			
			<script>
			$( document ).ready(function() {
				$('.sqlUpdate').click(function(){
					var table = $(this).data('table');
					
					//if (confirm(table + ' wirklich aktualisieren?')){
						$.ajax({
							type: "POST",
							url: "sqlUpdate.php",
							data: "table=" + table,
							success: function(data) {
								console.log(data);
								alert(data);
								window.location.reload();
							}
						});
					//}
				});
				
				$('.sqlUpdateAll').click(function(){
					
					var type = $(this).data('type');
					
					if (confirm('Wirklich alle aktualisieren?')){
						$.ajax({
							type: "POST",
							url: "sqlUpdateAll.php",
							data: "type=" + type,
							success: function(data) {
								console.log(data);
								alert(data);
								window.location.reload();
							}
						});
					}
				});
			});
			</script>
		
			<h2>Schritt 9: Datum der Infos anpassen</h2>
			<p><i>Im Studienführer wird angezeigt, aus welchem Modulhandbuch die Informationen stammen, die angezeigt werden. Gib hier das aktuelle Modulhandbuch an.</i></p>
			
			<form id="updateInfoDateForm">
				<div class="form-group">
					Das wird aktuell angezeigt:
					<?php
					$result=mysqli_query($con, "SELECT value FROM help WHERE name='infoDate'");
					$row=mysqli_fetch_assoc($result);
					?>
					<input class="form-control" name="infoDate" value="<?php echo $row['value'] ?>">
				</div>
				<button id="updateInfoDateButton" class="btn btn-primary">Aktualisieren</button>
			</form>
			
			<script>
			$( document ).ready(function() {
				$('#updateInfoDateButton').click(function(){
					alert("klick geht");
					$.ajax({
						type: "POST",
						url: "admin_updateInfo_submit.php",
						data: $("#updateInfoDateForm").serialize(),
						success: function(data) {
							alert(data);
							window.location.reload();
						}
					});
				});
			});
			</script>
			
			<br>
			<p>So wird das Ergebnis aussehen:</p>
			<div style="border: 1px lightgrey solid; border-radius:3px; padding:15px; margin-bottom:15px;">
				<?php echo "Stand der Informationen: " . $row['value']; ?>
			</div>
			
		</div>
		
		<?php if($userRow['super_admin'] == 1){ ?>
		<div id="spam" class="tab-pane fade">
		
			<?php //FORM HANDLING
			$displaySpamDetails = "display:none";
			$spamDetails = "";
			if (isset($_POST['test_button'])) {
				$subject =  $_POST['subject'];
				$body = $_POST['body'];
				$email = $_POST['email'];
				if ((EmailService::getService()->sendEmail($email, $userRow['first_name'], $subject, $body)) == 1 ){
					$testMailMessage = "<br><br><div class='alert alert-success'><span class='glyphicon glyphicon-info-sign'></span> &nbsp; Mail erfolgreich gesendet an: <b>$email</b></div>";
				}else{
					$testMailMessage = "<br><br><div class='alert alert-danger'><span class='glyphicon glyphicon-info-sign'></span> &nbsp; Etwas ist schief gelaufen!</b></div>";
				}
			} else if (isset($_POST['spam_button'])) {
				$sql = "SELECT first_name, email FROM `users` WHERE info = 'yes'";
				//$sql = "SELECT first_name, email FROM `users` WHERE user_ID IN (2)"; //Zum Testen
				$result = mysqli_query($con, $sql);
			
				$subject =  $_POST['subject'];
				$body = $_POST['body'];

				$spamSuccess = False;
				while($row = mysqli_fetch_assoc($result)){
					if ((EmailService::getService()->sendEmail($row["email"], $row["first_name"], $subject, $body)) == 1 ){
						$spamDetails .= "Mail gesendet an: <b>" . $row['email'] . "</b><br>";
						$spamSuccess = True;
					}else{
						$spamMailMessage = "<br><br><div class='alert alert-danger'><span class='glyphicon glyphicon-info-sign'></span> &nbsp; Etwas ist schief gelaufen!</b></div>";
					}

					if($spamSuccess == True){
						$spamMailMessage = "<br><br><div class='alert alert-success'><span class='glyphicon glyphicon-info-sign'></span> &nbsp; SPAM erfolgreich versendet!</b></div>";
						$displaySpamDetails = "";
						unset($subject); //Variablen löschen, damit nicht einfach so erneut Mail an alle geschickt werden kann
						unset($body);
					}
				}
			} else {
				//no button pressed
			}
			?>

			<br>
			
			<p><i>Hier hast du die Möglichkeit, eine Massenmail an alle Nutzer des Studienführers zu schicken, die angekreuzt haben, dass sie "über interessante Events informiert werden wollen".</i></p>
			<br>

			<h3>1. Betreff und Inhalt erstellen</h3>
			<!--<form method="post" action="spam_submit.php">-->
			<form id="spamForm" method="post" action="admin.php#spam">
				<div class="form-group">
					<label>Betreff:</label>
					<input type="text" class="form-control" name="subject" id="mailSubject" value="<?php if (isset($subject)) echo $subject?>" required>
				</div>
				<div class="form-group">
					<p><span style="color:red">Wichtig</span>: Studienführer-E-Mails verfügen bereits über eine Anrede ("Lieber Nutzer") sowie eine Grußformel ("Viele Grüße, Dein Studienführer-Team"). Verzichte also darauf, wenn du das Inhaltsfeld ausfüllst.</p>
					<label>Inhalt:</label>
					<textarea class="form-control" rows="7" name="body" id="mailBody" required><?php if (isset($body)) echo $body?></textarea>
				</div>
		
				<br>
				<h3>2. (Mindestens eine) Testmail verschicken</h3>

				<div class="form-group">
					<label>Deine E-Mail-Adresse:</label>
					<input type="email" class="form-control" name="email" id="testmail" value="<?php if (isset($email)) echo $email?>" required>
				</div>
				<input class="btn btn-primary" type="submit" name="test_button" value="Testmail verschicken">

				<?php if (isset($testMailMessage)) echo $testMailMessage ?>

				<br><br>
				<h3>3. Release the Spam!</h3>

				<?php
				$sql = "SELECT count(user_ID) as yesmen FROM `users` WHERE info = 'yes'";
				$result = mysqli_query($con, $sql);
				$row = mysqli_fetch_assoc($result);
				?>

				<p>Wenn die Testmail deinen Wünschen entspricht, kannst du jetzt diese Mail an alle Nutzer schicken, die angekreuzt haben, dass sie "über interessante Events informiert werden wollen".<br>
				Im Moment sind das: <b><?php echo $row['yesmen']?> Nutzer</b></p>

				<input class="btn btn-danger" type="submit" name="spam_button" id="spam_button" value="SPAM jetzt verschicken">

				<?php if (isset($spamMailMessage)) echo $spamMailMessage ?>

				<br>
				<button style="<?php echo $displaySpamDetails?>" type='button' class='btn btn-light' data-toggle='collapse' data-target='#spamDetails'>Details anzeigen/ausblenden</button>
				<div id='spamDetails' class='collapse show'>
					<?php echo $spamDetails?>
				</div>

			</form>

			<script>
				$('#spam_button').click(function() { //Mail muss nicht eingegeben werden, wenn SPAM verschickt wird
					$('#testmail').removeAttr('required');
				});
			</script>
		
		</div>
		<?php } ?>
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
$('#linkToAdminList').click(function(event){
 	event.preventDefault();
 	$('.nav-tabs a[href="#adminList"]').tab('show');
});
$('#linkToUserProfiles').click(function(event){
 	event.preventDefault();
 	$('.nav-tabs a[href="#userProfiles"]').tab('show');
});
$('#linkToNotes').click(function(event){
 	event.preventDefault();
 	$('.nav-tabs a[href="#notes"]').tab('show');
});
$('#linkToSemproAds').click(function(event){
 	event.preventDefault();
 	$('.nav-tabs a[href="#semproAds"]').tab('show');
});
$('#linkToUpdate').click(function(event){
 	event.preventDefault();
 	$('.nav-tabs a[href="#update"]').tab('show');
});
$('#linkToSpam').click(function(event){
 	event.preventDefault();
 	$('.nav-tabs a[href="#spam"]').tab('show');
});
</script>

</body>
</html>