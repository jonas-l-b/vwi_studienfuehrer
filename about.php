<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div class="container">

	<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" href="#info">Infos und FAQ</a></li>
		<li><a data-toggle="tab" href="#newFeatures">Neue Features</a></li>
		<li><a data-toggle="tab" href="#community_guidelines">Selbstverständnis & Nutzungsrichtlinien</a></li>
	</ul>

	<div class="tab-content">
	<div id="info" class="tab-pane fade in active">
		<h3>Infos und Frequently Asked Questions</h3>
		<p>Der Studienführer ist ein Projekt von VWI-ESTIEM Hochschulgruppe Karlsruhe e.V. von wurde von Studenten für Studenten zur kostenlosen Nutzung erstellt.</p>
		<p>Im Folgenden beantworten wir wiederkehrende Fragen; falls Du darüber hinaus noch etwas wissen willst, Anregungen hast oder einfach Feedback geben möchtest, kannst du das über "Kontakt" im Menü oben tun.</p>
		<br>
		
		<!-- FAQs auch in login.php. Auch dort aktualisieren!-->		
		<div class="panel-group" id="accordion">
			<div class="panel panel-default">
			  <div class="panel-heading">
				<h4 class="panel-title">
				  <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Warum muss ich mich für die Nutzung des Studienführers registrieren?</a>
				</h4>
			  </div>
			  <div id="collapse1" class="panel-collapse collapse">
				<div class="panel-body">Die Registrierung der einzelnen Nutzer ist notwendig, damit wir eine hohe Datenqualität gewährleisten können. 
				So wird beispielsweise verhindert, dass Nutzer die gleiche Veranstaltung mehrere Male bewerten. Durch die Bindung der Registrierung an
				die KIT-E-Mail-Adresse verhindern wir außerdem, dass Fake-Accounts angelegt werden können.<br>
				Weiterhin können wir so weitere Services wie das Speichern von Favoriten oder die Benachrichtigung für
				beantwortete Fragen zur Verfügung stellen.</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading">
				<h4 class="panel-title">
				  <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Fallen Kosten für die Registrierung und Nutzung des Studienführers an?</a>
				</h4>
			  </div>
			  <div id="collapse2" class="panel-collapse collapse">
				<div class="panel-body">Nein. Wir sind auch Studenten des KIT und haben den Studienführer ohne Gewinnabsichten entwickelt. Der Studienführer wird
				auch in Zukunft kostenlos bleiben: Kostenlos von Studenten für Studenten.</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading">
				<h4 class="panel-title">
				  <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">Wer hat den Studienführer entwickelt und wer betreibt ihn?</a>
				</h4>
			  </div>
			  <div id="collapse3" class="panel-collapse collapse">
				<div class="panel-body">Der Studienführer ist ein Projekt der VWI-ESTIEM Hochschulgruppe Karlsruhe e.V.</div>
			  </div>
			</div>
		</div>
	
	</div>

	<div id="newFeatures" class="tab-pane fade">
		<h3>Neue Features</h3>
		<p>Wir entwickeln den Studienführer stets weiter, um die Nutzererfahrung immer weiter zu verbessern. Hier kannst du sehen, an was wir gerade arbeiten. Falls Dir ein Feature in dieser Liste fehlt, kannst Du Dich über "Kontakt" im Menü oben mit uns in Verbindung setzen.</p>
		<br>
		<h4>In Bearbeitung:</h4>
		<ul class="list-group">
			<?php
			$ongoing = array(
				"Granularere Benachrichtigungen: Benachrichtigungsmails für einzelne Fragen einstellen können",
				"Erweiterung der Veranstaltungssuche, bspw. um die Option, nach ECTS zu filtern"
			);
			
			foreach($ongoing as $item){
				echo "<li class=\"list-group-item\">".$item."</li>";
			}
			?>
		</ul>
		<br>
		<h4 style="color:lightgrey">Kürzlich abgeschlossen:</h4>
		<ul style="color:lightgrey" class="list-group">
			<?php
			$done = array(
				"Feed auf der Startseite mit den neusten Bewertungen und Fragen",
				"Benachrichtigungsmails für beantwortete Fragen abstellen können",
				"Möglichkeit, Veranstaltungen zu Favoriten hinzuzufügen"
			);
			
			foreach($done as $item){
				echo "<li class=\"list-group-item\">".$item."</li>";
			}
			?>

		</ul>
	</div>

	<div id="community_guidelines" class="tab-pane fade">
		<h3>Selbstverständnis & Nutzungsrichtlinien</h3>
		<?php
		$sql="SELECT * FROM multiple_location_content WHERE name = 'community_guidelines'";
		$result=mysqli_query($con, $sql);
		$row = mysqli_fetch_assoc($result);

		echo $row['value']
		?>
	</div>
	</div>
</div>


</body>
</html>