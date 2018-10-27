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
		<li><a data-toggle="tab" href="#community_guidelines">Gemeinschaftsrichtlinien</a></li>
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
		<h3>Gemeinschaftsrichtlinien</h3>
		<?php
		$sql="SELECT * FROM multiple_location_content WHERE name = 'community_guidelines'";
		$result=mysqli_query($con, $sql);
		$row = mysqli_fetch_assoc($result);

		echo $row['value']
		?>
		<br>
		<div id="communityGuidelineFormDiv" style="margin:auto; padding:20px; width: 100%; min-width: 300px; background-color:#f2f2f2; border-radius:5px">
			<div id="communityGuidelineFormQuestionDiv">
				<form id="communityGuidelineForm">
					
					<h3 style="margin-top:0px">Alles verstanden? Gleich testen!</h3>
					
					<div style="background-color:white; border-radius:5px; padding:5px; padding-left:10px; margin:5px;">
						<p>"Der Studienführer führt nur subjektive Meinungen und seine Inhalte sollten jederzeit kritisch hinterfragt werden." - Stimmt das? </p>
						<div class="radio">
							<label><input type="radio" name="q1" value="1" required>Vollkommen richtig, die Meinungen sind von anderen Studierenden und somit subjektiv.</label>
						</div>
						<div class="radio">
							<label><input type="radio" name="q1" value="2">Nein, die Inhalte sind stets objektiv!</label>
						</div>
						<div class="radio">
							<label><input type="radio" name="q1" value="3">Was tut das zur Sache? Das ist doch egal!</label>
						</div>
					</div>
					
					<div style="background-color:white; border-radius:5px; padding:5px; padding-left:10px; margin:5px;">
						<p>"Der Studienführer ermöglicht mir, mein Studium bei möglichst wenig Aufwand mit möglichst guten Noten abzuschließen." - Stimmt das?</p>
						<div class="radio">
							<label><input type="radio" name="q2" value="1" required>Ja, und man sollte ihn auch dazu nutzen!</label>
						</div>
						<div class="radio">
							<label><input type="radio" name="q2" value="2">Das stimmt zwar, aber dennoch sollte man sich hauptsächlich von seinen Interessen leiten lassen.</label>
						</div>
						<div class="radio">
							<label><input type="radio" name="q2" value="3">Ich hoffe, ich studiere eh nur wegen den $$$.</label>
						</div>
					</div>
					
					<div style="background-color:white; border-radius:5px; padding:5px; padding-left:10px; margin:5px;">
						<p>"Mit seiner großen Reichweite ist der Studienführer bestens geeignet, um Frust über jegliche Vorlesungen loszuwerden." - Stimmt das?</p>
						<div class="radio">
							<label><input type="radio" name="q3" value="1" required>Nein, wer kennt schon den Studienführer?</label>
						</div>
						<div class="radio">
							<label><input type="radio" name="q3" value="2">Auch wenn die Reichweite groß ist, Frust gehört auf keinen Fall hier her!</label>
						</div>
						<div class="radio">
							<label><input type="radio" name="q3" value="3">Jep, ich suche schön längere eine Alternative für die ILIAS-Foren.</label>
						</div>
					</div>
					
					<button class="btn btn-default">Abschicken</button>
				</form>
			</div>
			
			<div>
				<p id="communityGuidelineFormFalse" style="display:none">Da stimmt leider noch nicht alles. Gleich nochmal probieren!</p>
				<p id="communityGuidelineFormCorrect" style="display:none">Alles richtig beantwortet! Danke, dass du dir Zeit genommen hast, unsere Gemeinschaftsrichtlinien zu lesen.</p>
				<p id="newAchievement" style="display:none"><b>Du hast du eine neue Errungenschaft freigeschaltet. Schau sie dir gleich an unter: <a href="userProfile.php#achievements">Meine Errungenschaften</a>!</b></p>
				<div id="AgainButton" style="display:none"><button class="btn-default btn" onClick="window.location.reload()">Nochmal</button></div>
			</div>
		</div>
			
		<script>
		$(document).ready(function(){
	
			$("#communityGuidelineForm").submit(function(e) {


				var form = $(this);
				var url = 'communityGuidelineForm_submit.php';

				$.ajax({
					type: "POST",
					url: url,
					data: form.serialize(), // serializes the form's elements.
					success: function(data){
						$('#communityGuidelineFormQuestionDiv').hide();
						$('#AgainButton').show();
						if(data.includes("correct")){
							$('#communityGuidelineFormCorrect').show();
						}
						if(data.includes("achievement")){
							$('#newAchievement').show();
						}
						if(data.includes("false")){
							$('#communityGuidelineFormFalse').show();
						}
					}
				});
				e.preventDefault(); // avoid to execute the actual submit of the form.
			});
			
		});	
		</script>
		
		<br><br>
		
	</div>
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
});
</script>

</body>
</html>