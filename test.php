<h3>Module</h3>
				
				<!-- Changed -->
				<?php
				$sql = "
					SELECT modules.name AS name, CHANGED_MODULES.id AS id, CHANGED_MODULES.identifier AS identifier, CHANGED_MODULES.changed_value AS changed_field, CHANGED_MODULES.value_old AS value_old, CHANGED_MODULES.value_new AS value_new FROM `CHANGED_MODULES`
					JOIN modules ON CHANGED_MODULES.identifier = modules.code COLLATE utf8_unicode_ci
				";
				$result = mysqli_query($con,$sql);
				?>
				
				<h4>
					<span>Geänderte</span>
					<span style="margin-left:5px; margin-right:5px" class="badge badge-pill badge-primary"><?php echo mysqli_num_rows($result) ?></span>
					<a id="display_modules_changed"><span id="display_modules_changed_glyph" class="glyphicon glyphicon-chevron-down"></span></a>
				</h4>
				
				<table class="table table-striped table-bordered table-condensed update-table" id="table_modules_changed" style="margin-top:15px; display:none">
					<tr>
						<th>Name</th>
						<th>Kennung</th>
						<th>Geändertes Feld</th>
						<th>Alter Wert</th>
						<th>Neuer Wert</th>
						<th>Bearbeiten</th>
						<th>Ändern</th>
						<th>Löschen</th>
					</tr>
				
				<?php
				while($row = mysqli_fetch_assoc($result)){
					?>
					<tr>
						<td><?php echo $row['name'] ?></td>
						<td><?php echo $row['identifier'] ?></td>
						<td><?php echo $row['changed_field'] ?></td>
						<td><?php echo $row['value_old'] ?></td>
						<td><?php echo $row['value_new'] ?></td>
						<td>
							<button type="button" class="btn btn-default" data-toggle="modal" data-target="#editChangedModuleModal"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
								data-changed_field="<?php echo $row['changed_field'] ?>"
								data-value_old="<?php echo $row['value_old'] ?>"
								data-value_new="<?php echo $row['value_new'] ?>"
							>Bearbeiten</button>
						</td>
						<td>
							<button type="button" class="btn btn-primary changeModule_confirmButton"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
								data-identifier="<?php echo $row['identifier'] ?>"
								data-changed_field="<?php echo $row['changed_field'] ?>"
								data-value_old="<?php echo $row['value_old'] ?>"
								data-value_new="<?php echo $row['value_new'] ?>"
							>Änderung bestätigen</button>
						</td>
						<td>
							<button type="button" class="btn btn-danger changeModule_deleteButton"
								data-id="<?php echo $row['id'] ?>"
								data-name="<?php echo $row['name'] ?>"
							>Löschen</button>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
				
				<div class="modal fade" id="editChangedModuleModal" tabindex="-1" role="dialog" aria-labelledby="editChangedModuleModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-body" id="changedModule_edit_modal-body">
							<form id="changedModule_edit_form">
								<div class="form-group" style="display:none">
									<label class="col-form-label">ID:</label>
									<input type="text" class="form-control" name ="id" id="id">
								</div>
								<div class="form-group">
									<label class="col-form-label">Name:</label>
									<input type="text" class="form-control" name="name" id="name" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Kennung:</label>
									<input type="text" class="form-control" name="identifier" id="identifier" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Geändertes Feld:</label>
									<input type="text" class="form-control" name="changed_field" id="changed_field" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Alter Wert:</label>
									<input type="text" class="form-control" name="value_old" id="value_old" disabled>
								</div>
								<div class="form-group">
									<label class="col-form-label">Neuer Wert:</label>
									<input type="text" class="form-control" name="value_new" id="value_new">
								</div>
							</form>
						</div>
						<div class="modal-footer" id="changedModule_edit_modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
							<button type="button" class="btn btn-primary" id="module-changed_save-changes-button">Änderungen speichern</button>
						</div>
					</div>
				  </div>
				</div>
				
				<script>
				$( document ).ready(function() {
					//Show and hide
					$("#display_modules_changed").click(function() {
						if($("#table_modules_changed").is(":visible")){
							$("#table_modules_changed").hide();
							$("#display_modules_changed_glyph").removeClass("glyphicon glyphicon-chevron-up");
							$("#display_modules_changed_glyph").addClass("glyphicon glyphicon-chevron-down");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=module_changed&value=0"});
						}else{
							$("#table_modules_changed").show();
							$("#display_modules_changed_glyph").removeClass("glyphicon glyphicon-chevron-down");
							$("#display_modules_changed_glyph").addClass("glyphicon glyphicon-chevron-up");
							$.ajax({type: "POST", url: "admin_update_visibility.php", data: "name=module_changed&value=1"});
						}
					});
					
					//show modal
					$('#editChangedModuleModal').on('show.bs.modal', function (event) {
						var button = $(event.relatedTarget) // Button that triggered the modal
						
						var id = button.data('id')
						var name = button.data('name')
						var identifier = button.data('identifier')
						var changed_field = button.data('changed_field')
						var value_old = button.data('value_old')
						var value_new = button.data('value_new')
						
						var modal = $(this)
						modal.find('.modal-body #id').val(id)
						modal.find('.modal-body #name').val(name)
						modal.find('.modal-body #identifier').val(identifier)
						modal.find('.modal-body #changed_field').val(changed_field)
						modal.find('.modal-body #value_old').val(value_old)
						modal.find('.modal-body #value_new').val(value_new)
					})
					
					//save changes
					$('#module-changed_save-changes-button').click(function() {
						$.ajax({
							type: "POST",
							url: "admin_updateModuleChanged_edit_submit.php",
							data: $("#changedModule_edit_form").serialize(),
							success: function(data) {
								//alert(data);
								if(data.includes("erfolg")){
									$('#changedModule_edit_modal-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Änderungen erfolgreich gespeichert!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" onClick=\"window.location.reload()\">Schließen & Seite neu laden</button>");
								}else{
									$('#changedModule_edit_modal-body').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Beim Speichern ist womöglich ein Fehler aufgetreten! Bitte probiere es erneut (oftmals liegt es am Server, sodass es beim zweiten Mal klappt).</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen & Seite neu laden</button>");
								}
								$('#changedModule_edit_modal-footer').hide();
							}
						});
					});
					
					//Change module
					$('.changeModule_confirmButton').click(function(){
						
						var id = $(this).data('id')
						var name = $(this).data('name')
						var identifier = $(this).data('identifier')
						var changed_field = $(this).data('changed_field')
						var value_old = $(this).data('value_old')
						var value_new = $(this).data('value_new')

						var result = confirm('Bei Modul "'+name+'" wirklich den Wert von '+changed_field+' von '+value_old+' zu '+value_new+' ändern?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateModuleChanged_confirm_submit.php",
								data: "id=" + id + "&identifier=" + identifier + "&changed_field=" + changed_field + "&value_new=" + value_new,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Nichts wurde geändert.");
						}
					});
					
					//Delete subject
					$('.changeModule_deleteButton').click(function(){
						var id = $(this).data('id')
						var name = $(this).data('name')
						
						var result = confirm('Änderungen bei Modul "' + name + '" wirklich löschen?');
						if(result){
							$.ajax({
								type: "POST",
								url: "admin_updateModuleChanged_delete_submit.php",
								data: "id=" + id,
								success: function(data) {
									alert(data);
									window.location.reload(true);
								}
							});
						}else{
							alert("Modul wurde nicht gelöscht.");
						}
					});
				});
				</script>