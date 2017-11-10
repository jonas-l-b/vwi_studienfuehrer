<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>

<?php
if($userRow['admin']==0){
	echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=no_admin';</SCRIPT>");
}

$InstanceCache->deleteItem("treeside");

?>

<html>
<body>

<?php include "inc/nav.php" ?>

<div class="container" style="margin-top:60px">
	<h2>Veranstaltung eintragen &nbsp
		<a href="#" data-trigger="focus" data-toggle="popoverLNDW" title="Du bist ein cooler Typ!" data-content="Und das obwohl du wahrscheinlich gerade keine Sonnenbrille aufhast. Du bist einfach nur cool, weil du hier bei der Langen Nacht des Wissens sitzt und dabei hilfst, das Modulhandbuch in unsere Datenbank einzupflegen. Danke dir dafür!">
			<span class="glyphicon glyphicon-sunglasses"></span>
		</a>
		<script>
		$('[data-toggle="popoverLNDW"]').popover();
		</script>
	</h2>
	<hr>

	<div class="col-md-8">


		<?php
		if (isset($_POST['btn-createSubject'])){
			$subject_name = strip_tags($_POST['subject_name']);
			$identifier = strip_tags($_POST['identifier']);
			$lv_number = strip_tags($_POST['lv_number']);
			$ECTS = strip_tags($_POST['ECTS']);
			$lec_select = $_POST['lec_select'];
			$mod_select = $_POST['mod_select'];
			$semester = strip_tags($_POST['semester']);
			$language = strip_tags($_POST['language']);
			$userID = $userRow['user_ID'];

			//in subjects einfügen
			if(mysqli_num_rows(mysqli_query($con,"SELECT * FROM subjects WHERE subject_name = '".$subject_name."';"))!=0){
				$msg = "
					<div class='alert alert-danger'>
						<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Die Veranstaltung ist bereits vorhanden.
					</div>
				";
			} else{
				$sql1 = "
					INSERT INTO subjects(subject_name, identifier, lv_number, ECTS, semester, language, createdBy_ID, time_stamp)
					VALUES ('$subject_name', '$identifier', '$lv_number', '$ECTS', '$semester', '$language', '$userID', now());
				";
				mysqli_query($con,$sql1);
				//$db_logger->info("Neue Veranstaltung hinzugefügt: $subject_name mit ($identifier) von User: $userID" );
			}

			//Verbindungseinträge vorbereiten
			$subject_new_id = "";

			$sub = mysqli_query($con,"SELECT * FROM subjects ORDER BY time_stamp DESC LIMIT 1;");
			while($sub_row = mysqli_fetch_assoc($sub)){
				$subject_new_id = $sub_row['ID'];
			}

			//in subjects_lectures einfügen
			foreach($lec_select as $value){
				$sql2 = "
					INSERT INTO subjects_lecturers(subject_ID, lecturer_ID)
					VALUES ('$subject_new_id', '$value');
				";
				mysqli_query($con,$sql2);
			}

			//in subjects_modules einfügen
			foreach($mod_select as $value){
				$sql3 = "
					INSERT INTO subjects_modules(subject_ID, module_ID)
					VALUES ('$subject_new_id', '$value');
				";
				mysqli_query($con,$sql3);
			}

			//create message
			$msg = "
				<div class='alert alert-success'>
					<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Die Veranstaltung wurde erfolgreich eingetragen.
				</div>
			";
		}
		?>

		<?php if(isset($msg)) echo $msg ?>

		<form id="createSubjectForm" method="POST">

			<div class="form-group">
				<label>Veranstaltungsname</label>
				<p>Wie heißt die Veranstaltung? Bitte den vollständigen Veranstaltungen angeben; dazu am Modulhandbuch orientieren.</p>
				<input name="subject_name" type="text" class="form-control" placeholder="Veranstaltungsname" required />
			</div>

			<hr>

			<div class="form-group">
				<label>Kennung</label>
				<p>Welche <strong>Veranstaltungs</strong>kennung hat die Veranstaltung im Modulhandbuch (Veranstaltungskennungen beginnen immer mit einem <strong>T</strong>; Bsp.: <strong>"T-WIWI-102861"</strong>)?
				<input name="identifier" type="text" class="form-control" placeholder="Kennung" required />
			</div>

			<hr>

			<div class="form-group">
				<label>LV.-Nummer</label>
				<p>Welche LV.-nummer hat die Veranstaltung im Modulhandbuch (LV.-Nummern bestehen nur aus Zahlen und finden sich im Modulhandbuch auf der jeweiligen Seite der Veranstaltung; Bsp.: <strong>"2521533"</strong>; Enthält die Veranstaltung mehrere Einträge, bspw. Übung und Vorlesung, wähle die der Vorlesung)?
				<input name="lv_number" type="text" class="form-control" placeholder="LV.-Nummer" required />
			</div>

			<hr>

			<div class="form-group">
				<label>ECTS</label>
				<p>Wie viele ECTS bringt die Veranstaltung ein?</p>
				<input name="ECTS" type="text" class="form-control" placeholder="ECTS" required />
			</div>

			<hr>

			<?php
			$lec_selection = "";

			$sql = "
				SELECT *
				FROM lecturers
				JOIN lecturers_institutes ON lecturers.lecturer_ID=lecturers_institutes.lecturer_ID
				JOIN institutes ON lecturers_institutes.institute_ID=institutes.institute_ID
				ORDER BY name, last_name
			";

			$lec = mysqli_query($con,$sql);

			while($lec_row = mysqli_fetch_assoc($lec)){
				$lec_selection .= "<option value=\"".$lec_row['lecturer_ID']."\">".$lec_row['last_name'].", ".$lec_row['first_name']." (".$lec_row['abbr'].")</option>";
			}

			?>

			<div class="form-group">
				<label>Dozent(en)</label>
				<p>Wer verantwortet die Veranstaltung?</p>
				<p><i>Falls gewünschter Dozent nicht in Dropdown vorhanden ist, muss er erst noch hinzugefügt werden. Dazu das entsprechende Formular rechts oben auf dieser Seite ausfüllen.</i></p>
				<select id="lec_select" name="lec_select[]" multiple="" class="search ui fluid dropdown form-control" required>
					<?php echo $lec_selection ?>
				</select>
			</div>

			<hr>

			<?php
			$mod_selection = "";

			$mod = mysqli_query($con,"SELECT * FROM modules ORDER BY name");

			while($mod_row = mysqli_fetch_assoc($mod)){
				$mod_selection .= "<option value=".$mod_row['module_ID'].">".$mod_row['name']." [".$mod_row['code']."]</option>";
			}
			?>

			<div class="form-group">
				<label>Teil der Module</label>
				<p>Welchen Modulen ist die Veranstaltung zuzuordnen?</p>
				<p><i>Falls gewünschtes Modul nicht in Dropdown vorhanden ist, muss es erst noch hinzugefügt werden. Dazu das entsprechende Formular rechts oben auf dieser Seite ausfüllen.</i></p>
				<select id="mod_select" name="mod_select[]" multiple="" class="search ui fluid dropdown form-control" required>
					<?php echo $mod_selection ?>
				</select>
			</div>

			<hr>

			<div class="form-group">
				<label>Semester</label>
				<p>In welchem Turnus findet die Veranstaltung statt?</p>
				<select name="semester" class="form-control" required>
					<option value="Winter">Winter</option>
					<option value="Sommer">Sommer</option>
					<option value="Ganzjährig">Ganzjährig</option>
					<option value="Unregelmäßig">Unregelmäßig</option>
				</select>
			</div>

			<hr>

			<div class="form-group">
				<label>Sprache</label>
				<p>In welcher Sprache findet die Veranstaltung statt?</p>
				<select name="language" class="form-control" required>
					<option value="Deutsch">Deutsch</option>
					<option value="Englisch">Englisch</option>
				</select>
			</div>

			<hr>

			<button type="submit" class="btn btn-primary" name="btn-createSubject">Veranstaltung eintragen</button>

		</form>
	</div>

	<div class="col-md-4">
		<div class="panel-group" id="accordion"> <!--Collapse 1 -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
							<h3>Dozent eintragen</h3>
						</a>
					</h3>
				</div>
				<div id="collapse1" class="panel-collapse collapse">
					<div class="panel-body">
						<form id="form1" name="form1">

							<div class="form-group">
								<label>Vorname</label>
								<input id="lec_first_name" type="text" class="form-control" placeholder="Vorname" required />
							</div>

							<div class="form-group">
								<label>Nachname</label>
								<input id="lec_last_name" type="text" class="form-control" placeholder="Nachname" required />
							</div>

							<?php
							$insti = mysqli_query($con, "SELECT * FROM institutes ORDER BY name");
							$insti_selection = "";
							while($insti_row = mysqli_fetch_assoc($insti)){
								$insti_selection .= '<div class="item" data-value="'.$insti_row['institute_ID'].'">'.$insti_row['name']." (".$insti_row['abbr'].")</div>";
							}
							?>
							<div class="form-group">
								<label>Institut</label>
								<p><i>Falls gewünschtes Institut nicht in Dropdown vorhanden ist, muss es erst noch hinzugefügt werden. Dazu das entsprechende Formular gleich hierunter.</i></p>
								<div class="ui fluid search selection dropdown">
								  <input id="lec_institute_select" class="form-control" type="hidden" required name="country">
								  <i class="dropdown icon"></i>
								  <div class="default text">Institut auswählen</div>
								  <div id="lec_institute_select2" class="menu">
								  <?php echo $insti_selection ?>
								</div>
								</div>
							</div>

							<div>
								<button id="lec_submit" onclick="myFunction()" type="submit" class="btn btn-primary">Dozent eintragen</button>
							</div>

						</form>

						<script>
						function myFunction() {
							event.preventDefault();
							var first_name = document.getElementById("lec_first_name").value;
							var last_name = document.getElementById("lec_last_name").value;
							var institute = document.getElementById("lec_institute_select").value;

							// Returns successful data submission message when the entered information is stored in database.
							var dataString = 'first_name1=' + first_name + '&last_name1=' + last_name + '&institute1=' + institute;
							if (first_name == '' || last_name == '' || institute == '') {
							alert("Bitte alle Felder ausfüllen!");
							} else {

							// AJAX code to submit form.
							$.ajax({
							type: "POST",
							url: "admin_createLecturer_submit.php",
							data: dataString,
							cache: false,
							success: function(data) {
								if($.trim(data)=='existsAlready'){
									alert('Dieser Dozent ist bereits vorhanden!')
								} else{
									alert('Dozent wurde erfolgreich eingetragen und ist jetzt im entsprechenden Dropdown auswählbar (ganz unten).');
									$('#lec_select').append(data);
								}
								$('#lec_first_name').val("");
								$('#lec_last_name').val("");
							}
							});
							}
							return false;
						}
						</script>
					</div>
				</div>
			</div>  <!--Ende Collapse 1 -->
			<div class="panel panel-default">  <!--Collapse 2 -->
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
							<h3>Institut eintragen</h3>
						</a>
					</h3>
				</div>
				<div id="collapse2" class="panel-collapse collapse">
					<div class="panel-body">
						<form id="form2" name="form2">

							<div class="form-group">
								<label>Name</label>
								<p>Wie heißt das Institut? Bitte vollen Namen angeben (Bsp.: "<strong>Institut für Informationswirtschaft und Marketing</strong>").</p>
								<input id="inst_name" type="text" class="form-control" placeholder="Institutsname" required />
							</div>

							<div class="form-group">
								<label>Abkürzung</label>
								<p>Wie wird das Institut abgekürzt (Bsp.: "<strong>IISM</strong>")?</p>
								<input id="inst_abbr" type="text" class="form-control" placeholder="Institutsname" required />
							</div>

							<div>
								<button id="mod_submit" onclick="myFunction2()" type="submit" class="btn btn-primary">Institut eintragen</button>
							</div>

						</form>

						<script>
						function myFunction2() {
							event.preventDefault();
							var inst_name = document.getElementById("inst_name").value;
							var inst_abbr = document.getElementById("inst_abbr").value;

							// Returns successful data submission message when the entered information is stored in database.
							var dataString = 'inst_name=' + inst_name + '&inst_abbr=' + inst_abbr;
							if (inst_name == ''|| inst_abbr == '') {
							alert("Bitte alle Felder ausfüllen!");
							} else {

							// AJAX code to submit form.
							$.ajax({
							type: "POST",
							url: "admin_createInstitute_submit.php",
							data: dataString,
							cache: false,
							success: function(data) {
								if($.trim(data)=='existsAlready'){
									alert('Dieses Institut ist bereits vorhanden!')
								} else{
									alert('Institut wurde erfolgreich eingetragen und ist jetzt im entsprechenden Dropdown auswählbar (ganz unten).');
									//alert(data);
									$('#lec_institute_select2').append(data);
								}
								$('#inst_name').val("");
								$('#inst_abbr').val("");
							}
							});
							}
							return false;
						}
						</script>
					</div>
				</div>
			</div> <!--Ende Collapse 2 -->
			<div class="panel panel-default">  <!--Collapse 3 -->
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
							<h3>Modul eintragen</h3>
						</a>
					</h3>
				</div>
				<div id="collapse3" class="panel-collapse collapse">
					<div class="panel-body">
						<form id="form3" name="form3">

							<div class="form-group">
								<label>Modul-Name</label>
								<p>Wie heißt das Modul?</p>
								<input id="mod_name" type="text" class="form-control" placeholder="Modul-Name" required />
							</div>

							<div class="form-group">
								<label>Kennung</label>
								<p>Welche <strong>Modul</strong>kennung hat das Modul im Modulhandbuch (Modulkennungen beginnen immer mit einem <strong>M</strong>; Bsp.: <strong>M-WIWI-101500</strong>)?</p>
								<input id="mod_code2" type="text" class="form-control" placeholder="Kennung" required />
							</div>

							<?php
							$mod_type1 = mysqli_query($con, "SELECT * FROM moduletypes");
							$mod_type1_selection = "";
							while($mod_type1_row = mysqli_fetch_assoc($mod_type1)){
								$mod_type1_selection .= "<option value=".$mod_type1_row['name'].">".$mod_type1_row['name']."</option>";
							}
							?>
							<div class="form-group">
								<label>Modultyp</label>
								<p>Von welchem Typ ist das Modul?</p>
								<select id="mod_type_select" class="form-control" name="modul_type" required>
									<?php echo $mod_type1_selection ?>
								</select>
							</div>

							<div class="form-group">
								<label>Modul-Level</label>
								<p>Wann kann das Modul belegt werden?</p>
								<p><i>Durch Gedrückthalten von STRG mehrere Level auswählen.</i></p>
								<select name="mod_level_select[]" id="mod_level_select" multiple="" class="form-control" required>
									<option value="bachelor_basic">Bachelor: Kernprogramm</option>
									<option value="bachelor">Bachelor: Vertiefungsprogramm</option>
									<option value="master">Master</option>
								</select>
							</div>

							<div class="form-group">
								<label>ECTS</label>
								<p>Wie viele ECTS bringt das gesamte Modul ein?</p>
								<input id="mod_ECTS" value="9" type="text" class="form-control" placeholder="ECTS" required />
							</div>

							<div>
								<button id="mod_submit" onclick="myFunction3()" type="submit" class="btn btn-primary">Modul eintragen</button>
							</div>

						</form>

						<script>
						function myFunction3() {
							event.preventDefault();
							var mod_code = document.getElementById("mod_code2").value;
							var mod_name = document.getElementById("mod_name").value;
							var mod_type = document.getElementById("mod_type_select").value;
							//var mod_level = document.getElementsById("mod_level_select").value;
							var mod_level = $('#mod_level_select').val();
							var mod_ECTS = document.getElementById("mod_ECTS").value;

							// Returns successful data submission message when the entered information is stored in database.
							var dataString = 'mod_code=' + mod_code + '&mod_name=' + mod_name + '&mod_type=' + mod_type + '&mod_level=' + mod_level + '&mod_ECTS=' + mod_ECTS;
							if (mod_code == '' || mod_name == '' || mod_type == '' || mod_level == '' || mod_ECTS == '') {
							alert("Bitte alle Felder ausfüllen!");
							alert("mod_name: "+mod_name+", mod_code: "+mod_code+", mod_type: "+mod_type+", mod_level: "+mod_level+", mod_ECTS: "+mod_ECTS);

							} else {

							// AJAX code to submit form.
							$.ajax({
							type: "POST",
							url: "admin_createModule_submit.php",
							data: dataString,
							cache: false,
							success: function(data) {
								//alert(data);
								if($.trim(data)=='existsAlready'){
									alert('Dieses Modul ist bereits vorhanden!')
								} else{
									alert('Modul wurde erfolgreich eingetragen und ist jetzt im entsprechenden Dropdown auswählbar (ganz unten).');
									$('#mod_select').append(data);
								}
								$('#mod_code2').val("");
								$('#mod_name').val("");
								$('#mod_type_select').val("BWL");
								$('#mod_level_select').val("");
								$('#mod_ECTS').val("");
							}
							});
							}
							return false;
						}
						</script>
					</div>
				</div>
			</div> <!--Ende Collapse 3 -->
		</div>

	</div>



</div>
<script>
$('.ui.dropdown')
  .dropdown({
    fullTextSearch: true,
	useLabels: false
  })
;
</script>
<br />
<br />
</body>
</html>
