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
			<div class="tab">
				<button class="tablinks active" onclick="openInbox(event, 'Bugs')">Bugs <span class="badge">5</span></button>
				<button class="tablinks" onclick="openInbox(event, 'Fehler')">Fehler <span class="badge">5</span></button>
				<button class="tablinks" onclick="openInbox(event, 'Fragen')">Fragen <span class="badge">5</span></button>
				<button class="tablinks" onclick="openInbox(event, 'Feedback')">Feedback <span class="badge">5</span></button>
				<button class="tablinks" onclick="openInbox(event, 'Kommentare')">Kommentare <span class="badge">5</span></button>
			</div>

			<div id="Bugs" class="tabcontent" style="display:block">
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
				tbd
			</div>
			
			<div id="Feedback" class="tabcontent">
				tbd
			</div>
			
			<div id="Kommentare" class="tabcontent">
				tbd
			</div>
		</div>
		
		<script>
		function openInbox(evt, type) {
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