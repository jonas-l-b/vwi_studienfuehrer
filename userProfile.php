<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div class="container" style="margin-top:60px">
	
	<?php
	$displayShow = "";
	$displayEdit = "style=\"display:none\"";
	
	//Müssen vorher schon gezogen werden, damit sie beim ÄNDERN geändert werden können; sonst werden Änderungen nicht direkt angezeigt
	$u_degree = $userRow['degree'];
	$u_advance = $userRow['advance'];
	$u_semester = $userRow['semester'];
	
	if (isset($_POST['btn-edit'])){
		$displayShow = "style=\"display:none\"";
		$displayEdit = "";
	}
	
	if (isset($_POST['btn-save'])){
		$displayShow = "";
		$displayEdit = "style=\"display:none\"";
		
		//Daten aus Form ziehen
		$degree = strip_tags($_POST['degree']);
		$advance = strip_tags($_POST['advance']);
		$semester = strip_tags($_POST['semester']);
		
		$q1 = mysqli_query($con,"
			UPDATE users
			SET degree = '".$degree."', advance = '".$advance."', semester = '".$semester."'
			WHERE user_ID = '".$userRow['user_ID']."'
		");
		
		if($q1==true){
			$msg = "
				<div class='alert alert-success'>
					<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Die Änderungen wurden erfolgreich gespeichert.
				</div>
			";
			
			//Hier ändern, damit Änderungen direkt nach Speichern angezeigt werden (nicht erst nach refresh)
			$u_degree = $degree;
			$u_advance = $advance;
			$u_semester = $semester;
		}
	}
	
	if (isset($_POST['btn-cancel'])){
		$displayShow = "";
		$displayEdit = "style=\"display:none\"";
	}
	?>

	<h2><?php echo $userRow['first_name']." ".$userRow['last_name']?></h2>
	<br>
	
	<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" href="#userData">Profil</a></li>
		<li><a data-toggle="tab" href="#favourites">Favoriten</a></li>
	<!--<li><a data-toggle="tab" href="#menu2">Menu 2</a></li>
		<li><a data-toggle="tab" href="#menu3">Menu 3</a></li>-->
	</ul>

	<div class="tab-content">
		<div id="userData" class="tab-pane fade in active">
			<br>
			<?php if(isset($msg)) echo $msg?>
			<div <?php echo $displayShow?>>
				<table class="table" style="border-top:solid; border-top-color:white">
					<tbody>
						<tr>
							<th>Benutzername:</th>
							<td><?php echo $userRow['username']?></td>
						</tr>
						<tr>
							<th>E-Mail:</th>
							<td><?php echo $userRow['email']?></td>
						</tr>
						<tr>
							<th>Studiengang:</th>
							<td><?php echo $u_degree?></td>
						</tr>
						<tr>
							<th>Fortschritt:</th>
							<td><?php echo ucfirst($u_advance)?></td>
						</tr>
						<tr>
							<th>Fachsemester:</th>
							<td><?php echo $u_semester?></td>
						</tr>
					</tbody>
				</table>
				<form method="post">
					<button type="submit" class="btn btn-primary" name="btn-edit">Daten bearbeiten</button>
					<button type="button" href="#myModal" role="button" class="btn btn-primary" data-toggle="modal">Passwort ändern</button>
					<button style="float:right;" type="button" id="deleteProfileButton" role="button" class="btn btn-danger">Profil löschen</button>
				</form>
			</div>
			
			<div <?php echo $displayEdit?>>
				<form method="post">
					<table class="table dataChangeTable" style="border-top:solid; border-top-color:white">
						<tbody>
							<tr>
								<th>Studiengang:</th>
								<td>
									<div class="form-group">
										<input value="<?php echo $userRow['degree']?>" name="degree" type="text" class="form-control" placeholder="Studiengang" required />
									</div>
								</td>
							</tr>
							<tr>
								<th>Fortschritt:</th>
								<td>
									<div class="form-group">
										<select name="advance" class="form-control" required>
											<option value="bachelor" <?php if (ucfirst($userRow['advance'])=="Bachelor") echo "selected"?>>Bachelor</option>
											<option value="master" <?php if (ucfirst($userRow['advance'])=="Master") echo "selected"?>>Master</option>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<th>Fachsemester:</th>
								<td>
									<div class="form-group">
										<input value="<?php echo $userRow['semester']?>" type="number" max="18" min="1" step="1" name="semester" type="text" class="form-control" placeholder="Fachsemester" required />
									</div>
								</td>
							</tr>
						</tbody>
					</table>
					
					<button type="submit" class="btn btn-primary" name="btn-save">Änderungen speichern</button>
					<button type="submit" class="btn btn-primary" name="btn-cancel">Abbrechen</button>
				</form>
			</div>
		</div>
		
		<div id="favourites" class="tab-pane fade">
			<br>
			
			<?php
			$sql="
				SELECT DISTINCT modules.type AS module_type
				FROM favourites
				JOIN subjects on favourites.subject_id = subjects.ID
				JOIN subjects_modules on subjects.ID = subjects_modules.subject_ID
				JOIN modules on subjects_modules.module_ID = modules.module_id
				WHERE favourites.user_id = '".$userRow['user_ID']."'
				ORDER BY modules.type
			";			
			$result = mysqli_query($con, $sql);
			
			while($modules = mysqli_fetch_assoc($result)){
				echo ("<h4>".$modules['module_type']."</h4>");
				
				$sql2="
					SELECT DISTINCT subjects.ID AS subject_id, subject_name, subjects.code AS subject_code, modules.type AS module_type, modules.name AS module_name, modules.module_id AS module_id
					FROM favourites
					JOIN subjects on favourites.subject_id = subjects.ID
					JOIN subjects_modules on subjects.ID = subjects_modules.subject_ID
					JOIN modules on subjects_modules.module_ID = modules.module_id
					WHERE favourites.user_id = '".$userRow['user_ID']."' AND modules.type = '".$modules['module_type']."'
					ORDER BY subject_name, subjects.ID
				";
				$result2 = mysqli_query($con, $sql2);
						
				$i = 1;
				
				while($subject = mysqli_fetch_assoc($result2)){
					if($i==1){
						$help[$i][1] = $subject['subject_id'];
						$help[$i][2] = $subject['subject_code'];
						$help[$i][3] = $subject['subject_name'];
						$help[$i][4] = "<a href=\"module.php?module_id=".$subject['module_id']."\">".$subject['module_name']."</a>";
						
						$i++;
					} elseif($subject['subject_id'] != $help[$i-1][1]){
						$help[$i][1] = $subject['subject_id'];
						$help[$i][2] = $subject['subject_code'];
						$help[$i][3] = $subject['subject_name'];
						$help[$i][4] = "<a href=\"module.php?module_id=".$subject['module_id']."\">".$subject['module_name']."</a>";

						$i++;	
					}elseif($subject['subject_id'] == $help[$i-1][1]){ //Fügt Modul der vorangegangenen Veranstaltung zu anstatt Veranstaltung erneut zu listen
						$help[$i-1][4] = $help[$i-1][4].", <a href=\"module.php?module_id=".$subject['module_id']."\">".$subject['module_name']."</a>";
					}
				}
				
				for($j=1; $j<$i; $j++){
					?>
					<p>
					<span id="<?php echo $help[$j][1]?>" style="color:rgb(255, 204, 0)" class="glyphicon glyphicon-star favouriteStar"></span>
					<a href="index.php?subject=<?php echo $help[$j][2]?>"><?php echo $help[$j][3]?></a>
					(<?php echo $help[$j][4]?>)
					</p>
					<?php
				}
					
				$i=1;
				//unset($help);			
			}
			?>
			
			<script>
			$(document).ready(function(){
				var numberOfSnackbars = 0;
				$(".favouriteStar").click(function(){
					var tempNumSnack = numberOfSnackbars++;
					var g=document.createElement('div');
					g.className='snackbar';
					g.setAttribute("id", "snackbarNumero" + tempNumSnack);
					$('body').append(g);
					var link = $(this).next();
					if($(this).attr("class") == "glyphicon glyphicon-star-empty favouriteStar"){
						$(this).attr("style", "color:rgb(255, 204, 0)");
						$(this).attr("class", "glyphicon glyphicon-star favouriteStar");
						$.post( "favourites_newEntry.php", {user_id: "<?php echo $userRow['user_ID'] ?>", subject_id: this.id} )
						.done(function() {
							$('#snackbarNumero' + tempNumSnack).text('Die Veranstaltung '+link.text()+' wurde wieder zu deinen Favoriten hinzugefügt.').addClass('show');
							setTimeout(function(){ $('#snackbarNumero' + tempNumSnack).removeClass('show'); }, 3000);
						  })
						  .fail(function() {
							$(this).attr("style", "color:grey");
							$(this).attr("class", "glyphicon glyphicon-star-empty favouriteStar");
							$('#snackbarNumero' + tempNumSnack).text('Die Veranstaltung '+link.text()+' konnte nicht wieder zu deinen Favoriten hinzugefügt werden.').addClass('show');
							setTimeout(function(){ $('#snackbarNumero' + tempNumSnack).removeClass('show'); }, 3000);
						  });
					} else{
						$(this).attr("style", "color:grey");
						$(this).attr("class", "glyphicon glyphicon-star-empty favouriteStar");
						$.post( "favourites_removeEntry.php", {user_id: "<?php echo $userRow['user_ID'] ?>", subject_id: this.id} )
						.done(function() {
							$('#snackbarNumero' + tempNumSnack).text('Die Veranstaltung '+link.text()+' wurde erfolgreich aus deinen Favoriten entfernt.').addClass('show');
							setTimeout(function(){ $('#snackbarNumero' + tempNumSnack).removeClass('show'); }, 3000);
						  })
						 .fail(function() {
							$(this).attr("style", "color:rgb(255, 204, 0)");
							$(this).attr("class", "glyphicon glyphicon-star favouriteStar");
							$('#snackbarNumero' + tempNumSnack).text('Die Veranstaltung '+link.text()+' konnte nicht aus deinen Favoriten entfernt werden.').addClass('show');
							setTimeout(function(){ $('#snackbarNumero' + tempNumSnack).removeClass('show'); }, 3000);
						});
					}
				});
			});
			</script>
		</div>	
			

		
			
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
});

$('#linkToUserFavorites').click(function(event){
	$('.nav-tabs a[href="#favourites"]').tab('show')
});
$('#linkToUserProfile').click(function(event){
	event.preventDefault();
	$('.nav-tabs a[href="#userData"]').tab('show')
});
</script>

<!-- End of page. Modal für Passwort vergessen -->
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h2 class="modal-title">Passwort ändern</h2> <!-- Dynamisch Name anpassen! -->
	</div>
	<div class="modal-body">
		<p id="pwrecovery"></p>
			<form action="recoverPW.php" method="POST">
				<p>Mit einem Klick auf den Button schicken wir dir eine E-Mail an die Adresse, mit der du dich registriert hast. Mit ihr kannst du dein Passwort ändern.</p>
				<br>
				
				<div class="form-group" style="display:none">
					<input value="<?php echo $u_email?>" type="email" class="form-control" name="email" />
					<input value="<?php echo "change"?>" class="form-control" name="recoverType" />
				</div>
			
				<button type="submit" class="btn btn-primary" >E-Mail zuschicken</button>
			</form>
		
		
		</div><!-- End of Modal body -->
	</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->


<!-- End of page. Modal für Profil löschen -->
<div id="deleteProfileModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" id="deleteModalClose" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h2 class="modal-title">Profil löschen</h2> <!-- Dynamisch Name anpassen! -->
		</div>
		<div id="deleteModalBody" class="modal-body">
		
		</div><!-- End of Modal body -->
	</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<script>
	$(document).ready(function() {
		var deleteLaden = function(){
				$('#deleteProfileModal').modal('show');
				insertLoader('#deleteModalBody');
				$('#deleteModalBody').load("delete-user-api.php?getDeleteModal=true", function( response, status, xhr ) {
				  if ( status == "error" ) {
					$('#deleteModalBody').html('<strong>Daten können nicht geladen werden. Bitte versuche es erneut.</strong>');
				  }
				});
		}
		$('#deleteProfileButton').click(deleteLaden);
	});
</script>
</body>
</html>
