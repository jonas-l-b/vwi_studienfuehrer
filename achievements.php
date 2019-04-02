<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;

?>
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
				echo "<script>alert(\"Du hast eine neue Errungenschaft für das erfolgreiche Werben eines Kommilitonenden freigeschaltet!\");</script>";
			}
		}
	}
}	
?>

<div id="load">
	<br><br><div class="loader"><div></div></div><br><br>
	<p style="text-align:center">Die Errungenschafts-Rangliste wird geladen.</p>
</div>

<script>
window.onload = function () {
	$("#main").show();
	$("#load").hide();
}
</script>

<div id="main" style="display:none" class="container">

	<!--Errungenschaft des Tages-->
	<?php
	$result=mysqli_query($con, "SELECT value FROM help WHERE name='achievementOfTheDay'");
	$row=mysqli_fetch_assoc($result);
	$today = date("Ymd");
	if($row['value'] != $today){ //Update if necessary
		$sql="
			SELECT * FROM badges
			ORDER BY RAND()
			LIMIT 1
		";
		$result2=mysqli_query($con, $sql);
		$row2=mysqli_fetch_assoc($result2);
		$newBadgeId = $row2['id'];
		
		mysqli_query($con, "UPDATE help SET value=$today WHERE  name='achievementOfTheDay'");
		
		mysqli_query($con, "UPDATE help SET value2='$newBadgeId' WHERE name='achievementOfTheDay'");
	}
	
	$result=mysqli_query($con, "SELECT value2 FROM help WHERE name='achievementOfTheDay'");
	$row=mysqli_fetch_assoc($result);
	
	$b_id = $row['value2'];
	$sql="
		SELECT * FROM badges
		WHERE id = $b_id
	";
	$result=mysqli_query($con, $sql);
	$row=mysqli_fetch_assoc($result);
	?>

	<h4><a href="#" data-toggle="modal" data-target="#sneakAnAchievementModal">Sneak a Errungenschaft</a></h4>

	<div id="sneakAnAchievementModal" class="modal fade" role="dialog">
		<div class="modal-dialog" >
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Sneak a Errungenschaft!</h4>
				</div>
				<div class="modal-body">
					<p>Jeden Tag wird hier angezeigt, wie du eine zufällig ausgewählte Errungenschaft erringen kannst. Reinschauen lohnt sich!</p>
					
					<div style="border: solid lightgrey 3px; max-width: 330px; padding: 5px; background-color:#f2f2f2; display: inline-block; margin:5px;">
						<table style="width:100%">
							<tr>
								<td style="width:1%; padding:5px;"><img src="pictures/badges/<?php echo $row['image']?>" class="media-object" style="width:80px; background:rgb(20,90,157); border: 4px solid white; padding:5px;"></td>
								<td>
									<table style="width:100%">
										<tr>
											<td class="enter" style="text-align:left; font-size:17px;"><b><?php echo $row['name']?></b></td> 
										</tr>
										<tr>
											<td style="text-align:left;"><?php echo $row['description']?> | <?php echo $row['points']?> Punkte</td> 
										</tr>
									</table>
								</td> 
							</tr>
						</table>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
				</div>
			</div>
		</div>
	</div>

	<h3 style="margin-bottom:0">Errungenschaften-Rangliste</h3>
	<p>
	<?php
	$result = mysqli_query($con, "SELECT SUM(points) AS sum_points FROM badges");
	$row = mysqli_fetch_assoc($result);
	echo "Maximalpunktzahl: " . $row['sum_points'] . " Punkte";
	?>
	</p>
	
	<table style="width:100%; text-align:left;">
		<?php
		$sql="
			SELECT *, sum(badges.points) AS sum_of_points FROM badges
			JOIN users_badges ON badges.id = users_badges.badge_id
			GROUP BY users_badges.user_id
			ORDER BY sum_of_points DESC
		";
		$result=mysqli_query($con, $sql);
		
		$i = 1;
		$lineExistsNot1500 = true;
		$lineExistsNot2500 = true;
		while($row = mysqli_fetch_assoc($result)){
			//username
			$row2 = mysqli_fetch_assoc(mysqli_query($con, "SELECT username FROM `users` WHERE user_ID = ".$row['user_id'].""));
			//points
			$sql3="
				SELECT sum(badges.points) AS sum_of_points FROM badges
				JOIN users_badges ON badges.id = users_badges.badge_id
				WHERE users_badges.user_id = ".$row['user_id']."
			";
			$result3=mysqli_query($con, $sql3);
			$row3 = mysqli_fetch_assoc($result3);
			
			//Divider: over 2500 points
			if($lineExistsNot2500 AND $row3['sum_of_points'] < 2500){
				echo "
					<tr>
						<td colspan=\"4\" style=\"border-top:solid white 5px; border-bottom: solid white 5px; color:white; text-align:center\" bgcolor=\"grey\">
							<span class=\"glyphicon glyphicon-arrow-up\"></span> Errungenschaften-Erringer mit 2500 Punkten oder mehr erhalten einen <a style=\"color:white\" href=\"#\" data-toggle=\"modal\" data-target=\"#gutscheinModal2500\"><u>weiteren Gutschein</u></a>! <span class=\"glyphicon glyphicon-arrow-up\"></span>
						</td>
					</tr>	
				";
				$lineExistsNot2500 = false;
			}
			
			//Divider: over 1500 points
			if($lineExistsNot1500 AND $row3['sum_of_points'] < 1500){
				echo "
					<tr>
						<td colspan=\"4\" style=\"border-top:solid white 5px; border-bottom: solid white 5px; color:white; text-align:center\" bgcolor=\"grey\">
							<span class=\"glyphicon glyphicon-arrow-up\"></span> Errungenschaften-Erringer mit 1500 Punkten oder mehr erhalten einen <a style=\"color:white\" href=\"#\" data-toggle=\"modal\" data-target=\"#gutscheinModal1500\"><u>Gutschein</u></a>! <span class=\"glyphicon glyphicon-arrow-up\"></span>
						</td>
					</tr>
				";
				$lineExistsNot1500 = false;
			}
			
			?>
			
			<?php
			if($detect->isMobile()){
				$fontsize = "";
			}else{
				$fontsize = "font-size:18px;";
			}
			
			?>
			
			<tr <?php if($row['user_id'] == $userRow['user_ID']) echo "bgcolor=\"#F7D358\""?>>
				<td style="<?php echo $fontsize ?>; padding-bottom: 10px; padding-top:10px; padding-right:10px;text-align:center">
					<?php echo $i ?>
				</td>
				<td style="<?php echo $fontsize ?>">
					<?php echo $row2['username'];?>
				</td>
				<td style="<?php echo $fontsize ?>">
					<?php echo $row3['sum_of_points']?> Punkte
				</td>
				<td>
					<a style="<?php echo $fontsize ?>" href="#" data-toggle="modal" data-target="#achievementsModal<?php echo $row['user_id']?>">Errungenschaften ansehen</a>

					<!--Liste Errungenschaften Start-->
					<div id="achievementsModal<?php echo $row['user_id']?>" class="modal fade" role="dialog">
						<div class="modal-dialog">

						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Errungenschaften von <b><?php echo $row2['username'];?></b></h4>
							</div>
							<div class="modal-body">
						  
							<div style="text-align:center;">
							
								<?php
								$sql_ach1="
									SELECT b.*, (CASE WHEN ub.id IS NOT NULL THEN '1' ELSE NULL END) AS badgeStatus
									FROM badges b
									LEFT JOIN users_badges ub ON b.id = ub.badge_id AND ub.user_id = ".$row['user_id']."
									ORDER BY b.sequence
								";
								$result_ach1=mysqli_query($con, $sql_ach1);
								
								while($row_ach = mysqli_fetch_assoc($result_ach1)){
									if($row_ach['badgeStatus'] == 1){
										$color = "rgb(20,90,157)";
										$blurry = "";
										$name = $row_ach['name'];
										$description = $row_ach['description'];
										$blurryimage = "";
									}else{
										$color = "lightgrey";
										$blurry = "class=\"blurry\"";
										$name = "A name not yet detected!";
										$description = "Don't use source code to spy on badges!";
										$blurryimage = "blurryimage";
										
										//For development, comment for use
										/*
										$color = "rgb(20,90,157)";
										$blurry = "";
										$name = $row_ach['name'];
										$description = $row_ach['description'];
										$blurryimage = "";
										*/
									}
									
									?>
									<div style="border: solid lightgrey 3px; width: 270px; padding: 5px; background-color:#f2f2f2; display: inline-block; margin:5px;">
										<table style="width:100%">
											<tr>
												<td style="width:1%; padding:5px;"><img src="pictures/badges/<?php echo $row_ach['image']?>" class="media-object <?php echo $blurryimage?>" style="width:80px; background:<?php echo $color?>; border: 4px solid white; padding:5px;"></td>
												<td>
													<table style="width:100%">
														<tr>
															<td <?php echo $blurry?> style="text-align:left; font-size:15px;"><b><?php echo $name?></b></td> 
														</tr>
														<!--
														<tr>
															<td <?php echo $blurry?> style="text-align:left;"><?php //echo $description?> | <?php //echo $row_ach['points']?> Punkte</td> 
														</tr>
														-->
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
			
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
							</div>
						</div>

						</div>
					</div>
					<!--Liste Errungenschaften Ende-->

				</td>
			</tr>
			<?php
			$i++;
		}
		if($lineExistsNot2500){ //Falls alle Nutzer mehr als 2500 Punkte haben und Linie darum noch nicht eingefügt wurde
			echo "
				<tr>
					<td colspan=\"4\" style=\"border-top:solid white 5px; border-bottom: solid white 5px; color:white; text-align:center\" bgcolor=\"grey\">
						<span class=\"glyphicon glyphicon-arrow-up\"></span> Errungenschaften-Erringer mit 2500 Punkten oder mehr erhalten einen <a style=\"color:white\" href=\"#\" data-toggle=\"modal\" data-target=\"#gutscheinModal2500\"><u>weiteren Gutschein</u></a>! <span class=\"glyphicon glyphicon-arrow-up\"></span>
					</td>
				</tr>
			";
			$lineExistsNot2500 = false;
		}
		
		if($lineExistsNot1500){ //Falls alle Nutzer mehr als 1500 Punkte haben und Linie darum noch nicht eingefügt wurde
			echo "
				<tr>
					<td colspan=\"4\" style=\"border-top:solid white 5px; border-bottom: solid white 5px; color:white; text-align:center\" bgcolor=\"grey\">
						<span class=\"glyphicon glyphicon-arrow-up\"></span> Errungenschaften-Erringer mit 1500 Punkten oder mehr erhalten einen <a style=\"color:white\" href=\"#\" data-toggle=\"modal\" data-target=\"#gutscheinModal1500\"><u>Gutschein</u></a>! <span class=\"glyphicon glyphicon-arrow-up\"></span>
					</td>
				</tr>
			";
			$lineExistsNot1500 = false;
		}
		?>
	</table>
	
	<!-- Gutschein Modal 1500 -->
	<div id="gutscheinModal1500" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><!--Wie komme ich an den Gutschein?-->Gutscheine nach dem Launch geplant!</h4>
				</div>
				<div class="modal-body">
					<!--
					<p>Für besonders aktive Nutzer ist nach dem Launch des Studienführers die Ausgabe von Gutscheinen geplant. Da musst du dich noch ein bisschen gedulden :)</p>
					<p>Danke dennoch für deinen Einsatz!</p>
					<p>In der Zwischenzeit hast du hier einen Apfel: <span class="glyphicon glyphicon-apple"></span></p>
					-->
					<p>Wenn du über dieser Linie stehst: Herzlichen Glückwunsch zum Gewinn deines Gutscheins und vielen Dank, dass du den Studienführer so aktiv nutzt! Du trägst so bedeutend dazu bei, anderen Wiwis die Fächerwahl zu erleichtern.</p>
					<p>Um das zu honorieren, möchten wir dir einen Gutschein schenken. Du kannst ihn prinzipiell während unserer Sitzung (der Sitzung der VWI-ESTIEM Hochschulgruppe) abholen - sie findet jeden Dienstag um 19:30 Uhr in Gebäude 05.20, Raum 1C-01 statt. Bitte nimm einen Ausweis (z.B. Studi-Ausweis) mit, damit wir sichergehen können, dass du es auch wirklich bist. Wir kennen dich ja nicht - und sonst könnte ja jeder kommen :)</p>
					<p>Um sicherzugehen, dass wir deinen Gutschein auch dabei haben, melde dein Kommen vorher an: <a href="mailto:studienfuehrer@vwi-karlsruhe.de">studienfuehrer@vwi-karlsruhe.de</a>.</p>

					<p><b>Aktuelle Meldung: Dieses Semester gibt es keine Sitzung mehr. Wer trotzdem an seinen Gutschein will, kann sich gerne melden: <a href="mailto:studienfuehrer@vwi-karlsruhe.de">studienfuehrer@vwi-karlsruhe.de</a>.</b></p>

					<p>Wir konnten folgende Partner für unsere Gutschein-Aktion gewinnen:</p>
					<ul style="list-style-position:inside">
						<li>Phono Kraftbierbar</li>
						<li>Waldemars Suppenstüble</li>
						<li>Habibi</li>
						<li>Vogelbräu</li>
						<li>Oxford Pub</li>
						<li>AppClub</li>
					</ul>
					
					<p>Gutscheine gibt es nur so lange der Vorrat reicht!</p>
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Gutschein Modal 2500-->
	<div id="gutscheinModal2500" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><!--Wie komme ich an den Gutschein?-->Gutscheine nach dem Launch geplant!</h4>
				</div>
				<div class="modal-body">
					<p>Wenn du über dieser Linie stehst: Wow super, du bist ja super aktiv! Mit dem Überschreiten dieser Linie hast du dir einen zweiten Gutschein mehr als verdient!</p>
					<p>Die Koditionen sind wie beim ersten Gutschein und es gilt immer noch: Nur so lange der Vorrat reicht :)</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Bildlizenzen Modal -->
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

</body>