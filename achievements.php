<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div class="container">
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
		$lineExistsNot = true;
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
			if($lineExistsNot AND $row3['sum_of_points'] < 2500){
				echo "
					<td colspan=\"4\" style=\"border-top:solid white 5px; border-bottom: solid white 5px; color:white; text-align:center\" bgcolor=\"grey\">
						<span class=\"glyphicon glyphicon-arrow-up\"></span> Errungenschaften-Erringer mit 2500 Punkten oder mehr erhalten einen <a style=\"color:white\" href=\"#\" data-toggle=\"modal\" data-target=\"#gutscheinModal\"><u>Gutschein</u></a>! <span class=\"glyphicon glyphicon-arrow-up\"></span>
					</td>
				";
				$lineExistsNot = false;
			}
			
			?>
			<!--<tr style="border-top:solid 1px lightgrey">-->
			<tr <?php if($row['user_id'] == $userRow['user_ID']) echo "bgcolor=\"#F7D358\""?>>
				<td style="font-size:18px; padding-bottom: 10px; padding-top:10px; padding-right:10px;text-align:center">
					<?php echo $i ?>
				</td>
				<td style="font-size:18px;">
					<?php echo $row2['username'];?>
				</td>
				<td style="font-size:18px;">
					<?php echo $row3['sum_of_points']?> Punkte
				</td>
				<td>
					<a style="font-size:18px;" href="#" data-toggle="modal" data-target="#achievementsModal<?php echo $row['user_id']?>">Errungenschaften ansehen</a>

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
										$name = "This name, you know!";
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
															<td <?php echo $blurry?> style="text-align:left;"><?php echo $description?> | <?php echo $row_ach['points']?> Punkte</td> 
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
		if($lineExistsNot){ //Falls alle Nutzer mehr als 2500 Punkte haben und Linie darum noch nicht eingefügt wurde
			echo "
				<td colspan=\"4\" style=\"border-top:solid white 5px; border-bottom: solid white 5px; color:white; text-align:center\" bgcolor=\"grey\">
					<span class=\"glyphicon glyphicon-arrow-up\"></span> Errungenschaften-Erringer mit 2500 Punkten oder mehr erhalten einen <a style=\"color:white\" href=\"#\" data-toggle=\"modal\" data-target=\"#gutscheinModal\"><u>Gutschein</u></a>! <span class=\"glyphicon glyphicon-arrow-up\"></span>
				</td>
			";
			$lineExistsNot = false;
		}
		?>
	</table>
	
	<!-- Gutschein Modal -->
	<div id="gutscheinModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><!--Wie komme ich an den Gutschein?-->Gutscheine nach dem Launch geplant!</h4>
				</div>
				<div class="modal-body">
					<p>Für besonders aktive Nutzer ist nach dem Launch des Studienführers die Ausgabe von Gutscheinen geplant. Da musst du dich noch ein bisschen gedulden :)</p>
					<p>Danke dennoch für deinen Einsatz!</p>
					<p>In der Zwischenzeit hast du hier einen Apfel: <span class="glyphicon glyphicon-apple"></span></p>
					<!--
					<p>Erstmal herzlichen Glückwunsch zum Gewinn deines Gutscheins (oder zumindest dein Interesse daran) und vielen Dank, dass du den Studienführer so aktiv nutzt! Du trägst so bedeutend dazu bei, anderen Wiwis die Fächerwahl zu erleichtern.</p>
					<p>Um das zu honorieren, möchten wir dir einen Gutschein schenken. Prinzipiell kannst du ihn während unserer Sitzung (der Sitzung der VWI-ESTIEM Hochschulgruppe) abholen - sie findet jeden Dienstag um 19:30 Uhr in Gebäude 05.20, Raum 1C-01 statt. Um sicherzustellen, dass wir den Gutschein auch dabei haben, schreib uns bitte vorher eine E-Mail mit dem Datum, an dem du vorbeischauen willst. Die E-Mail geht an <a href="mailto:studienfuehrer@vwi-karlsruhe.de">studienfuehrer@vwi-karlsruhe.de</a>.</p>
					<p>Bitte nimm einen Ausweis (z.B. Studi-Ausweis) mit, damit wir sichergehen können, dass du es auch wirklich bist. Wir kennen dich ja nicht - und sonst könnte ja jeder kommen :)</p>
					<p>Wir freuen uns darauf, dich kennenzulernen!</p>
					-->
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
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
	</div>	
	
	
</div>

</body>