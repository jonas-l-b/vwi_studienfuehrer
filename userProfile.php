<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>

<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

<?php include "inc/nav.php" ?>

<?php
//Badges: Freunde geworben
$sql="SELECT COUNT(user_ID) AS count FROM `users` WHERE advertised_by = ".$userRow['user_ID']." AND active = 1";
$result=mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

$counts = array(1,2,3);
$badges = array(93,94,95);

for ($i = 0; $i <= count($counts)-1; $i++) {
	if($row['count'] >= $counts[$i]){ //Wenn genügend Werbungen vorhanden
		$result2 = mysqli_query($con, "SELECT * FROM users_badges WHERE user_id = '".$userRow['user_ID']."' AND badge_id = '$badges[$i]'");
		if(mysqli_num_rows($result2) == 0){ //Wenn badge noch nicht vorhanden
			$sql2="INSERT INTO `users_badges`(`user_id`, `badge_id`) VALUES (".$userRow['user_ID'].",'$badges[$i]')";
			if ($con->query($sql2) == TRUE) {
				echo "<script>alert(\"Du hast eine neue Errungenschaft für das erfolgreiche Werben eines Kommilitonenden freigeschaltet! Schau gleich nach unter Profil > Errungenschaften.\");</script>";
			}
		}
	}
}

//Badges: Upvotes gesammelt
$sql="
	SELECT count(commentratings.ID) AS count FROM `commentratings`
	JOIN ratings ON commentratings.comment_ID = ratings.ID
	WHERE comment_ID = ANY (SELECT ID FROM `ratings` WHERE user_ID = ".$userRow['user_ID'].") AND rating_direction = 1
";
$result=mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

$counts = array(15,30,50);
$badges = array(99,100,101);

for ($i = 0; $i <= count($counts)-1; $i++) {
	if($row['count'] >= $counts[$i]){ //Wenn genügend Upvotes vorhanden
		$result2 = mysqli_query($con, "SELECT * FROM users_badges WHERE user_id = '".$userRow['user_ID']."' AND badge_id = '$badges[$i]'");
		if(mysqli_num_rows($result2) == 0){ //Wenn badge noch nicht vorhanden
			$sql2="INSERT INTO `users_badges`(`user_id`, `badge_id`) VALUES (".$userRow['user_ID'].",'$badges[$i]')";
			if ($con->query($sql2) == TRUE) {
				echo "<script>alert(\"Du hast eine neue Errungenschaft für das Sammeln von Upvotes freigeschaltet!\");</script>";
			}
		}
	}
}
?>

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
		<li class="active"><a data-toggle="tab" href="#userData">Mein Profil</a></li>
		<li><a data-toggle="tab" href="#favourites">Meine Favoriten</a></li>
		<li><a data-toggle="tab" href="#userRatings">Meine Bewertungen</a></li>
		<li><a data-toggle="tab" href="#questions">Meine Fragen</a></li>
		<li><a data-toggle="tab" href="#notifications">Meine Benachrichtigungen</a></li>
		<li><a data-toggle="tab" href="#achievements">Meine Errungenschaften</a></li>
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
					<button type="button" href="#changePasswordModal" role="button" class="btn btn-primary" data-toggle="modal">Passwort ändern</button>
					<button style="float:right;" type="button" id="deleteProfileButton" role="button" class="btn btn-danger<?php if($userRow['admin']!='0') echo ' disabled" data-toggle="tooltip" title="Nur `normale` Nutzer können ihr Profil löschen. Gibt zunächst deine Admin-Rechte ab.'; ?>">Profil löschen</button>
				</form>
			</div>

			<div <?php echo $displayEdit?>>
				<form method="post">
					<table class="table dataChangeTable" style="border-top:solid; border-top-color:white">
						<tbody>
							<tr>
								<th>Studiengang:</th>
								<td>

									<div class="form-group has-feedback">
										<div class="ui dropdown">
										  <input data-error="Gib deinen Studiengang ein!" required class="form-control" type="hidden" name="degree">
										  <i class="dropdown icon"></i>
										  <div class="default text">Studiengang</div>
										  <div class="menu">
										    <div class="item" data-value="Wirtschaftsingenieurwesen">Wirtschaftsingenieurwesen</div>
										    <div class="item" data-value="Technische Volkswirtschaftslehre">Technische Volkswirtschaftslehre</div>
												<div class="item" data-value="Informationswirtschaft">Informationswirtschaft</div>
												<div class="item" data-value="Wirtschaftsmathematik">Wirtschaftsmathematik</div>
												<div class="item" data-value="Sonstige">Sonstige</div>
										  </div>
										</div>
										<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
										<div class="help-block with-errors"></div>
									</div>

									<script>
									$('.ui.dropdown')
										.dropdown(
											<?php
														if($userRow['degree'] == 'Wirtschaftsingenieurwesen' || $userRow['degree'] == 'Technische Volkswirtschaftslehre' || $userRow['degree'] == 'Informationswirtschaft' || $userRow['degree'] == 'Wirtschaftsmathematik' || $userRow['degree'] == 'Sonstige' )
																{
																	echo "'set selected','". $userRow['degree']."'"; 
																}
																?>
										)
										;
									</script>
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
			<p>Diese Veranstaltungen hast du als Favoriten markiert. Um die Markierung zu entfernen, klicke auf den jeweiligen Stern.</p>
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

			//Hinweis, falls noch keine Favoriten hinzugefügt
			if(mysqli_num_rows($result) == 0){
				echo "<i><p>Du hast noch keine Objekte zu deinen Favoriten hinzugefügt.</p><p>Navigiere bspw. auf die Seite einer Veranstaltung und klicke auf den Stern oben rechts, um sie deinen Favoriten hinzuzufügen.</p></i>";
			}

			while($modules = mysqli_fetch_assoc($result)){
				echo ("<h4>".$modules['module_type']."</h4>");

				$sql2="
					SELECT DISTINCT subjects.ID AS subject_id, subject_name, modules.type AS module_type, modules.name AS module_name, modules.module_id AS module_id
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
						$help[$i][2] = $subject['subject_name'];
						$help[$i][3] = "<a href=\"module.php?module_id=".$subject['module_id']."\">".$subject['module_name']."</a>";

						$i++;
					} elseif($subject['subject_id'] != $help[$i-1][1]){
						$help[$i][1] = $subject['subject_id'];
						$help[$i][2] = $subject['subject_name'];
						$help[$i][3] = "<a href=\"module.php?module_id=".$subject['module_id']."\">".$subject['module_name']."</a>";

						$i++;
					}elseif($subject['subject_id'] == $help[$i-1][1]){ //Fügt Modul der vorangegangenen Veranstaltung zu anstatt Veranstaltung erneut zu listen
						$help[$i-1][3] = $help[$i-1][3].", <a href=\"module.php?module_id=".$subject['module_id']."\">".$subject['module_name']."</a>";
					}
				}

				for($j=1; $j<$i; $j++){
					?>
					<p>
					<span id="<?php echo $help[$j][1]?>" style="color:rgb(255, 204, 0)" title="Klicken, um als Favorit an- oder abzuwählen" class="glyphicon glyphicon-star favouriteStar"></span>
					<a href="index.php?subject=<?php echo $help[$j][1]?>"><?php echo $help[$j][2]?></a>
					(<?php echo $help[$j][3]?>)
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




		<div id="userRatings" class="tab-pane fade">
			<br>
			<p>Folgende Veranstaltungen hast du bereits bewertet:</p>

			<?php
			$result = mysqli_query($con, "
				SELECT subject_name, subjects.ID AS subject_id
				FROM ratings
				JOIN subjects ON subjects.ID = ratings.subject_ID
				WHERE ratings.user_ID = ".$userRow['user_ID']."
			");
			?>

			<ol class="container">
			<?php
			while($row = mysqli_fetch_assoc($result)){
				echo "<li><a href=\"index.php?subject=".$row['subject_id']."\">".$row['subject_name']."</a></li>";
			}
			?>
			</ol>

		</div>
		
		<div id="questions" class="tab-pane fade">
		
			<br>
			<p>Diese Fragen hast du gestellt. Im Reiter "Meine Benachrichtigungen" kannst du einstellen, ob du per Mail benachrichtigt werden willst, wenn jemand deine Frage beantwortet.</p>
		
			<br><br>
		


				<div id="questionBody">

					<?php
					$sql = "
						SELECT questions.ID AS ID, questions.subject_ID AS subject_ID, questions.user_ID AS user_ID, questions.question AS question, questions.time_stamp AS time_stamp, users.username AS username, subject_name
						FROM questions
						JOIN users ON questions.user_ID = users.user_ID
						JOIN subjects ON questions.subject_ID = subjects.ID
						WHERE questions.user_ID = ".$userRow['user_ID']."
						ORDER BY time_stamp DESC;
					";
					$result = mysqli_query($con, $sql);

					if(mysqli_num_rows($result)==0){
						echo "<i>Noch keine Fragen vorhanden.</i>";
					}

					while($row = mysqli_fetch_assoc($result)){
						?>
						<div class="well" style="background-color:white; border-radius:none">
							<span class="actualQuestion" id="question<?php echo $row['ID']?>"><?php echo $row['question']?></span>
							<hr style="margin:10px">
							<p style="font-size:10px">Gestellt in: <a href="index.php?subject=<?php echo $row['subject_ID']?>"><?php echo $row['subject_name']?></a> &#124; <?php echo time_elapsed_string($row['time_stamp']);?></p>

							<?php
							$num = mysqli_num_rows(mysqli_query($con, "SELECT * FROM answers WHERE question_ID = ".$row['ID']));
							?>
							<p style="margin-bottom:0px">
								<span class="showAnswers">
									<?php
									switch($num){
										case 0:
											echo "Keine Antworten zum Anzeigen vorhanden";
											break;
										default:
											echo "<a>Antworten anzeigen</a>";
											break;
									}
									?>
								</span>
							</p>

							<div class="answerSection" style="display:none"> <!--Antworten-->
								<hr class="style">

								<?php
								$sql2 = "
									SELECT answers.ID AS ID, answers.question_ID AS question_ID, answers.user_ID AS user_ID, answers.answer AS answer, answers.time_stamp AS time_stamp, users.username AS username
									FROM answers
									JOIN users ON answers.user_ID = users.user_ID
									WHERE question_ID = ".$row['ID']."
									ORDER BY time_stamp DESC;
								";
								$result2 = mysqli_query($con, $sql2);

								while($row2 = mysqli_fetch_assoc($result2)){
									?>
									<div class="well" style="background-color:white; border-radius:none; margin-bottom:5px; margin-left:3%">
										<?php echo $row2['answer']?>
										<hr style="margin:10px">
										<p style="font-size:10px; margin-bottom:0px"><?php echo $row2['username']?> &#124; <?php echo time_elapsed_string($row2['time_stamp']);?></p>
									</div>
									<?php
								}
								?>
							</div>
						</div>
						<?php
					}
					?>
				</div>		
		
				<script>
				//Antworten anzeigen
				$('.showAnswers').click(function() {
					if(!($(this).text().trim() == "Keine Antworten zum Anzeigen vorhanden")){
						if(($(this).text().trim() == "Schließen")){
							$(this).parent().next(".answerSection").hide(); //Bin nicht ganz sicher, wie stabil das ist
							$(this).html("<a>Antworten anzeigen</a>");
							if($('#questionBody').hasScrollBarIH()){
								$('#showAllQuestions').show();
							}else if($('#showAllQuestions').text()!="Fragen wieder einklappen"){
								$('#showAllQuestions').hide();
							}
						}else{
							$(this).parent().next(".answerSection").show(); //Bin nicht ganz sicher, wie stabil das ist
							$(this).html("<a>Schließen</a>");
							if($('#questionBody').hasScrollBarIH()){
								$('#showAllQuestions').show();
							}else if($('#showAllQuestions').text()!="Fragen wieder einklappen"){
								$('#showAllQuestions').hide();
							}
						}
					}
				});
				</script>
		
		</div>
		
		<div id="notifications" class="tab-pane fade">
			<br>
			<p>Hier kannst du einstellen, bei welchen Ereignissen du per Mail benachrichtigt werden willst. Vergiss nicht, Änderungen durch den Klick auf den Button zu speichern.</p>
			<p>Leider landen unserer E-Mails oft im <strong>SPAM-Ordner</strong>. Bitte überprüfe ihn und füge <strong>noreply@studienführer.vwi-karlsruhe.de</strong> zu deinen Ausnahmen hinzu!</p>
			
			<hr>
			
			<?php //Script für Datenbankänderung		
			if(isset($_POST['btn-change-questions'])) {
				
				//Änderung durchführen
				if(isset($_POST['own_questions']) && $_POST['own_questions'] == '1') {
					$changedQuestionValue = 1;
				}else{
					$changedQuestionValue = 0;
				}
				
				if(isset($_POST['user_messages']) && $_POST['user_messages'] == '1') {
					$changedUserMessagesValue = 1;
				}else{
					$changedUserMessagesValue = 0;
				}
				
				if(isset($_POST['vwi_newsletter']) && $_POST['vwi_newsletter'] == '1') {
					$changedVwiNewsletter = 1;
				}else{
					$changedVwiNewsletter = 0;
				}

				$sql="
					UPDATE user_notifications
					SET own_questions = ".$changedQuestionValue.", user_messages = ".$changedUserMessagesValue.", vwi_newsletter = ".$changedVwiNewsletter."
					WHERE user_id = ".$userRow['user_ID'].";
				";
				
				if(mysqli_query($con, $sql)){
					$msg_questions = "<div class='alert alert-success'>
					<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Änderungen erfolgreich gespeichert!
					</div>";					
				}else{
					$msg_questions = "<div class='alert alert-danger'>
					<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Beim Versuch, deine Änderungen zu speichern, ist ein Fehler aufgetreten; Bitte versuche es erneut. Falls es weiterhin nicht klappen sollte, wende Dich bitte an studienfuehrer@vwi-karlsruhe.de.
					</div>";						
				}
			}
			?>
			
			<?php if (isset($msg_questions)) echo $msg_questions;?>
			<form method="post">
				<?php
					/*Wird mittlerweile auf tree.php behandelt. Code ist lediglich sicherheitshalber immer noch hier*/
					//Check, ob Zeile in user_notifications bereits vorhanden ist (und ggfls. erstellen)
					$sql="SELECT * FROM user_notifications WHERE user_id = ".$userRow['user_ID']."";
					$result = mysqli_query($con, $sql);
					if(mysqli_num_rows($result)==0){ //Zeile für Benutzer anlegen falls noch nicht vorhanden
						//Get value for vwi_newsletter
						$row = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE user_ID = ".$userRow['user_ID'].""));
						if($row['info'] == "yes"){
							$vwi_newsletter_value = 1;
						}else{
							$vwi_newsletter_value = 0;
						}
						$sql1="
							INSERT INTO user_notifications (user_id, own_questions, user_messages, vwi_newsletter)
							VALUE (".$userRow['user_ID'].", 1, 1, $vwi_newsletter_value)
						";
						mysqli_query($con, $sql1);
					}elseif(mysqli_num_rows($result)>1){
						echo "<div class='alert alert-danger'>
						<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Es ist ein Problem in der Datenbank aufgetreten (Fehler: Mehrere Einträge in Datenbank). Bitte wende dich an studienfuehrer@vwi-karlsruhe.de.
						</div>";
						exit();
					}
					/*(Bis hier auf tree.php*/
					
					$sql="SELECT * FROM user_notifications WHERE user_id = ".$userRow['user_ID']."";
					$result = mysqli_query($con, $sql);
					$row = mysqli_fetch_assoc($result);
					if($row['own_questions']=="1"){
						$check_own_questions = "checked";
					}
					if($row['user_messages']=="1"){
						$check_user_messages = "checked";
					}
					if($row['vwi_newsletter']=="1"){
						$check_vwi_newsletter = "checked";
					}
				?>
				<h3>Fragen</h3>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="own_questions" value="1" <?php if (isset($check_own_questions)) echo $check_own_questions?>>
						Ich möchte benachrichtigt werden, wenn jemand auf eine von mir gestellte Frage antwortet.
					</label>
				</div>
				
				<h3>Nachrichten</h3>
				<p>Es kann vorkommen, dass ein Nutzer eine Nachfrage (bspw. auf eines deiner Kommentare) für dich hat. Falls du hier zustimmst, kann ein Nutzer dir eine Nachricht schreiben. Wir geben jedoch nicht deine E-Mail-Adresse heraus, sondern verschicken die Nachricht über unseren Server. Falls du eine Nachricht an einen anderen Nutzer schicken willst, kannst du dies nur tun, wenn du selbst Nachrichten empfängst.</p>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="user_messages" value="1" <?php if (isset($check_user_messages)) echo $check_user_messages?>>
						Nutzer dürfen mir Nachrichten schicken
					</label>
				</div>
				
				<h3>VWI-ESTIEM</h3>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="vwi_newsletter" value="1" <?php if (isset($check_vwi_newsletter)) echo $check_vwi_newsletter?>>
						<?php
						$row = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM multiple_location_content WHERE name = 'vwi_newsletter'"));
						echo $row['value'];
						?>
					</label>
				</div>
				
				<br>
				
				<button class="btn btn-primary" name="btn-change-questions">Änderungen speichern</button>
			</form>
			
		</div>
		
		<div id="achievements" class="tab-pane fade">
		
		<br>
		
		<div class="well" style="background-color: #e6f3ff; border-radius:3px;">
			<p style="text-align:center; font-size:20px">
				Freunde werben: 
				<input id="recruitLink" style="display:inline-block; border: solid lightgrey 1px; border-radius:3px; padding:10px; background-color:white" value="https://studienführer.vwi-karlsruhe.de/register.php?f=<?php echo $userRow['user_ID'] ?>">
				<!--<button type="button" class="btn btn-default" onclick="copyLink()" style="margin-bottom:2px">Kopieren</button>-->
			</p>
			<p style="text-align:center;">
				Wenn sich deine Kommiltonen mit diesem Link erfolgreich registrieren, besteht die Möglichkeit, dass du weitere Errungenschaften freischaltest!
			</p>
		</div>
		<div class="snackbar" id="recruitLinkCopied">Dein Werbe-Link wurde kopiert.</div>
		
		<script>
		function copyLink() {
			
			var copyText = document.getElementById("recruitLink").innerHTML;
			copyText.select();
			//document.execCommand("copy");
			
			$('#recruitLinkCopied').addClass('show');
			setTimeout(function(){ $('#recruitLinkCopied').removeClass('show'); }, 3000);
		}
		</script>
		
		
		<?php
		//Check if badges were earned
		$sql="
			SELECT * FROM ratings
			WHERE user_ID = ".$userRow['user_ID']." AND comment_rating >= 20
		";
		$result=mysqli_query($con, $sql);
		if(mysqli_num_rows($result) >= 1){
			$result2 = mysqli_query($con, "SELECT * FROM users_badges WHERE user_id = ".$userRow['user_ID']." AND badge_id = 81");
			if(mysqli_num_rows($result2) == 0){ //Wenn badge noch nicht vorhanden
				$sql2="INSERT INTO `users_badges`(`user_id`, `badge_id`) VALUES (".$userRow['user_ID'].",81)";
				if ($con->query($sql2) == TRUE) {
					?><script>alert("Du hast die Errungenschaft Bestseller-Autor freigeschaltet!")</script><?php
				}
			}
		}
		?>
		
		<h1 style="text-align:center"><i class="fa fa-trophy" style="color:#FACC2E"></i> <u>Meine Er&shy;run&shy;gen&shy;schaf&shy;ten</u> <i class="fa fa-trophy" style="color:#FACC2E"></i></h1>
			
			<?php
			$sql="
				SELECT sum(badges.points) AS sum_of_points FROM badges
				JOIN users_badges ON badges.id = users_badges.badge_id
				WHERE users_badges.user_id = ".$userRow['user_ID']."
			";
			$result=mysqli_query($con, $sql);
			$row = mysqli_fetch_assoc($result);
			?>
			<h3 style="text-align:center">Gesamtpunktzahl: <?php echo $row['sum_of_points']?> Punkte</h3>
			<p style="text-align:center">Vergleiche dich mit anderen: <a href="achievements.php">Zum Ranking</a></p>
			<br>

			<div style="text-align:center;">
			
				<?php
				$sql="
					SELECT b.*, (CASE WHEN ub.id IS NOT NULL THEN '1' ELSE NULL END) AS badgeStatus
					FROM badges b
					LEFT JOIN users_badges ub ON b.id = ub.badge_id AND ub.user_id = ".$userRow['user_ID']."
					ORDER BY b.sequence
				";
				$result=mysqli_query($con, $sql);
				
				while($row = mysqli_fetch_assoc($result)){
					if($row['badgeStatus'] == 1){
						$color = "rgb(20,90,157)";
						$blurry = "";
						$name = $row['name'];
						$description = $row['description'];
						$blurryimage = "";
					}else{
						$color = "lightgrey";
						$blurry = "class=\"blurry\"";
						$name = "This name, you know!";
						$description = "Don't use source code to spy on badges!";
						$blurryimage = "blurryimage";
						
						//For development, comment for use
						/*
						$color = "rgb(20,90,157)";
						$blurry = "";
						$name = $row['name'];
						$description = $row['description'];
						$blurryimage = "";
						*/
					}
					
					?>
					<div style="border: solid lightgrey 3px; width: 330px; padding: 5px; background-color:#f2f2f2; display: inline-block; margin:5px;">
						<table style="width:100%">
							<tr>
								<td style="width:1%; padding:5px;"><img src="pictures/badges/<?php echo $row['image']?>" class="media-object <?php echo $blurryimage?>" style="width:80px; background:<?php echo $color?>; border: 4px solid white; padding:5px;"></td>
								<td>
									<table style="width:100%">
										<tr>
											<td <?php echo $blurry?> style="text-align:left; font-size:20px;"><b><?php echo $name?></b></td> 
										</tr>
										<tr>
											<td <?php echo $blurry?> style="text-align:left;"><?php echo $description?> | <?php echo $row['points']?> Punkte</td> 
										</tr>
									</table>
								</td> 
							</tr>
						</table>
					</div>
					<?php
				}
				?>
			</div>		
			<br>
			<p style="text-align:center"><a data-toggle="modal" data-target="#myModal">Bildlizenzen</a></p>

			<!-- Modal -->
			<div class="modal fade" id="myModal" role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						  <button type="button" class="close" data-dismiss="modal">&times;</button>
						  <h4 class="modal-title">Lizenzen für genutzte Icons</h4>
					</div>
					<div class="modal-body">
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.flaticon.com/authors/vitaly-gorbachev" title="Vitaly Gorbachev">Vitaly Gorbachev</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.flaticon.com/authors/lucy-g" title="Lucy G">Lucy G</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.flaticon.com/authors/stephen-hutchings" title="Stephen Hutchings">Stephen Hutchings</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.flaticon.com/authors/smashicons" title="Smashicons">Smashicons</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.flaticon.com/authors/simpleicon" title="SimpleIcon">SimpleIcon</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.flaticon.com/authors/prettycons" title="prettycons">prettycons</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.flaticon.com/authors/mavadee" title="mavadee">mavadee</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.flaticon.com/authors/sarfraz-shoukat" title="Sarfraz Shoukat">Sarfraz Shoukat</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.flaticon.com/authors/creaticca-creative-agency" title="Creaticca Creative Agency">Creaticca Creative Agency</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.flaticon.com/authors/smashicons" title="Smashicons">Smashicons</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.flaticon.com/authors/srip" title="srip">srip</a> from <a href="https://www.flaticon.com/" 			    title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" 			    title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.freepik.com/" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" 			    title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" 			    title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.freepik.com/" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" 			    title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" 			    title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.freepik.com/" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" 			    title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" 			    title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.freepik.com/" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" 			    title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" 			    title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
						<div>Icons made by <a href="https://www.flaticon.com/authors/mynamepong" title="mynamepong">mynamepong</a> from <a href="https://www.flaticon.com/" 			    title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" 			    title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
			</div>		
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

$('#linkToUserFavorites').click(function(event){
	$('.nav-tabs a[href="#favourites"]').tab('show')
});
$('#linkToUserProfile').click(function(event){
	event.preventDefault();
	$('.nav-tabs a[href="#userData"]').tab('show')
});
$('#linkToUserRatings').click(function(event){
	event.preventDefault();
	$('.nav-tabs a[href="#userRatings"]').tab('show')
});
$('#linkToQuestions').click(function(event){
	event.preventDefault();
	$('.nav-tabs a[href="#questions"]').tab('show')
});
$('#linkToNotifications').click(function(event){
	event.preventDefault();
	$('.nav-tabs a[href="#notifications"]').tab('show')
});
$('#linkToAchievements').click(function(event){
	event.preventDefault();
	$('.nav-tabs a[href="#achievements"]').tab('show')
});
</script>

<!-- End of page. Modal für Passwort vergessen -->
<div id="changePasswordModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
					<input value="<?php echo $userRow['email']?>" type="email" class="form-control" name="email" />
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
	<?php if($userRow['admin']=='0'):?>
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
	<?php else:  ?>
		$('[data-toggle="tooltip"]').tooltip();
	<?php endif; ?>
	});
</script>
</body>
</html>
