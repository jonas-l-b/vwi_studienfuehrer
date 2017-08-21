<?php include "header.php" ?>


<button type="button" a href="#contactModal" role="button" data-toggle="modal">Modal</button>
									

<div id="contactModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h2 class="modal-title">Kontakt</h2>
	</div>
		<div class="modal-body">
			<form action="contact_submit.php" method="POST">
			
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
					<select id="reason" class="form-control" required>
						<option disabled selected value style=\"display:none\"> -- Bitte wählen -- </option>
						<option value="bug">Ich möchte einen (funktionalen) Bugs melden</option>
						<option id="mistake" value="mistake">Ich möchte einen (inhaltlichen) Fehler melden</option>
						<option value="question">Ich möchte eine Frage stellen</option>
						<option value="feedback">Ich möchte Feedback/Verbesserungsvorschläge geben</option>
					</select>
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				</div>
				
				<div id="subject" style="display:none" class="form-group">
					<label>Welches Fach betrifft es?</label>
					<select class="form-control" required>
						<?php echo $subjects ?>
					</select>
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
				</div>
				
				<div class="checkbox">
					<label><input type="checkbox" value="yes">Ich möchte gerne eine Antwort erhalten.</label>
				</div>
				
				<div class="form-group">
					<label>Kommentar:</label>
					<textarea id="comment" class="form-control" rows="5" id="comment" required></textarea>
				</div>

				<button type="submit" class="btn btn-primary">Nachricht abschicken</button>
			</form>
		</div><!-- End of Modal body -->
	</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->


<script>
$("#reason").change(function() {
	if($('#reason option:selected').val() == "bug"){
		$('#subject').attr("style", "display:none")
		$('#comment').attr("placeholder", "Bitte beschreibe möglichst genau, wie der Bug entsteht, indem du die einzelnen Schritte aufzählst, die zum Bug führen. Je genauer die Beschreibung, desto schneller können wir den Bug beheben!");
		$("#subject :input").prop('required',null);
	}
	
	if($('#reason option:selected').val() == "mistake"){
		$('#subject').attr("style", "")
		$('#comment').attr("placeholder", "");
		$("#subject :input").prop('required');
		$("#subject").change(function() {
			$('#comment').attr("placeholder", "Was genau ist inhaltlich falsch bei \""+$('#subject option:selected').text()+"\"?");
		});
	}
	
	if($('#reason option:selected').val() == "question"){
		$('#subject').attr("style", "display:none")
		$('#comment').attr("placeholder", "Wie können wir dir weiterhelfen?");
		$("#subject :input").prop('required',null);
	}
	
	if($('#reason option:selected').val() == "feedback"){
		$('#subject').attr("style", "display:none")
		$('#comment').attr("placeholder", "Was machen wir gut, was lässt sich noch verbessern? Welche Funktionen vermisst du? All das kann hier rein :)");
		$("#subject :input").prop('required',null);
	}
});
</script>