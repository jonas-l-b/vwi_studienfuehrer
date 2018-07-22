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
				<p class="more">
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
				<p class="more">
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
	
	<div id="result"></div>
	<?php
		if(isset($_GET['suchfeld']) && $_GET['suchfeld'] != 'Übersicht Startseite'){
			echo 	'<div class="alert alert-warning alert-dismissable fade in">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					  <strong>Hinweis!</strong> Leider konnten wir für deine Suche keine Ergebnisse finden.
					</div>';
		}
	 ?>
	 
	<!--Karussell
	<div class="container">	
	  <div style="margin-left:10%; margin-right:10%" id="myCarousel" class="carousel slide" data-ride="carousel">
		<!-- Indicators 
		<ol class="carousel-indicators">
		  <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
		  <li data-target="#myCarousel" data-slide-to="1"></li>
		  <li data-target="#myCarousel" data-slide-to="2"></li>
		</ol>

		<!-- Wrapper for slides 
		<div class="carousel-inner">
		  <div class="item active">
			<img src="pictures/carousel_afterLogin/carouselAfter_one.jpg" style="width:100%;">
		  </div>

		  <div class="item">
			<img src="pictures/carousel_afterLogin/carouselAfter_two.jpg" style="width:100%;">
		  </div>

		  <div class="item">
			<img src="pictures/carousel_afterLogin/carouselAfter_three.jpg" style="width:100%;">
		  </div>
		</div>

		<!-- Left and right controls 
		<a class="left carousel-control" href="#myCarousel" data-slide="prev">
		  <span class="glyphicon glyphicon-chevron-left"></span>
		  <span class="sr-only">Previous</span>
		</a>
		<a class="right carousel-control" href="#myCarousel" data-slide="next">
		  <span class="glyphicon glyphicon-chevron-right"></span>
		  <span class="sr-only">Next</span>
		</a>
	  </div>
	</div>
 
	<hr>
-->	
	<div style="border: lightgrey solid 1px; border-radius:3px; background-color:#e6f3ff; padding:15px; padding-bottom:30px;">
		<h2 id="auswahl" align="center">Wie möchtest du deine Veranstaltung finden?</h2>
		<div align="center">
				<a href="search.php?manner=list" style="width:330px" class="btn btn-primary">Veranstaltung aus Verzeichnis wählen</a>
				<a href="search.php?manner=search" style="width:330px" class="btn btn-primary" >Veranstaltungen nach Kriterien durchsuchen</a>
		</div>
	</div>
	
	<hr>
	
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
	
	<div class="row">
		<div class="col-md-4">
			<div class="notes" style="background-color:<?php echo $colorLeft?>">
				<div id="noteLeft" style="display: table-cell; vertical-align: middle;">
					<?php echo $note['noteLeft'];?>
				</div>
			</div>
		</div>
		
		<div class="col-md-4 notesTop">
			<div class="notes" style="background-color:<?php echo $colorMiddle?>">
				<div id="noteMiddle" style="display: table-cell; vertical-align: middle;">
					<?php echo $note['noteMiddle'];?>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="notes" style="background-color:<?php echo $colorRight?>">
				<div id="noteRight" style="display: table-cell; vertical-align: middle;">
					<?php echo $note['noteRight'];?>
				</div>
			</div>
		</div>	
	</div>
	
	<!-- Unbeantwortete Fragen -->
	<?php
	$id=$userRow['user_ID'];
	$sql="
		SELECT DISTINCT *, questions.time_stamp AS q_time_stamp FROM questions
		JOIN subjects ON questions.subject_ID = subjects.ID
		WHERE questions.ID NOT IN (SELECT DISTINCT answers.question_ID FROM answers) AND questions.subject_ID IN (SELECT DISTINCT ratings.subject_ID FROM ratings WHERE user_ID = $id)
		LIMIT 5
	";
	
	$result=mysqli_query($con, $sql);
	if(mysqli_num_rows($result)!=0){
		echo "<hr>";

		echo "Es gibt <strong>unbeantwortete Fragen</strong> zu Veranstaltungen, die du bewertet hast. Kannst du helfen?";
	}
	while($row = mysqli_fetch_assoc($result)){
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
	?>
	
	<!--Neue Bewertungen zu Veranstaltungen, die als Fav markiert wurden?-->
	
	<?php
	$sql="
		SELECT DISTINCT ratings.time_stamp AS r_time_stamp, ratings.subject_ID, ratings.comment, subjects.subject_name FROM ratings
		JOIN subjects ON ratings.subject_ID = subjects.ID
		WHERE ratings.subject_ID IN (SELECT DISTINCT favourites.subject_ID FROM favourites WHERE user_ID = $id)
		ORDER BY ratings.time_stamp DESC
		LIMIT 5
	";
	$result=mysqli_query($con, $sql);
	if(mysqli_num_rows($result)!=0){
		echo "<br>";
		echo "Hier sind die <strong>neusten Kommentare</strong> zu Veranstaltungen, die du als Favorit markiert hast.";
	}
	while($row = mysqli_fetch_assoc($result)){
		?>
			<div style="border-left:solid 5px grey; border-radius:3px; padding:5px; margin:5px; margin-top:8px; margin-bottom:8px;">
				<p>
					<a href="index.php?subject=<?php echo $row['ID']?>"><?php echo $row['subject_name']?></a>
					<span style="color:grey;">| <?php echo time_elapsed_string($row['r_time_stamp'])?></span>
				</p>
				<div>
					<?php echo $row['comment']?>
				</div>
			</div>
		<?php
	}
	?>
	
	<br><br>
	
</body>
</html>
