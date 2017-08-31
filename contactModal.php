<form action="/" id="contactForm">

	<?php
	include "connect.php";
	
	$result = mysqli_query($con, "SELECT * FROM subjects ORDER BY subject_name");
	$subjects = "<option disabled selected value style=\"display:none\"> -- Bitte wählen -- </option>";
	while($row = mysqli_fetch_assoc($result)){
		$subjects .= "<option value=\"".$row['ID']."\">".$row['subject_name']."</option>";
	}				
	
	$result = mysqli_query($con, "SELECT * FROM modules ORDER BY name");
	$modules = "<option disabled selected value style=\"display:none\"> -- Bitte wählen -- </option>";
	while($row = mysqli_fetch_assoc($result)){
		$modules .= "<option value=\"".$row['module_ID']."\">".$row['name']."</option>";
	}
	
	$result = mysqli_query($con, "SELECT * FROM lecturers ORDER BY last_name");
	$lecturers = "<option disabled selected value style=\"display:none\"> -- Bitte wählen -- </option>";
	while($row = mysqli_fetch_assoc($result)){
		$lecturers .= "<option value=\"".$row['lecturer_ID']."\">".$row['last_name'].", ".$row['first_name']."</option>";
	}
	
	$result = mysqli_query($con, "SELECT * FROM institutes ORDER BY name");
	$institutes = "<option disabled selected value style=\"display:none\"> -- Bitte wählen -- </option>";
	while($row = mysqli_fetch_assoc($result)){
		$institutes .= "<option value=\"".$row['institute_ID']."\">".$row['name']."</option>";
	}
	
	?>

	<div class="form-group">
		<label>Um was geht es denn?</label>
		<select id="reason" name="reason" class="form-control" required>
			<option disabled selected value style=\"display:none\"> -- Bitte wählen -- </option>
			<option value="bug">Ich möchte einen (funktionalen) Bugs melden</option>
			<option id="mistake" value="mistake">Ich möchte einen (inhaltlichen) Fehler melden</option>
			<option value="question">Ich möchte eine Frage stellen</option>
			<option value="feedback">Ich möchte Feedback/Verbesserungsvorschläge geben</option>
		</select>
	</div>
	
	<div id="area" style="display:none" class="form-group">
		<label>Wo findest sich der Fehler?</label>
		<select name="area" class="form-control" required>
			<option disabled selected value style="display:none"> -- Bitte wählen -- </option>
			<option value="subject">Veranstaltung</option>
			<option value="module">Modul</option>
			<option value="lecturer">Dozent</option>
			<option value="institute">Institut</option>
			<option value="other">Sonstige</option>
		</select>
	</div>				
	
	<div id="subject" style="display:none" class="form-group">
		<label>Welche Veranstaltung betrifft es?</label>
		<select name="subject_id" class="form-control" required>
			<?php echo $subjects ?>
		</select>
	</div>
	
	<div id="module" style="display:none" class="form-group">
		<label>Welches Modul betrifft es?</label>
		<select name="module_id" class="form-control" required>
			<?php echo $modules ?>
		</select>
	</div>
	
	<div id="lecturer" style="display:none" class="form-group">
		<label>Welchen Dozenten betrifft es?</label>
		<select name="lecturer_id" class="form-control" required>
			<?php echo $lecturers ?>
		</select>
	</div>
	
	<div id="institute" style="display:none" class="form-group">
		<label>Welches Institut betrifft es?</label>
		<select name="institute_id" class="form-control" required>
			<?php echo $institutes ?>
		</select>
	</div>
	
	<div name="answer" class="checkbox" style="display:none">
		<label id="answer"></label>
	</div>
	<!--
	Ich möchte informiert werden, wenn Fehler/Bug behoben wurde
	Frage: ausblenden
	Ich möchte gerne eine Antwort erhalten.-->
	
	<div class="form-group">
		<label>Kommentar:</label>
		<textarea name="comment" id="comment" class="form-control" rows="5" required></textarea>
	</div>

	<button id="submitButton" class="btn btn-primary">Nachricht abschicken</button>
</form>
	

<script>
$("#reason").change(function() {
	if($('#reason option:selected').val() == "bug"){
		$('.checkbox').show();
		$('#answer').html("<input name=\"answer\" type=\"checkbox\">Ich möchte informiert werden, wenn der Bug behoben wurde");
		$('#subject').attr("style", "display:none")
		$('#comment').attr("placeholder", "Bitte beschreibe möglichst genau, wie der Bug entsteht, indem du die einzelnen Schritte aufzählst, die zum Bug führen. Je genauer die Beschreibung, desto schneller können wir den Bug beheben!");
		$('#area').attr("style", "display:none");
		$('#subject').attr("style", "display:none");
		$('#module').attr("style", "display:none");
		$('#lecturer').attr("style", "display:none");
		$('#institute').attr("style", "display:none");
		$("#area :input").prop('required',null);
		$("#subject :input").prop('required',null);
		$("#module :input").prop('required',null);
		$("#lecturer :input").prop('required',null);
		$("#institute :input").prop('required',null);
	}
	
	if($('#reason option:selected').val() == "mistake"){
		$('.checkbox').show();
		$('#answer').html("<input name=\"answer\" type=\"checkbox\">Ich möchte informiert werden, wenn der Fehler behoben wurde");
		$('#comment').attr("placeholder", "");
		$('#area').attr("style", "")
		$("#area :input").prop('required');
		
		$("#area").change(function() {
			switch ($('#area option:selected').val()) {
				case "subject":
					$('#subject').attr("style", "");
					$('#module').attr("style", "display:none");
					$('#lecturer').attr("style", "display:none");
					$('#institute').attr("style", "display:none");
					$("#subject :input").prop('required');
					$("#module :input").prop('required',null);
					$("#lecturer :input").prop('required',null);
					$("#institute :input").prop('required',null);
					$('#comment').attr("placeholder", "");
					$("#subject").change(function() {
						$('#comment').attr("placeholder", "Was genau ist inhaltlich falsch bei der Veranstaltung \""+$('#subject option:selected').text()+"\"?");
					});
					break;
				case "module":
					$('#subject').attr("style", "display:none");
					$('#module').attr("style", "");
					$('#lecturer').attr("style", "display:none");
					$('#institute').attr("style", "display:none");
					$("#subject :input").prop('required',null);
					$("#module :input").prop('required');
					$("#lecturer :input").prop('required',null);
					$("#institute :input").prop('required',null);
					$('#comment').attr("placeholder", "");
					$("#module").change(function() {
						$('#comment').attr("placeholder", "Was genau ist inhaltlich falsch beim Modul \""+$('#module option:selected').text()+"\"?");
					});
					break;
				case "lecturer":
					$('#subject').attr("style", "display:none");
					$('#module').attr("style", "display:none");
					$('#lecturer').attr("style", "");
					$('#institute').attr("style", "display:none");
					$("#subject :input").prop('required',null);
					$("#module :input").prop('required',null);
					$("#lecturer :input").prop('required');
					$("#institute :input").prop('required',null);
					$('#comment').attr("placeholder", "");
					$("#lecturer").change(function() {
						$('#comment').attr("placeholder", "Was genau ist inhaltlich falsch bei Dozent \""+$('#lecturer option:selected').text()+"\"?");
					});
					break;
				case "institute":
					$('#subject').attr("style", "display:none");
					$('#module').attr("style", "display:none");
					$('#lecturer').attr("style", "display:none");
					$('#institute').attr("style", "");
					$("#subject :input").prop('required',null);
					$("#module :input").prop('required',null);
					$("#lecturer :input").prop('required',null);
					$("#institute :input").prop('required');
					$('#comment').attr("placeholder", "");
					$("#institute").change(function() {
						$('#comment').attr("placeholder", "Was genau ist inhaltlich falsch beim Institut \""+$('#institute option:selected').text()+"\"?");
					});
					break;
				case "other":
					$('#subject').attr("style", "display:none");
					$('#module').attr("style", "display:none");
					$('#lecturer').attr("style", "display:none");
					$('#institute').attr("style", "display:none");
					$("#subject :input").prop('required',null);
					$("#module :input").prop('required',null);
					$("#lecturer :input").prop('required',null);
					$("#institute :input").prop('required',null);
					$('#comment').attr("placeholder", "Wo genau hast du einen Fehler gefunden?");
					break;
			}
		});
	}
	
	if($('#reason option:selected').val() == "question"){
		$('.checkbox').hide();
		$('#answer').html("<input name=\"answer\" type=\"checkbox\">");
		$('#subject').attr("style", "display:none")
		$('#comment').attr("placeholder", "Wie können wir dir weiterhelfen?");
		$('#area').attr("style", "display:none");
		$('#subject').attr("style", "display:none");
		$('#module').attr("style", "display:none");
		$('#lecturer').attr("style", "display:none");
		$('#institute').attr("style", "display:none");
		$("#area :input").prop('required',null);
		$("#subject :input").prop('required',null);
		$("#module :input").prop('required',null);
		$("#lecturer :input").prop('required',null);
		$("#institute :input").prop('required',null);
	}
	
	if($('#reason option:selected').val() == "feedback"){
		$('.checkbox').show();
		$('#answer').html("<input name=\"answer\" type=\"checkbox\">Ich möchte gerne eine Antwort erhalten");
		$('#subject').attr("style", "display:none")
		$('#comment').attr("placeholder", "Was machen wir gut, was lässt sich noch verbessern? Welche Funktionen vermisst du? All das kann hier rein :)");
		$('#area').attr("style", "display:none");
		$('#subject').attr("style", "display:none");
		$('#module').attr("style", "display:none");
		$('#lecturer').attr("style", "display:none");
		$('#institute').attr("style", "display:none");
		$("#area :input").prop('required',null);
		$("#subject :input").prop('required',null);
		$("#module :input").prop('required',null);
		$("#lecturer :input").prop('required',null);
		$("#institute :input").prop('required',null);
	}
});

$("#submitButton").one('click', function(){
	$('#contactForm *').filter(':input').each(function(){ //Disable all hidden fields to prevent them to be posted
		if ($(this).css('display') == 'none'){
			$(this).prop('disabled', true);
		}
	});
	
	
	$("#contactForm").submit(function(e){
		$.ajax({
			type: "POST",
			url: "contact_submit.php",
			data: $("#contactForm").serialize(),
			success: function(data) {
				//alert(data);	
				if(data.trim() == "erfolg"){
					$('.modal-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Dein Anliegen wurde erfolgreich an uns übermittelt!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen</button>");
				}
			}
		});
		e.preventDefault();
	});
});
</script>