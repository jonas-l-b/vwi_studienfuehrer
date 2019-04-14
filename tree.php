<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

include "dataPrivacy.php";

?>
<body>

<?php include "inc/nav.php" ?>
<!-- FEED START -->
<div id="div2" class="feeddiv">
	<script>
	$(document).ready(function() {
		// Configure/customize these variables.
		var showChar = 100;  // How many characters are shown by default
		var ellipsestext = "...";
		var moretext = "Mehr";
		var lesstext = "Weniger";
		

		$('.more').each(function() {
			var content = $(this).html();
	 
			if(content.length > showChar) {
	 
				var c = content.substr(0, showChar);
				var h = content.substr(showChar, content.length - showChar);
	 
				var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';
	 
				$(this).html(html);
			}
	 
		});
	 
		$(".morelink").click(function(){
			if($(this).hasClass("less")) {
				$(this).removeClass("less");
				$(this).html(moretext);
			} else {
				$(this).addClass("less");
				$(this).html(lesstext);
			}
			$(this).parent().prev().toggle();
			$(this).prev().toggle();
			return false;
		});
	});
	</script>

	<?php
	$feedLimit = 3;
	?>

	<div class="feedbox">
		<div class="feedhead">
			<span class="feedtitle">	
				NEUSTE KOMMENTARE
			</span>
		</div>
		<div class="feedbody">
			<?php
			$sql = "
				SELECT subjects.ID AS ID, subject_name, username, ratings.time_stamp AS time_stamp, comment
				FROM ratings
				JOIN subjects ON ratings.subject_ID = subjects.ID
				JOIN users ON ratings.user_ID = users.user_ID
				ORDER BY ratings.time_stamp DESC
				LIMIT $feedLimit
			"; //Set $feedLimit above
			$result = mysqli_query($con, $sql);
			
			$count = 0;
			while($row = mysqli_fetch_assoc($result)){
				$count++;
				?>
				<p>
					<strong><a href="index.php?subject=<?php echo $row['ID']?>"><?php echo $row['subject_name']?></a></strong>
					<br>
					<span style="color:grey; font-size:10px;"><?php echo $row['username']?> <?php echo time_elapsed_string($row['time_stamp'])?></span>
				</p>
				<p class="more" style="word-break: break-all;">
					<?php echo $row['comment']?>
				</p>
				<hr>
				<?php
			}
			?>
			<p align="center"><a href="feed.php">Mehr anzeigen</a></p>
		</div>
	</div>

	<br>

	<div class="feedbox">
		<div class="feedhead">
			<span class="feedtitle">	
				NEUSTE FRAGEN
			</span>
		</div>
		<div class="feedbody">
			<?php
			$sql = "
				SELECT subjects.ID AS ID, subject_name, username, questions.time_stamp AS time_stamp, question
				FROM questions
				JOIN subjects ON questions.subject_ID = subjects.ID
				JOIN users ON questions.user_ID = users.user_ID
				ORDER BY questions.time_stamp DESC
				LIMIT $feedLimit
			"; //Set $feedLimit above
			$result = mysqli_query($con, $sql);
			
			$count = 0;
			while($row = mysqli_fetch_assoc($result)){
				$count++;
				?>
				<p>
					<strong><a href="index.php?subject=<?php echo $row['ID']?>"><?php echo $row['subject_name']?></a></strong>
					<br>
					<span style="color:grey; font-size:10px;"><?php echo $row['username']?> <?php echo time_elapsed_string($row['time_stamp'])?></span>
				</p>
				<p class="more" style="word-break: break-all;">
					<?php echo $row['question']?>
				</p>
				<hr>
				<?php
			}
			?>
			<p align="center"><a href="feed.php">Mehr anzeigen</a></p>
		</div>
	</div>
	
	<br>
</div>

<div id="changeButton"></div>

<script>
function collision($div1, $div2) {

	var left1 = $div1.offset().left;
	var width1 = $div1.width();
	var right1 = left1 + width1;
	
	var left2 = $div2.offset().left;
	
	//$('#aaa').html("left1: " + left1 + ", width1: " + width1 + ", right1: " + right1 + " // left2: " + left2);
	
	if(right1+33 > left2) return true;
	return false;
};

$(document).ready(function() {
	if(collision($('#treebody'), $('#changeButton')) == true){
		$('#div2').hide();
		$("#changeButton").text("Feed anzeigen");
	}else{
		$('#div2').show();
		$("#changeButton").text("Feed ausblenden");
	}
});

$(window).resize(function(){
	if(collision($('#treebody'), $('#changeButton')) == true){
		$('#div2').hide();
		$("#changeButton").text("Feed anzeigen");
	}else{
		$('#div2').show();
		$("#changeButton").text("Feed ausblenden");
	}	
});


$("#changeButton").click(function () {
	if($('#div2').is(":visible")){
		$('#div2').hide();
		$("#changeButton").text("Feed anzeigen");
	}else{
		$('#div2').show();
		$("#changeButton").text("Feed ausblenden");
	}
});
</script>
<!-- FEED ENDE -->

<div id="treebody" class="container">
	<h3 style="margin-botttom:0" align="center">Herzlich Willkommen beim Studienführer!</h3>
	<br>
	
	<div id="result"></div>
	<?php
		if(isset($_GET['suchfeld']) && $_GET['suchfeld'] != 'Übersicht Startseite'){
			echo 	'<div class="alert alert-warning alert-dismissable fade in">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					  <strong>Hinweis!</strong> Leider konnten wir für deine Suche keine Ergebnisse finden.
					</div>';
		}
	 ?>

	<?php
	//Beim ersten Besuch: Badge vergeben
	$result2 = mysqli_query($con, "SELECT * FROM users_badges WHERE user_id = ".$userRow['user_ID']." AND badge_id = 59");
	if(mysqli_num_rows($result2) == 0){ //Wenn badge noch nicht vorhanden
		$sql2="INSERT INTO `users_badges`(`user_id`, `badge_id`) VALUES (".$userRow['user_ID'].",59)";
		if ($con->query($sql2) == TRUE) {
			echo "
				<div>
					<div style=\"border: none; border-radius:3px; background-color:#F7D358; padding:10px; padding-bottom:20px;\">
						<h4 align=\"center\"><b>Schön, dass du es hierher geschafft hast!</b></h4>
						<p align=\"center\">Du hast eine neue Errungenschaft freigeschaltet. Sieh sie dir gleich an unter: <a href=\"userProfile.php#achievements\">Meine Errungenschaften</a>!</p>
					</div>
					<br>
				</div>
			";
		}
	}
	
	//Beim ersten Besuch: Zeile in user_notifications für neue User erstellen
	$result3 = mysqli_query($con, "SELECT * FROM user_notifications WHERE user_id = ".$userRow['user_ID']."");
	if(mysqli_num_rows($result3) == 0){
		$sql3 = "
			INSERT INTO `user_notifications`(`user_id`, `own_questions`, `user_messages`)
			VALUES (".$userRow['user_ID'].",1,1)
		";
		mysqli_query($con, $sql3);
	}
		
	?>
	
	<div class="goToBox" id="goToBox">
		<h4 style="margin-botttom:0" align="center"><b>Hier findest du deine Veranstaltung</b></h4>

		<div class="contenedor_tree">
			<div id="searchfield2" style="width:100%; max-width:620px; min-width:180px; margin:10px">
				<form action="tree.php" method="get" class="navbar-form" style="padding:0;margin:0">
				  <div style="display:table;" class="input-group">
					<input type="text" class="form-control suchen-autocomplete" name="suchfeld" placeholder="Stichwortsuche">
					<!--<span style="width: 1%;" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>-->
					<div class="input-group-btn" style="width:1%">
					  <button class="btn btn-default">
						<i class="glyphicon glyphicon-search"></i>
					  </button>
					</div>
				  </div>
				</form>
			</div>
		</div>

		<div class="contenedor_tree">		
			<button onclick="location.href='search.php?manner=list'" class="btn btn-primary contenido_boxtree" style="">Veranstaltungsverzeichnis</button>
			<button onclick="location.href='search.php?manner=search'" class="btn btn-primary contenido_boxtree" style="">Erweiterte Suche</button>
		</div>
	
	</div>
	
	<script>
	$(document).ready(function(){ //Größe Suchfeld
		if($('#goToBox').height() > 150){
			$("#searchfield2").css("maxWidth", 300);
		}
		
		$(window).resize(function(){
			if($('#goToBox').height() > 150){
				$("#searchfield2").css("maxWidth", 300);
			}else{
				$("#searchfield2").css("maxWidth", 620);
			}
		});
	});
	</script>

	<br>
	
	<!--Neuigkeiten-->
	<?php
	$note = array();
	$color = array();
	$sql = "SELECT * FROM notes";
	$result = mysqli_query($con, $sql);
	while($row = mysqli_fetch_assoc($result)){
		$note[$row['name']] = $row['content'];
		$color[$row['name']] = $row['color'];
	}
	
	switch($color['noteLeft']) {
		case "blue":
			$colorLeft = "#e6f3ff";
			break;
		case "orange":
			$colorLeft = "#fff0e2";
			break;
		default:
			$colorLeft = "#ffffff";
	}
	
	switch($color['noteMiddle']) {
		case "blue":
			$colorMiddle = "#e6f3ff";
			break;
		case "orange":
			$colorMiddle = "#fff0e2";
			break;
		default:
			$colorMiddle = "#ffffff";
	}
	
	switch($color['noteRight']) {
		case "blue":
			$colorRight = "#e6f3ff";
			break;
		case "orange":
			$colorRight = "#fff0e2";
			break;
		default:
			$colorRight = "#ffffff";
	}

	?>
	
	<div style="background-color:#F8F8F8; padding: 10px">
	
		<h4 style="margin-botttom:0" align="center"><b>Das sind aktuelle Neuigkeiten</b></h4>
		<br>
		
		<div class="row">
			<div class="col-md-4">
				<div class="notes" style="background-color:<?php echo $colorLeft?>;margin-bottom:5px;">
					<div class="innernote" id="noteLeft">
						<?php echo $note['noteLeft'];?>
					</div>
				</div>
			</div>
			
			<div class="col-md-4 notesTop">
				<div class="notes" style="background-color:<?php echo $colorMiddle?>;margin-bottom:5px;">
					<div class="innernote" id="noteMiddle">
						<?php echo $note['noteMiddle'];?>
					</div>
				</div>
			</div>
			
			<div class="col-md-4">
				<div class="notes" style="background-color:<?php echo $colorRight?>">
					<div class="innernote" id="noteRight">
						<?php echo $note['noteRight'];?>
					</div>
				</div>
			</div>	
		</div>
	
	</div>
	
	<br>
	
	<!--Feed-->
	<div style="background-color:#F8F8F8; padding: 10px">
	
		<h4 style="margin-botttom:0" align="center"><b>Dein Feed zeigt für dich relevante Informationen</b></h4>
		<br>
			
		<!--Neue Bewertungen zu Veranstaltungen, die als Fav markiert wurden?-->
		<?php
		$id=$userRow['user_ID'];
		$sql="
			SELECT DISTINCT * FROM(		
				SELECT DISTINCT ratings.time_stamp AS r_time_stamp, ratings.subject_ID AS subject_ID, ratings.comment, subjects.subject_name FROM ratings
				JOIN subjects ON ratings.subject_ID = subjects.ID
				WHERE ratings.subject_ID IN (SELECT DISTINCT favourites.subject_ID FROM favourites WHERE user_ID = $id)
				ORDER BY ratings.subject_ID, ratings.time_stamp DESC
			) AS subquery
			GROUP BY subject_ID
			LIMIT 5
		";
		$result=mysqli_query($con, $sql);
		if(mysqli_num_rows($result)!=0){
			echo "Hier sind die <strong>neusten Kommentare</strong> zu Veranstaltungen, die du als Favorit markiert hast.";
		}else{
			echo "<i>Hier erscheinen die neusten Kommentare zu Veranstaltungen, die du als Favorit markiert hast.</i><br>";
		}
		while($row = mysqli_fetch_assoc($result)){
			?>
				<div style="border-left:solid 5px grey; border-radius:3px; padding:5px; margin:5px; margin-top:8px; margin-bottom:8px;">
					<p>
						<a href="index.php?subject=<?php echo $row['subject_ID']?>"><?php echo $row['subject_name']?></a>
						<span style="color:grey;">| <?php echo time_elapsed_string($row['r_time_stamp'])?></span>
					</p>
					<div>
						<?php echo $row['comment']?>
					</div>
				</div>
			<?php
		}
		?>
		<br>
		
		<!-- Unbeantwortete Fragen -->
		<?php
		$sql="
			SELECT DISTINCT *, questions.time_stamp AS q_time_stamp FROM questions
			JOIN subjects ON questions.subject_ID = subjects.ID
			WHERE questions.ID NOT IN (SELECT DISTINCT answers.question_ID FROM answers) AND questions.subject_ID IN (SELECT DISTINCT ratings.subject_ID FROM ratings WHERE user_ID = $id) AND questions.user_ID != $id
			LIMIT 5
		";
		
		$result_q=mysqli_query($con, $sql);
		if(mysqli_num_rows($result_q)!=0){
			echo "Es gibt <strong>unbeantwortete Fragen</strong> zu Veranstaltungen, die du bewertet hast. Kannst du helfen?";
		}
		while($row = mysqli_fetch_assoc($result_q)){
			?>
				<div style="border-left:solid 5px grey; border-radius:3px; padding:5px; margin:5px; margin-top:8px; margin-bottom:8px;">
					<p>
						<a href="index.php?subject=<?php echo $row['ID']?>"><?php echo $row['subject_name']?></a>
						<span style="color:grey;">| <?php echo time_elapsed_string($row['q_time_stamp'])?></span>
					</p>
					<div>
						<?php echo $row['question']?>
					</div>
				</div>
			<?php
		}
		if(mysqli_num_rows($result_q)!=0){
			echo "<br>";
		}
		?>
		
		<!--Veranstaltungen ohne Links-->
		<?php
		$sql="
			SELECT subjects.ID AS subject_ID, subjects.subject_name AS subject_name FROM `subjects`
			LEFT JOIN ratings ON subjects.ID = ratings.subject_ID
			WHERE (subjects.facebook = '' AND subjects.studydrive = '') AND ratings.user_ID = $id
			ORDER BY ratings.time_stamp
			LIMIT 5
		";
		$result=mysqli_query($con, $sql);
		if(mysqli_num_rows($result)!=0){
			echo "Hier sind die von dir zuletzt bewerteten Veranstaltungen, zu denen <b>noch keine hilfreichen Links eingetragen</b> wurden. Hast du welche parat?";
		}
		while($row = mysqli_fetch_assoc($result)){
			?>
				<div style="border-left:solid 5px grey; border-radius:3px; padding:5px; margin:5px; margin-top:8px; margin-bottom:8px;">
					<a href="index.php?subject=<?php echo $row['subject_ID']?>"><?php echo $row['subject_name']?></a>
				</div>
			<?php
		}
		?>
	</div>
	
	<br>
	
	<!--Veranstaltung des Tages-->
	<?php
	$result=mysqli_query($con, "SELECT value FROM help WHERE name='subjectOfTheDay'");
	$row=mysqli_fetch_assoc($result);
	$today = date("Ymd");
	if($row['value'] != $today){ //Update if necessary
		$sql="
			SELECT subjects.ID AS subject_ID, subject_name FROM subjects
			ORDER BY RAND()
			LIMIT 1
		";
		$result2=mysqli_query($con, $sql);
		$row2=mysqli_fetch_assoc($result2);
		$newSubjectId = $row2['subject_ID'];
		
		mysqli_query($con, "UPDATE help SET value=$today WHERE  name='subjectOfTheDay'");
		
		mysqli_query($con, "UPDATE help SET value2='$newSubjectId' WHERE name='subjectOfTheDay'");
	}
	
	$result=mysqli_query($con, "SELECT value2 FROM help WHERE name='subjectOfTheDay'");
	$row=mysqli_fetch_assoc($result);
	
	$s_id = $row['value2'];
	$sql="
		SELECT subjects.ID AS subject_ID, subject_name FROM subjects
		WHERE subjects.ID = $s_id
	";
	$result=mysqli_query($con, $sql);
	$row=mysqli_fetch_assoc($result);
	?>
	
	<!--Quicklinks-->
	<div>
		<h4 style="margin-botttom:0" align="center"><b>Interessante Links</b></h4>
		
		<div class="contenedor_tree">
			<button onclick="location.href='achievements.php'" class="btn contenido_tree">Errungenschaften-Ranking</button>
			<button onclick="location.href='userProfile.php#achievements'" class="btn contenido_tree">Freunde werben</button>
			<button onclick="location.href='ranking.php'" class="btn contenido_tree">Bewertungs-Ranking</button>
			<button onclick="location.href='stats.php'" class="btn contenido_tree">Bewertungs-Statistiken</button>
			<button onclick="location.href='about.php#community_guidelines'" class="btn contenido_tree">Gemeinschaftsrichtlinien</button>
		</div>
	
	</div>
	
	<hr>
	
	<div style="padding-top:15px; text-align:center">
		<p style="color:grey">Offen für Neues? Veranstaltung des Tages! Was hälst du von:</p>
		<h3 style="margin:0">
			<a href="index.php?subject=<?php echo $row['subject_ID']?>"><?php echo $row['subject_name']?></a>
		</h3>
	</div>
	
	<br><br>
	
	
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
?>
	
</body>
</html>
