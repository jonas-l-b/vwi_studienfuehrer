<!-- Added -->
				<?php
				$sql = "SELECT * FROM `ADDED_LECTURES`";
				$result = mysqli_query($con,$sql);
				?>
				
				<h4>
					<span>Hinzugekommene</span>
					<span style="margin-left:5px; margin-right:5px" class="badge badge-pill badge-primary"><?php echo mysqli_num_rows($result) ?></span>
					<a id="display_lectures_added"><span id="display_lectures_added_glyph" class="glyphicon glyphicon-chevron-down"></span></a>
				</h4>
				
				<table class="table table-striped" id="table_lectures_added" style="margin-top:15px; display:none">
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
				while($row = mysqli_fetch_assoc($result)){
					?>
					<tr>
						<td><?php echo $row['subject_name'] ?></td>
						<td><?php echo $row['identifier'] ?></td>
						<td><?php echo $row['ECTS'] ?></td>
						<td><?php echo $row['semester'] ?></td>
						<td><?php echo $row['language'] ?></td>
						<td>
							<button type="button" class="btn btn-default" data-toggle="modal" data-target="#editAddedSubjectModal"
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
						
						var modal = $(this)
						modal.find('.modal-body #id').val(id)
						modal.find('.modal-body #subject_name').val(subject_name)
						modal.find('.modal-body #identifier').val(identifier)
						modal.find('.modal-body #ECTS').val(ECTS)
						modal.find('.modal-body #semester').val(semester)
						modal.find('.modal-body #language').val(language)
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