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
			<i>Hier können bald automatisch ermittelte Änderungen des Modulhandbuches eingepflegt werden.</i>
			<br><br>
			<div class="alert alert-danger">
				<strong>Vorsicht!</strong> Diese Seite befindet sich noch im Aufbau - alle Daten sind Beispieldaten.
			</div>
			
			<h2>Vorlesungen</h2>
			<h3>Geänderte</h3>
			<h3 style="display:inline; padding-right: 10px">Hinzugekommene</h3> <a id="display_leactures_added"><span class="glyphicon glyphicon-chevron-down"></span></a>
			<br><br>
			
			<table class="table table-striped" id="table_leactures_added">
				<tr>
					<th>Name</th>
					<th>Kennung</th>
					<th>ECTS</th>
					<th>Semester</th>
					<th>Sprache</th>
					<th>Bearbeiten</th>
					<th>Hinzufügen</th>
					<th>Löschen</th>
				</tr>
			
			<?php
			$sql = "SELECT * FROM `ADDED_LECTURES`";
			$result = mysqli_query($con,$sql);
			while($row = mysqli_fetch_assoc($result)){
				?>
					<tr>
						<td><?php echo $row['subject_name'] ?></td>
						<td><?php echo $row['identifier'] ?></td>
						<td><?php echo $row['ECTS'] ?></td>
						<td><?php echo $row['semester'] ?></td>
						<td><?php echo $row['language'] ?></td>
						<td>
							<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editSubjectModal"
								data-id="<?php echo $row['ID'] ?>"
								data-subject_name="<?php echo $row['subject_name'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
								data-ects="<?php echo $row['ECTS'] ?>"
								data-semester="<?php echo $row['semester'] ?>"
								data-language="<?php echo $row['language'] ?>"
							>Bearbeiten</button>
						</td>
						<td>
							<button type="button" class="btn btn-primary addSubject_addButton"
								data-id="<?php echo $row['ID'] ?>"
								data-subject_name="<?php echo $row['subject_name'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
								data-ects="<?php echo $row['ECTS'] ?>"
								data-semester="<?php echo $row['semester'] ?>"
								data-language="<?php echo $row['language'] ?>"
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
			
			<div class="modal fade" id="editSubjectModal" tabindex="-1" role="dialog" aria-labelledby="editSubjectModalLabel" aria-hidden="true">
			  <div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-body" id="addedSubject_edit_modal-body">
						<form id="addedSubject_edit_form">
							<div class="form-group">
								<label class="col-form-label">ID:</label>
								<input type="text" class="form-control" name ="id" id="id">
							</div>
							<div class="form-group">
								<label class="col-form-label">Name:</label>
								<input type="text" class="form-control" name="subject_name" id="subject_name">
							</div>
							<div class="form-group">
								<label class="col-form-label">Kennung:</label>
								<input type="text" class="form-control" name="identifier" id="identifier">
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

						</form>
					</div>
					<div class="modal-footer" id="addedSubject_edit_modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
						<button type="button" class="btn btn-primary" id="save-changes-button">Änderungen speichern</button>
					</div>
				</div>
			  </div>
			</div>
			
			<script>
			$( document ).ready(function() {
				//Show and hide
				$("#display_leactures_added").click(function() {
					if($("#table_leactures_added").is(":visible")){
						$("#table_leactures_added").hide();
					}else{
						$("#table_leactures_added").show();
					}
					
				});
				
				//show modal
				$('#editSubjectModal').on('show.bs.modal', function (event) {
					var button = $(event.relatedTarget) // Button that triggered the modal
					
					var id = button.data('id')
					var subject_name = button.data('subject_name')
					var identifier = button.data('identifier')
					var ECTS = button.data('ects')
					var semester = button.data('semester')
					var language = button.data('language')
					
					var modal = $(this)
					modal.find('.modal-body #id').val(id)
					modal.find('.modal-body #subject_name').val(subject_name)
					modal.find('.modal-body #identifier').val(identifier)
					modal.find('.modal-body #ECTS').val(ECTS)
					modal.find('.modal-body #semester').val(semester)
					modal.find('.modal-body #language').val(language)
				})
				
				//save changes
				$('#save-changes-button').click(function() {
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
					
					var result = confirm('Veranstaltung "' + subject_name + '" wirklich hinzufügen?');
					if(result){
						$.ajax({
							type: "POST",
							url: "admin_updateLectureAdded_add_submit.php",
							data: "id=" + id + "&subject_name=" + subject_name + "&identifier=" + identifier + "&ECTS=" + ECTS + "&semester=" + semester + "&language=" + language,
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
			
			<h3>Gelöschte</h3>
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