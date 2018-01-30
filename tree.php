<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>


<div class="container">
<?php
 	if(isset($_GET['suchfeld']) && $_GET['suchfeld'] != 'Übersicht Startseite'){
 		echo 	'<div class="alert alert-warning alert-dismissable fade in">
 					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
 				  <strong>Hinweis!</strong> Leider konnten wir für deine Suche keine Ergebnisse finden.
 				</div>';
 	}
 ?>
  <div class="jumbotron">
    <h1>Willkommen beim Studienführer</h1>
    <p>Schön, dass du die Registrierung geschaffts hast! Der Studienführer ist die beste Hilfestellung bei der Wahl von Vertiefungs- und Mastermodulen für Wiwis am Karlsruher Institut für Technologie.</p>
	<p>Falls du Bugs, Fehler oder Verbessungsvorschläge zum Studienführer hast, kannst du das über "Kontakt" ganz oben loswerden.</p>
  </div>
</div>
<div class="container">
	<?php
	/*Vorbereitung*/
	//Hide all
	$displayTree = "style=\"display:none\"";
	$displaySearch = "style=\"display:none\"";

	//Enable all buttons
	$displayButtonTree = "";
	$displayButtonSearch = "";


	if (isset($_GET['btn-toTree'])){ //Wenn Baum-Button geklickt
		$displayTree = "";
		$displaySearch = "style=\"display:none\"";

		$displayButtonTree = "disabled";
	}

	if (isset($_GET['btn-toSearch'])){ //Wenn Suche-Button geklickt
		$displayTree = "style=\"display:none\"";
		$displaySearch = "";

		$displayButtonSearch = "disabled";
	}
	?>

	<h3 id="auswahl" align="center">Wie möchtest du deine Veranstaltung finden?</h3>
	<div align="center">
			<a id="treebutton" style="width:330px" class="btn btn-primary" >Veranstaltung aus Verzeichnis wählen</a>
			<a id="searchbutton" style="width:330px" class="btn btn-primary" >Veranstaltungen nach Kriterien durchsuchen</a>
	</div>




	<div id="treeSide" <?php echo $displayTree ?>>
	<hr>
 		<h2>Veranstaltungsverzeichnis</h2>
 		<div class="well" style="width:500px; padding: 8px 0;">
 			<div>
 				<ul class="nav nav-list">

 	<?php

	//Super sexy Caching startet
 	$key = "treeside";
 	$CachedString = $InstanceCache->getItem($key);


 	if (is_null($CachedString->get())) {

		$content = "";
 		//Erstellt das Verzeichnis
		$array = array(array("Bachelor - Kernprogramm", "bachelor_basic"), array("Bachelor - Vertiefungsprogramm", "bachelor"), array("Master", "master"));
		for($x = 0; $x <= 2; $x++) {
			$content .= "<li><label class=\"tree-toggler nav-header treetop\" style=\"color:rgb(0, 51, 153)\"><strong>".$array[$x][0]."</strong></label>";

				$content .= "<ul class=\"nav nav-list tree\" style=\"display:none\">";
				$result = mysqli_query($con,"SELECT * FROM moduletypes");
				while($modulTypes = mysqli_fetch_assoc($result)){ //Modultyp
					$content .= "<li><label class=\"tree-toggler nav-header\">".$modulTypes['name']."</label>";

					$content .= "<ul class=\"nav nav-list tree\" style=\"display:none\">";
					$result2 = mysqli_query($con,"
						SELECT modules.name AS module_name, levels.name AS level_name, type
						FROM modules
						JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
						JOIN levels ON modules_levels.level_ID = levels.level_ID
						WHERE levels.name = '".$array[$x][1]."' AND type = '".$modulTypes['name']."'
            ORDER BY TRIM(modules.name);
					");
					while($modules = mysqli_fetch_assoc($result2)){ //Modulname
						$content .= "<li><label class=\"tree-toggler nav-header\">".$modules['module_name']."</label>";

						$content .= "<ul class=\"nav nav-list tree\" style=\"display:none\">";
						$result3 = mysqli_query($con,"
							SELECT subject_name, subjects.ID AS subject_id, modules.name AS module_name
							FROM subjects
							JOIN subjects_modules ON subjects.ID = subjects_modules.subject_ID
							JOIN modules ON subjects_modules.module_ID = modules.module_ID
							WHERE modules.name = '".$modules['module_name']."'
              ORDER BY TRIM(subject_name);
						");
						while($subjects = mysqli_fetch_assoc($result3)){ //Veranstaltungsname
							$content .= "<li><a target=\"_blank\" href=\"index.php?subject=".$subjects['subject_id']."\">".$subjects['subject_name']."</a></li>";
						}
						$content .= "</ul>";
						$content .= "</li>";
					}
					$content .= "</ul>";
					$content .= "</li>";
				}
				$content .= "</ul>";
			$content .= "</li>";
		}
		$CachedString->set($content)->expiresAfter(300000);//in seconds, also accepts Datetime
 		$InstanceCache->save($CachedString); // Save the cache item just like you do with doctrine and entities

 		echo $CachedString->get();

 	} else {
 		echo $CachedString->get();
 	}
?>
				</ul>
			</div>
		</div>

		<script> //Schließt das Verzeichnis beim Laden der Seite
		$(document).ready(function () {
			$('label.tree-toggler').click(function () {
				$(this).parent().children('ul.tree').toggle(300);
			});
		});
		</script>
	</div>


	<div id="searchSide" <?php echo $displaySearch ?>>
		<hr>
		<h2>Veranstaltungssuche</h2>
		<p><i>Vorsicht beim Filtern: Wird beim Modul-Typ "BWL" angegeben, beim Modul aber "Informatik", kann es natürlich keine Ergebnisse geben. Ebenso können sich beispielsweise Dozent und Institut schnell gegenseitig ausschließen.</i></p>

		<form id="filtersort" class="form-horizontal" method="post">
			<div class="row">
				<div class="col-md-4">

					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4">Modul-Typ:</label>
							<div class="col-md-8">
								<div class="row">

									<!--
									VORSICHT!
									Folgender Code erstellt Checkboxen dynamisch. Funktioniert allerdings (kontrolliert) nur
									bis zu 12 Checkboxen wegen der Spaltenaufteilung (col-md-x), siehe switch unten.
									Sollte aber eigentlich genügen.
									-->

									<?php

									$q = mysqli_query($con,"SELECT * FROM moduletypes");
									$array_types = array();
									while($row = mysqli_fetch_assoc($q)){
										$array_types[] .= $row['name'];
									}

									$columnSize = "";
									switch(count($array_types)){
										case 1:
										case 2:
										case 3:
										$columnSize = 12;
										break;
										case 4:
										case 5:
										case 6:
										$columnSize = 6;
										break;
										case 7:
										case 8:
										case 9:
										$columnSize = 4;
										break;
										case 10:
										case 11:
										case 12:
										$columnSize = 3;
										break;
									}
									?>

									<div class="col-md-<?php echo($columnSize)?>">
										<?php
										for ($j = 1; $j <= count($array_types); $j++) {
											$i = $j-1; //Nicht einfach "$j = 0; $j < count($array_types)" damit Modulus-Operation unten funktioniert

											echo("
												<div class=\"checkbox\"><label><input type=\"checkbox\" name=\"modulType[]\" value=\"".$array_types[$i]."\" checked >".$array_types[$i]."</label></div>
											");

											if(($j%3)==0){ //Fängt eine neue Spalte nach drei Einträgen an
												echo("</div><div class=\"col-md-".$columnSize."\">");
											}
										}
										?>
									</div>
								</div>
							</div>
						</div>
					</div>

				<?php
					$key = "table_mod_selection";
					$CachedString = $InstanceCache->getItem($key);
					if (is_null($CachedString->get())) {
						$mod_selection = "<option value=\"none\">(Keine Einschränkung)</option>";
						$result = mysqli_query($con,"SELECT * FROM modules ORDER BY name");
						while($mod_row = mysqli_fetch_assoc($result)){
              $mod_selection .= "<option value=".$mod_row['module_ID'].">".$mod_row['name']." [".$mod_row['code']."]</option>";
            }
						$CachedString->set($mod_selection)->expiresAfter(3000000);//in seconds, also accepts Datetime
						$InstanceCache->save($CachedString); // Save the cache item just like you do with doctrine and entities
					} else {
						$mod_selection = $CachedString->get();
					}
				?>
					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4" for="pwd">Modul:</label>
							<div class="col-md-8">
								<select class="form-control" name="module">
									<?php echo $mod_selection ?>
								</select>
							</div>
						</div>
					</div>

				</div>

				<div class="col-md-4">

				<?php
					$key = "table_lec_selection";
					$CachedString = $InstanceCache->getItem($key);
					if (is_null($CachedString->get())) {
						$lec_selection = "<option value=\"none\">(Keine Einschränkung)</option>";
						$sql = "
							SELECT *
							FROM lecturers
							JOIN lecturers_institutes ON lecturers.lecturer_ID=lecturers_institutes.lecturer_ID
							JOIN institutes ON lecturers_institutes.institute_ID=institutes.institute_ID
							ORDER BY name, last_name
						";
						$result = mysqli_query($con,$sql);
						while($row = mysqli_fetch_assoc($result)){
							$lec_selection .= "<option value=".$row['lecturer_ID'].">".$row['last_name'].", ".$row['first_name']." (".$row['abbr'].")</option>";
						}
						$CachedString->set($lec_selection)->expiresAfter(3000000);//in seconds, also accepts Datetime
						$InstanceCache->save($CachedString); // Save the cache item just like you do with doctrine and entities
					} else {
						$lec_selection = $CachedString->get();
					}
				?>
					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4" for="pwd">Dozent:</label>
							<div class="col-md-8">
								<select class="form-control" name="lecturer">
									<?php echo $lec_selection ?>
								</select>
							</div>
						</div>
					</div>

				<?php
					$key = "table_insti_selection";
					$CachedString = $InstanceCache->getItem($key);
					if (is_null($CachedString->get())) {
						$insti_selection = "<option value=\"none\">(Keine Einschränkung)</option>";
						$result = mysqli_query($con, "SELECT * FROM institutes ORDER BY name");
						while($row = mysqli_fetch_assoc($result)){
              $insti_selection .= "<option value=".$row['institute_ID'].">".$row['name']." (".$row['abbr'].")</option>";
            }
						$CachedString->set($insti_selection)->expiresAfter(3000000);//in seconds, also accepts Datetime
						$InstanceCache->save($CachedString); // Save the cache item just like you do with doctrine and entities
					} else {
						$insti_selection = $CachedString->get();
					}
				?>
					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4" for="pwd">Institut:</label>
							<div class="col-md-8">
								<select class="form-control" name="institute">
									<?php echo $insti_selection ?>
								</select>
							</div>
						</div>
					</div>

				</div>

				<div class="col-md-4">

					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4" for="pwd">Level:</label>
							<div class="col-md-8">
								<select name="level" class="form-control" required>
									<option value="none">(Keine Einschränkung)</option>
									<option value="bachelor_basic">Bachelor: Kernprogramm</option>
									<option value="bachelor">Bachelor: Vertiefungsprogramm</option>
									<option value="master">Master</option>
								</select>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4">Semester:</label>
							<div class="col-md-8">
								<select class="form-control" name="semester">
									<option value="none">(Keine Einschränkung)</option>
									<option value="Winter">Winter</option>
									<option value="Sommer">Sommer</option>
								</select>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4" for="pwd">Sprache:</label>
							<div class="col-md-8">
								<select class="form-control" name="language">
									<option value="none">(Keine Einschränkung)</option>
									<option value="Deutsch">Deutsch</option>
									<option value="Englisch">Englisch</option>
								</select>
							</div>
						</div>
					</div>

				</div>
			</div>

			<br><br>

			<div class="row"><div class="control-group"> <div class="controls form-inline">
				<span><strong>Sortieren nach</strong></span>

				<select class="form-control" id="sortArea" name="sortArea">
					<option value="overall">Bewertung insgesamt</option>
					<option value="lecture">Vorlesung</option>
					<option value="exam">Prüfung</option>
				</select>

				<select class="form-control treeSort" id="sortOverall" name="sortOverall">
					<option value="overallRating">Gesamtbewertung</option>
					<option value="recoms">Veranstaltungsempfehlungen</option>
				</select>

				<select class="form-control treeSort" id="sortLecture" style="display:none" name="sortLecture">
					<option value="overallLecture">Overall Vorlesung</option>
					<option value="relevance">Prüfungsrelevanz</option>
					<option value="interest">Interessantheit</option>
					<option value="quality">Qualität der Arbeitsmaterialien</option>
				</select>

				<select class="form-control treeSort" id="sortExamType" style="display:none" name="sortExamType">
					<option value="written_oral">Schriftlich/Mündlich</option>
					<!--<option value="oral">Mündlich</option>-->
					<option value="other">Sonstige</option>
				</select>

				<select class="form-control treeSort" id="sortExamItem" style="display:none" name="sortExamItem">
					<option value="overallExam">Overall Prüfung</option>
					<option value="effort">Aufwand</option>
					<option value="fairness">Fairness</option>
					<option value="timePressure">Zeitdruck</option>
					<option value="reproductionTransfer">Reproduktion/Transfer</option>
					<option value="qualitativeQuantitative">Qualitativ/Quantiativ</option>
				</select>

				<select class="form-control treeSort" id="sortExamOther" style="display:none" name="sortExamOther">
					<option value="amountRatings">#Anmerkungen Prüfung</option>
				</select>

				<select class="form-control" name="orderDirection">
					<option id="ab" value="ASC">Absteigend</option>
					<option id="auf" value="DESC">Aufsteigend</option>
				</select>

				<button type="submit" class="btn btn-primary" id="btn-filterSort">Filtern & Sortieren</button>
			</div></div></div>
		</form>

		<div style="align:center;display:none;" id="tabelleLaden"></div>

		<script>
		//Script für die Sortierungs-Dropdowns
		$('#sortArea').on('change', function() {
			switch(this.value){
				case "overall":
					$('.treeSort').hide();
					$('#sortOverall').show();
					$('#auf').html("Aufsteigend");
					$('#ab').html("Absteigend");
					break;
				case "lecture":
					$('.treeSort').hide();
					$('#sortLecture').show();
					$('#auf').html("Aufsteigend");
					$('#ab').html("Absteigend");
					break;
				case "exam":
					$('.treeSort').hide();
					$('#sortExamType').show();
					$('#sortExamItem').show();
					if($('#sortExamItem').val() != "reproductionTransfer" && $('#sortExamItem').val() != "qualitativeQuantitative"){
						$('#auf').html("Aufsteigend");
						$('#ab').html("Absteigend");
					}else{
						switch($('#sortExamItem').val()){
							case "reproductionTransfer":
								$('#auf').html("Reproduktion zuerst");
								$('#ab').html("Transfer zuerst");
								break;
							case "qualitativeQuantitative":
								$('#auf').html("Qualitativ zuerst");
								$('#ab').html("Quantitativ zuerst");
								break;
						}
					}
					break;
			}
		})

		$('#sortExamType').on('change', function() {
			switch(this.value){
				case "other":
					$('#sortExamItem').hide();
					$('#sortExamOther').show();
					$('#auf').html("Aufsteigend");
					$('#ab').html("Absteigend");
					break;
				default:
					$('#sortExamOther').hide();
					$('#sortExamItem').show();
					if($('#sortExamItem').val() != "reproductionTransfer" && $('#sortExamItem').val() != "qualitativeQuantitative"){
						$('#auf').html("Aufsteigend");
						$('#ab').html("Absteigend");
					}else{
						switch($('#sortExamItem').val()){
							case "reproductionTransfer":
								$('#auf').html("Reproduktion zuerst");
								$('#ab').html("Transfer zuerst");
								break;
							case "qualitativeQuantitative":
								$('#auf').html("Qualitativ zuerst");
								$('#ab').html("Quantitativ zuerst");
								break;
						}
					}
					break;
			}
		})

		$('#sortExamItem').on('change', function() {
			switch(this.value){
				case "reproductionTransfer":
					$('#auf').html("Reproduktion zuerst");
					$('#ab').html("Transfer zuerst");
					break;
				case "qualitativeQuantitative":
					$('#auf').html("Qualitativ zuerst");
					$('#ab').html("Quantitativ zuerst");
					break;
				default:
					$('#auf').html("Aufsteigend");
					$('#ab').html("Absteigend");
					break;
			}
		})
		</script>

		<!--Ergebnistabelle-->
		<script>

		$('#btn-filterSort').on('click', function(e) {
			e.preventDefault();
			$('#limitationText').hide();
			if($('input[type="checkbox"]:checked').length) {
				$('#resultTable').hide();
				$('#tabelleLaden').show();
				$.ajax({
					url: "tree_createTable.php",
					type: "get",
					data: $("#filtersort").serialize(),
					success: function (data) {
						var help = $('#resultTable').html();
						$('#resultTable').show();
						$('#resultTable').html(data);
						if(help == ""){ //Nur beim ersten Mal (wenn noch keine Tabelle vorhanden)
							$('html, body').animate({ //Scroll down to results
								scrollTop: $("#btn-filterSort").offset().top -100
							}, 1500);
						}
            $('#tabelleLaden').hide();
						history.replaceState("Studienführer Such- und Filterseite", "Such- und Filterergebnis", "tree.php?filterandsearch=filterandsearch&val="+encodeURI($("#filtersort").serialize()));
					},
					error: function() {
						$('#tabelleLaden').hide();
						alert("Error!");
					}
				});
			} else {
				alert("Wähle mindestens einen Modultyp aus - andernfalls kann es keine Ergebnisse geben.");
			}

		});
		</script>

		<div id="resultTable"></div>
		<p id="limitationText" style="text-align:center; display:none"><i>Das Suchergebnis ist aus Performancegründen auf 50 Ergebnisse limitiert.</i></p>
	</div>
</div>
<script>
	//Startet Pagination
	$(document).ready(function() {
		insertLoader('#tabelleLaden');
		if((decodeURIComponent((new RegExp('[?|&]' + 'filterandsearch' + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [null, ''])[1].replace(/\+/g, '%20')) || null)=="filterandsearch"){
			$('#searchbutton').addClass('disabled');
			$('#treebutton').removeClass('disabled');
			$('#treeSide').hide();
			$('#searchSide').show();
			$('#tabelleLaden').show();
			$.ajax({
					url: "tree_createTable.php",
					type: "get",
					data: decodeURI(window.location.href.split("&val=")[1]),
					success: function (data) {
              $('#tabelleLaden').hide();
						var help = $('#resultTable').html();
						$('#resultTable').html(data);
						if(help == ""){ //Nur beim ersten Mal (wenn noch keine Tabelle vorhanden)
							$('html, body').animate({ //Scroll down to results
								scrollTop: $("#btn-filterSort").offset().top -100
							}, 500);
						}
						history.replaceState("Studienführer Such- und Filterseite", "Such- und Filterergebnis", "tree.php?filterandsearch=filterandsearch&val="+$("#filtersort").serialize());
					},
					error: function() {
						$('#tabelleLaden').hide();
						alert("Error!");
					},
          finally: function(){
            $('#tabelleLaden').hide();
          }
				});
		}
		$('#treebutton').click(function(event){
			$('#treebutton').addClass('disabled');
			$('#searchbutton').removeClass('disabled');
			$('#searchSide').hide();
			$('#treeSide').show();
		});
		$('#searchbutton').click(function(event){
			$('#searchbutton').addClass('disabled');
			$('#treebutton').removeClass('disabled');
			$('#treeSide').hide();
			$('#searchSide').show();
		});
	});
</script>
<script src="res/lib/jquery.simplePagination.js"></script>
<script src="res/lib/jquery.nicescroll-master/jquery.nicescroll.js"></script>
<br />
<br />
<br />
<br />
<br />
</body>
</html>
