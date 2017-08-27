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
	<!--<li><a data-toggle="tab" href="#menu2">Menu 2</a></li>
		<li><a data-toggle="tab" href="#menu3">Menu 3</a></li>-->
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
						$badge[$types[$i]] = mysqli_num_rows(mysqli_query($con, "SELECT * FROM messages WHERE message_type = '".$types[$i]."'"));
					}
					?>
				
					<button class="tablinks active" onclick="changeInbox(event, 'Bugs')">Bugs <span class="badge"><?php echo $badge['bug']?></span></button>
					<button class="tablinks" onclick="changeInbox(event, 'Fehler')">Fehler <span class="badge"><?php echo $badge['mistake']?></span></button>
					<button class="tablinks" onclick="changeInbox(event, 'Fragen')">Fragen <span class="badge"><?php echo $badge['question']?></span></button>
					<button class="tablinks" onclick="changeInbox(event, 'Feedback')">Feedback <span class="badge"><?php echo $badge['feedback']?></span></button>
					<button class="tablinks" onclick="changeInbox(event, 'Kommentare')">Kommentare <span class="badge"><?php echo $badge['comment']?></span></button>
				</div>

				<div id="Bugs" class="tabcontent" style="display:block">
					<p style="font-size:20px"><span id="open" style="font-weight:bold">Offen</span> | <span id="closed" style="color:lightgrey" >Bearbeitet</span></p>
					
					<?php
					$result = mysqli_query($con, "SELECT * FROM messages WHERE message_type = 'bug' AND processed = 0");
					
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
							$assignedToLine = "<span class=\"text\">Wird bearbeitet von:<br><strong>".$assignedTo."</strong></span>";
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
				</div>

				<div id="Fehler" class="tabcontent">
					<p style="font-size:20px"><span id="open" style="color:lightgrey">Offen</span> | <span id="closed" style="font-weight:bold" >Bearbeitet</span></p>
					
					<div class="message" style="border-bottom: solid lightgrey 1px;">
						<span class="symbol glyphicon glyphicon-ok-circle"></span>
						<span class="text">Als gelöst markiert von:<br><strong>der_albert</strong></span>
						<span class="symbol glyphicon glyphicon glyphicon-send"></span>
						<span class="text">Antwort verschickt am:<br><strong>11.08.17</strong></span>
					</div>
					
					<div class="message" style="border-bottom: solid lightgrey 1px;">
						<span class="symbol glyphicon glyphicon-remove-circle"></span>
						<span class="text">Als ungelöst markiert von:<br><strong>der_albert</strong></span>
						<span class="symbol glyphicon glyphicon glyphicon-send"></span>
						<span class="text">Antwort verschickt am:<br><strong>11.08.17</strong></span>
					</div>
					
				</div>

				<div id="Fragen" class="tabcontent">
					<p style="font-size:20px"><span id="open" style="font-weight:bold">Offen</span> | <span id="closed" style="color:lightgrey" >Bearbeitet</span></p>
					
					<div class="message" style="border-bottom: solid lightgrey 1px;">
						<span class="symbol glyphicon glyphicon-envelope"></span>
						<span class="text">Empfangen: 22.08.2017<br></span>
					</div>
					
					<div class="message" style="border-bottom: solid lightgrey 1px;">
						<span class="symbol glyphicon glyphicon-list-alt"></span>
						<span class="text">Empfangen: 22.08.2017<br>Zuletzt gelesen: <strong>der_albert</strong></span>
						<span class="symbol glyphicon glyphicon glyphicon-user"></span>
						<span class="text">Wird bearbeitet von:<br><strong>der_albert</strong></span>
					</div>
					
					<div class="message" style="border-bottom: solid lightgrey 1px;">
						<span class="symbol glyphicon glyphicon-list-alt"></span>
						<span class="text">Empfangen: 22.08.2017<br>Zuletzt gelesen: <strong>der_albert</strong></span>
						<span class="symbol glyphicon glyphicon glyphicon-question-sign"></span>
						<span class="text">Wird bearbeitet von:<br><strong><i>nicht zugewiesen</i></strong></span>
					</div>
				</div>
				
				<div id="Feedback" class="tabcontent">
					tbd
				</div>
				
				<div id="Kommentare" class="tabcontent">
					tbd
				</div>
				
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
				$('#messageDetail').on('click', '#assignButton', function() {
					$.ajax({
						url: "admin_assignMessage.php",
						type: "post",
						data: $("#assignForm").serialize() + "&message_id=" + m_id,
						success: function (data) {
							alert("Erfolgreich zugewiesen!");
							var output = "<span class=\"text\">Wird bearbeitet von:<br><strong>" + data + "</strong></span>";
							$(this_save).find(".assignedTo").html(output);
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
				});
				
				//Open Inbox
				$("#backToInbox").click(function(){
					$("#inbox").show();
					$("#messageContent").hide();
				});
				
				
			});	
			</script>
		
		</div> <!-- messages END -->
		
			
	<!--<div id="menu2" class="tab-pane fade">
			<h3>Menu 2</h3>
			<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
		</div>
		<div id="menu3" class="tab-pane fade">
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
</script>

</body>
</html>