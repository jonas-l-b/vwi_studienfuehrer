<?php

/*
*		Dieses Skript liefert das Bewertungsform unbefüllt oder befüllt
*/

include "sessionsStart.php";
include "connect.php";
?>

<?php
if (isset($_GET['subject'])){
	$subject = $_GET['subject'];
	$userID = $userRow['user_ID'];

	$lectureItems = array(
		"Für wie prüfungsrelevant bewertest du den Vorlesungsbesuch (Folien selbsterklärend? Vorlesung behandelt zusätzlichen Stoff?)?",
		"Wie interessant war die Vorlesung gestaltet?",
		"Wie bewertest du die Veranstaltungsmaterialien?"
	);

	$lectureItemsLabels = array(
		array("Nicht relevant", "Sehr relevant"),
		array("Uninteressant", "Sehr interessant"),
		array("Unstrukturiert, Unvollständig", "Strukturiert, Selbsterklärend, Vollständig")
	);
	
	$examItems = array(
		"Lag der Schwerpunkt auf Reproduktion oder auf Transfer?",
		"Wie rechenlastig war die Prüfung?",
		"War der Aufwand zur Prüfungsvorbereitung dem Leistungsumfang (ECTS) der Veranstaltung gegenüber angemessen?",
		"Wie hat dich die Gesamtheit der Lernmöglichkeiten (inkl. Tutorium, Übung, Forum, Buch, ...) auf die Prüfung vorbereitet?",
	);
	$examItemsLabels = array(
		array("Reproduktion", "Transfer"),
		array("Nicht rechenlastig", "Sehr rechenlastig"),
		array("Aufwand deutlich geringer", "Aufwand deutlich höher"),
		array("Schlecht", "Gut")
	);

	$generalItems = array(
		"Wie bewertest du die Veranstaltung ingesamt? (1 = schlecht, 10 = gut)",
	);

	//Values if already filleds
	if(isset($_GET['filled'])){
		$statement1 = $con->prepare("SELECT * FROM ratings
			WHERE ratings.user_ID = ? AND ratings.subject_ID = (SELECT ID FROM subjects WHERE subjects.ID = ?);");
		$statement1->bind_param('ss', $userID, $subject);
		$statement1->execute();
		$result = $statement1->get_result();
		$ratingData = mysqli_fetch_assoc($result);

		$lectureValues = array($ratingData['lecture0'], $ratingData['lecture1'], $ratingData['lecture2']);
		$examValues = array($ratingData['exam0'], $ratingData['exam1'], $ratingData['exam2'], $ratingData['exam3']);
		//examType
		switch($ratingData['examType']){
			case "written":
				$written = "selected";
				$oral = "";
				$other = "";
				break;
			case "oral":
				$written = "";
				$oral = "selected";
				$other = "";
				break;
			case "other":
				$written = "";
				$oral = "";
				$other = "selected";
				break;
		}
		//examText
		$examText = $ratingData['examText'];
		//examSemester
		$examSemester = $ratingData['examSemester'];
		//General
		$general0 = $ratingData['general0'];
		//Recommendation
		if($ratingData['recommendation'] == 1){
			$recom1 = "checked";
			$recom0 = "";
		} else{
			$recom1 = "";
			$recom0 = "checked";
		}
		//comment
		$comment = $ratingData['comment'];
	}else{
		$lectureValues = array(0,0,0);
		$examValues = array(0,0,0,0);
		$written = "";
		$oral = "";
		$other = "";
		$examText = "";
		$examSemester = "choose";
		$general0 = "";
		$recom1 = "";
		$recom0 = "";
		$comment = "";
	}
	
	//Erfolgstext
	$result = mysqli_query($con, "SELECT * FROM `rating_submit_messages` ORDER BY rand() LIMIT 1");
	$row = mysqli_fetch_assoc($result);
	$successMessage = $row['message'];

/*
	echo $twig->render('bewerten.template.html',
						array(	'subject' => $subject,
								'form_target' => 'rating_submit.php',
								'button_text' => 'Bewertung abschicken',
								'lectureItems' => $lectureItems,
								'lectureItemsLabels' => $lectureItemsLabels,
								'examItems' => $examItems,
								'examItemsLabels' => $examItemsLabels,
								'generalItems' => $generalItems,

								'lectureValues' => $lectureValues,
								'typeWritten' => $written,
								'typeOral' => $oral,
								'typeOther' => $other,
								'examValues' => $examValues,
								'examText' => $examText,
								'examSemester' => $examSemester,
								'general0' => $general0,
								'weiterempfehlen_ja' => $recom1,
								'weiterempfehlen_nein' => $recom0,
								'comment' => $comment,
								
								'successMessage' => $successMessage,
							));
*/
	//from here
	?>
	<form id="ratingForm" action="rating_submit.php?subject=<?php echo $subject?>" method="POST">

		<!--<button type="button" id="button" class="btn">Give value!</button>-->

		<p style="font-weight: bold; font-size: 20px; color: rgb(0, 51, 153)">Bewertung der Vorlesung</p>
		
		<?php
		foreach($lectureItems as $key => $value){
			echo "
				<p>$value</p>
				<span>".$lectureItemsLabels[$key][0]."</span><span style='float:right'>".$lectureItemsLabels[$key][1]."</span><br>
				<input type='range' style='width:100%' class='sliderLecture".$key."' min='-3' max='3' step='1' value='".$lectureValues[$key]."'>
				<br><br>
			";
		}
		?>
		
		<br>
		<p style="font-weight: bold; font-size: 20px; color: rgb(0, 51, 153)">Bewertung der Prüfung</p>

		<p>Wann wurdest du geprüft?</p>
		<select id="examSemester" class="form-control" style="width:100%" required>
			<option disabled selected value="" style=\"display:none\"> -- Bitte wählen -- </option>
			<option value="ss10">SS10</option>
			<option value="ws10-11">WS10-11</option>
			<option value="ss11">SS11</option>
			<option value="ws11-12">WS11-12</option>
			<option value="ss12">SS12</option>
			<option value="ws12-13">WS12-13</option>
			<option value="ss13">SS13</option>
			<option value="ws13-14">WS13-14</option>
			<option value="ss14">SS14</option>
			<option value="ws14-15">WS14-15</option>
			<option value="ss15">SS15</option>
			<option value="ws15-16">WS15-16</option>
			<option value="ss16">SS16</option>
			<option value="ws16-17">WS16-17</option>
			<option value="ss17">SS17</option>
			<option value="ws17-18">WS17-18</option>
			<option value="ss18">SS18</option>
			<option value="ws18-19">WS18-19</option>
			<option value="ss19">SS19</option><!--
			<option value="ws19-20">WS19-20</option>
			<option value="ss20">SS20</option>
			<option value="ws20-21">WS20-21</option>
			<option value="ss21">SS21</option>
			<option value="ws21-22">WS21-22</option>
			<option value="ss22">SS22</option>
			<option value="ws22-23">WS22-23</option>
			<option value="ss23">SS23</option>
			<option value="ws23-24">WS23-24</option>
			<option value="ss24">SS24</option>
			<option value="ws24-25">WS24-25</option>
			<option value="ss25">SS25</option>-->
		</select>

		<script>
		$(document).ready(function () {
			$('#examSemester option[value=<?php echo $examSemester?>]').attr('selected','selected');
		});
		</script>

		<br>

		<p>Wie wurdest du geprüft (Boni etc. nicht berücksichtigen)?</p>
		<select id="examType" class="form-control" style="width:100%" required>
			<option disabled selected value style=\"display:none\"> -- Bitte wählen -- </option>
			<option value="written" <?php echo $written?>>Schriftlich</option>
			<option value="oral" <?php echo $oral?>>Mündlich</option>
			<option value="other" <?php echo $other?>>Sonstige</option>
		</select>
		<br>
		<textarea class="form-control" style="width:100%; display:none; margin-top:-15px; margin-bottom:15px;" rows="5" maxlength="2500" id="examDetails" placeholder="Wie genau wurdest du geprüft? Was wurde wie bewertet? Welche Tipps kannst du zukünftigen Prüflingen mit auf den Weg geben? Erzähl uns mehr!"><?php echo $examText?></textarea>
		<div id="commentWarningExamDetails"></div>

		<script>
		$("#examType").on('change', function() {
			if($('#examType option:selected').val() == "other"){
				$("#examDetails").show();
				$("#commentWarningExamDetails").show();
				$(".examItems").hide();
				$("#examDetails").prop('required',true);
			}
			if($('#examType option:selected').val() != "other"){
				$("#examDetails").hide();
				$("#commentWarningExamDetails").hide();
				$(".examItems").show();
				$("#examType :input").prop('required',null);
				$("#examDetails").prop('required',false);
			}
		});

		//Gleicher Code. Nötig, damit alles richtig angezeigt wird, wenn Kommentar bearbeitet wird

		$(document).ready(function () {
			if($('#examType option:selected').val() == "other"){
				$("#examDetails").show();
				$("#commentWarningExamDetails").show();
				$(".examItems").hide();
				$("#examDetails").prop('required',true);
			}
			if($('#examType option:selected').val() == "written" || $('#examType option:selected').val() == "oral"){
				$("#examDetails").hide();
				$("#commentWarningExamDetails").hide();
				$(".examItems").show();
				$("#examType :input").prop('required',null);
				$("#examDetails").prop('required',false);
			}
		});

		$('#examDetails').on("propertychange input textInput", function() {
			if($('#examDetails').val().length < 2000){
				$('#commentWarningExamDetails').html("");
			}else if($('#examDetails').val().length >= 2000 && $('#examDetails').val().length < 2400){
				$('#commentWarningExamDetails').css('color', 'black');
				$('#commentWarningExamDetails').html("Noch " + (2500 - $('#examDetails').val().length) + " Zeichen übrig");
			}else{
				$('#commentWarningExamDetails').css('color', 'red');
				$('#commentWarningExamDetails').html("Noch " + (2500 - $('#examDetails').val().length) + " Zeichen übrig");
			}
		});
		</script>

		<?php
		foreach($examItems as $key => $value){
			echo "
				<div style='display:none' class='examItems'>
					<p>".$value."</p>
					<span>".$examItemsLabels[$key][0]."</span><span style='float:right'>".$examItemsLabels[$key][1]."</span><br>
					<input type='range' style='width:100%' class='sliderExam".$key."' min='-3' max='3' step='1' value='".$examValues[$key]."'>

					<br><br>
				</div>
			";
		}
		?>


		<br>
		<p style="font-weight: bold; font-size: 20px; color: rgb(0, 51, 153)">Bewertung insgesamt</p>

		<?php
		foreach($generalItems as $key => $value){
			echo "
				<p>".$value."</p>
				<input type='range' class='sliderGeneral".$key."' min='0' max='10' step='1' value='".$general0."'>
				<span class='sliderGeneral".$key."'>".$general0."</span>
				<br><br>
			";
		}
		?>


		<p style="font-weight:bold">Würdest du diese Veranstaltung weiterempfehlen?</p>

		<label class="radio-inline"><input type="radio" name="recommendation" value='1' <?php echo $recom1?> required>Ja</label>
		<label class="radio-inline"><input type="radio" name="recommendation" value='0' <?php echo $recom0?>>Nein</label>

		<br>
		<hr>

		<p style="font-weight: bold; font-size: 20px; color: rgb(0, 51, 153)">Kommentar</p>

		<div class="form-group">
			<textarea id="ratingComment" name="comment" class="form-control" rows="5" minlength=25 maxlength="2500" placeholder="Was gibt es sonst interessantes über die Veranstaltung festzuhalten? Speziell sind natürlich Dinge interessant, die du nicht über die Fragen oben ausdrücken konntest!" required><?php echo $comment?></textarea>
		</div>

		<div id="commentWarning"></div>

		<script>
		$('#ratingComment').on("propertychange input textInput", function() {
			if($('#ratingComment').val().length < 2000){
				$('#commentWarning').html("");
			}else if($('#ratingComment').val().length >= 2000 && $('#ratingComment').val().length < 2400){
				$('#commentWarning').css('color', 'black');
				$('#commentWarning').html("Noch " + (2500 - $('#ratingComment').val().length) + " Zeichen übrig");
			}else{
				$('#commentWarning').css('color', 'red');
				$('#commentWarning').html("Noch " + (2500 - $('#ratingComment').val().length) + " Zeichen übrig");
			}
		});
		</script>

		<hr>

		<button id="submitButton" type="submit" class="btn btn-primary"><div id="submitButton">Bewertung abschicken</div></button>
		
	</form>

	<script>
	//send to DP
	$("#submitButton").click(function(){

		var lecture0 = $(".sliderLecture"+0).val();
		var lecture1 = $(".sliderLecture"+1).val();
		var lecture2 = $(".sliderLecture"+2).val();

		var examType = $('#examType option:selected').val();

		var exam0 = $(".sliderExam"+0).val();
		var exam1 = $(".sliderExam"+1).val();
		var exam2 = $(".sliderExam"+2).val();
		var exam3 = $(".sliderExam"+3).val();

		var examText = $('#examDetails').val();

		var examSemester = $('#examSemester option:selected').val();

		var general0 = $(".sliderGeneral"+0).val();

		var recommendation = $('input:radio[name="recommendation"]:checked').val()
		var comment = $('#ratingComment').val();

		var subject = getUrlParameter('subject');

		$("#ratingForm").submit(function(e){
			$('#submitButton').html("Verarbeite...");
			
			$.ajax({
				type: "POST",
				url: "rating_submit.php",
				data: {
					lecture0: lecture0,
					lecture1: lecture1,
					lecture2: lecture2,
					examType: examType,
					exam0: exam0,
					exam1: exam1,
					exam2: exam2,
					exam3: exam3,
					examText: examText,
					examSemester: examSemester,
					general0: general0,
					recommendation: recommendation,
					comment: comment,
					subject: subject
				},
				success: function(data) {
					//alert(data);
					$('#jetztBewertenModal').on('hide.bs.modal', function(e) {
								   e.preventDefault();
								   location.reload();
					});
					$('#editModal').on('hide.bs.modal', function(e) {
								   e.preventDefault();
								   location.reload();
					});
					if(data.trim().substr(0,6) == "erfolg"){ //substring stellt sicher, dass hier auch reingegangen wenn E-Mail-Fehler auftritt
						$('.modal-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; <?echo $successMessage?> </div><button type=\"button\" onclick=\"reload()\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen</button>");
					}else if(data.trim().substr(0,6) == "errorM"){
						$('.modal-body').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Fehler! Es befinden sich mehrere Bewertungen von dir zu dieser Veranstaltung in der Datenbank. Bitte wende dich an VWI-ESTIEM Karlsruhe.</div><button type=\"button\" onclick=\"reload()\" class=\"btn btn-primary btn-dismiss\" data-dismiss=\"modal\">Schließen</button>");
					}else if(data.trim().substr(0,6) == "change"){
						$('.modal-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Deine Bewertung wurde erfolgreich geändert!</div><button type=\"button\" onclick=\"reload()\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen</button>");
					}else{
						$('.modal-body').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Bei der Übermittlung deiner Bewertung ist ein Fehler aufgetreten. Bitte wende dich an VWI-ESTIEM Karlsruhe.</div><button type=\"button\" onclick=\"reload()\" class=\"btn btn-primary btn-dismiss\" data-dismiss=\"modal\">Schließen</button>");
					}
					
					if(data.includes("achievement")){
						alert("Du hast eine neue Errungenschaft freigeschaltet! Schau gleich nach unter Profil > Errungenschaften.");
					}
				}
			});
			e.preventDefault();
		});
	});

	function reload() {
		location.reload();
	}


	//Function for getting URL params
	var getUrlParameter = function getUrlParameter(sParam) {
		var sPageURL = decodeURIComponent(window.location.search.substring(1)),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : sParameterName[1];
			}
		}
	};
	</script>

	<script>
		//Slider
		$('input[type="range"]').on("change mousemove", function () {
			var val = ($(this).val() - $(this).attr('min')) / ($(this).attr('max') - $(this).attr('min'));

			if($(this).attr('max') == 10){
				$(this).css('background-image',
				'-webkit-gradient(linear, left top, right top, '
				+ 'color-stop(' + val + ', #2d5699), '
				+ 'color-stop(' + val + ', #d3d3db)'
				+ ')'
				);
			}else{			
				if($(this).val() <= 0){
					$(this).css('background-image',
					'-webkit-gradient(linear, right top, left top, '
					+ 'color-stop(' + 0.5 + ', #d3d3db), '	//grey
					+ 'color-stop(' + 0.5 + ', rgb(45, 86, 153)), '	//blue
					+ 'color-stop(' + (1-val) + ', rgb(45, 86, 153)), '	//blue
					+ 'color-stop(' + (1-val) + ', #d3d3db)'	//grey
					+ ')'
					);
				}else{
					$(this).css('background-image',
					'-webkit-gradient(linear, left top, right top, '
					+ 'color-stop(' + 0.5 + ', #d3d3db), '	//grey
					+ 'color-stop(' + 0.5 + ', rgb(45, 86, 153)), '	//blue
					+ 'color-stop(' + val + ', rgb(45, 86, 153)), '	//blue
					+ 'color-stop(' + val + ', #d3d3db)'	//grey
					+ ')'
					);
				}
			}

			$('span.'+$(this).attr('class')).html($(this).val());
		});
		
		$('input[type="range"]').each(function () {
			var val = ($(this).val() - $(this).attr('min')) / ($(this).attr('max') - $(this).attr('min'));

			if($(this).attr('max') == 10){
				$(this).css('background-image',
				'-webkit-gradient(linear, left top, right top, '
				+ 'color-stop(' + val + ', #2d5699), '
				+ 'color-stop(' + val + ', #d3d3db)'
				+ ')'
				);
			}else{			
				if($(this).val() <= 0){
					$(this).css('background-image',
					'-webkit-gradient(linear, right top, left top, '
					+ 'color-stop(' + 0.5 + ', #d3d3db), '	//grey
					+ 'color-stop(' + 0.5 + ', rgb(45, 86, 153)), '	//blue
					+ 'color-stop(' + (1-val) + ', rgb(45, 86, 153)), '	//blue
					+ 'color-stop(' + (1-val) + ', #d3d3db)'	//grey
					+ ')'
					);
				}else{
					$(this).css('background-image',
					'-webkit-gradient(linear, left top, right top, '
					+ 'color-stop(' + 0.5 + ', #d3d3db), '	//grey
					+ 'color-stop(' + 0.5 + ', rgb(45, 86, 153)), '	//blue
					+ 'color-stop(' + val + ', rgb(45, 86, 153)), '	//blue
					+ 'color-stop(' + val + ', #d3d3db)'	//grey
					+ ')'
					);
				}
			}
						
			$('span.'+$(this).attr('class')).html($(this).val());
		});
	</script>

	<?php
	//to here
}else{
	exit;
}
?>
