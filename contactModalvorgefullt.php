<form action="/" id="contactForm">


<?php
include "connect.php";

$result = mysqli_query($con, "SELECT * FROM subjects ORDER BY subject_name");
$subjects = "<option disabled selected value style=\"display:none\"> -- Bitte wählen -- </option>";
while($row = mysqli_fetch_assoc($result)){
	$subjects .= "<option value=\"".$row['ID']."\">".$row['subject_name']."</option>";
}

 ?>

	<div class="form-group">
		<label>Um was geht es denn?</label>
		<select id="reason" name="reason" class="form-control" required>
			<option id="mistake" value="mistake">Ich möchte einen (inhaltlichen) Fehler melden</option>
		</select>
	</div>

	<div id="area" style="display:none" class="form-group">
		<label>Wo findest sich der Fehler?</label>
		<select name="area" id="select_area" class="form-control" required>
			<option value="subject">Veranstaltung</option>
		</select>
	</div>

	<div id="subject" style="display:none" class="form-group">
		<label>Welche Veranstaltung betrifft es?</label>
		<select name="subject_id" id="select_subject" class="form-control" required>
			<?php echo $subjects; ?>
		</select>
	</div>

	<div name="answer" class="checkbox" style="display:none">
		<label id="answer"></label>
	</div>

	<div class="form-group">
		<label>Kommentar:</label>
		<textarea name="comment" id="comment" class="form-control" rows="5" maxlength="5000" placeholder="Maximal 5000 Zeichen" required></textarea>
	</div>

	<p id="commentWarning"></p>

	<script>
	$('#comment').on("propertychange input textInput", function() {
		if($('#comment').val().length < 4500){
			$('#commentWarning').html("");
		}else if($('#comment').val().length >= 4500 && $('#comment').val().length < 4900){
			$('#commentWarning').css('color', 'black');
			$('#commentWarning').html("Noch " + (5000 - $('#comment').val().length) + " Zeichen übrig");
		}else{
			$('#commentWarning').css('color', 'red');
			$('#commentWarning').html("Noch " + (5000 - $('#comment').val().length) + " Zeichen übrig");
		}
	});
	</script>

	<button id="submitButton" class="btn btn-primary">Nachricht abschicken</button>
</form>


<script>

$("#submitButton").one('click', function(){

	$('#reason').attr("disabled", false);
	$('#select_area').attr("disabled", false);
	$('#select_subject').attr("disabled", false);

	$("#contactForm").submit(function(e){
		console.log($("#contactForm").serialize());
		$.ajax({
			type: "POST",
			url: "contact_submit.php",
			data: $("#contactForm").serialize(),
			success: function(data) {
				//alert(data);
				if(data.trim().substr(0,6) == "erfolg"){ //substring stellt sicher, dass hier auch reingegangen wird wenn E-Mail-Fehler auftritt
					$('.modal-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Dein Anliegen wurde erfolgreich an uns übermittelt!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" onClick=\"window.location.reload()\">Schließen</button>");
				}else{
				$('.modal-body').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Bei der Übermittlung Deines Anliegens ist womöglich ein Fehler aufgetreten!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen</button>");
				}
			}
		});
		e.preventDefault();
	});
});
</script>
