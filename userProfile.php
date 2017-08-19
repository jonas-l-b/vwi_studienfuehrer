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
	$u_email = $userRow['email'];
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
		$email = strip_tags($_POST['email']);
		$degree = strip_tags($_POST['degree']);
		$advance = strip_tags($_POST['advance']);
		$semester = strip_tags($_POST['semester']);
		
		$q1 = mysqli_query($con,"
			UPDATE users
			SET email = '".$email."', degree = '".$degree."', advance = '".$advance."', semester = '".$semester."'
			WHERE user_ID = '".$userRow['user_ID']."'
		");
		
		if($q1==true){
			$msg = "
				<div class='alert alert-success'>
					<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Die Änderungen wurden erfolgreich gespeichert.
				</div>
			";
			
			//Hier ändern, damit Änderungen direkt nach Speichern angezeigt werden (nicht erst nach refresh)
			$u_email = $email;
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
	<hr>
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
					<td><?php echo $u_email?></td>
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
			<button type="button" a href="#myModal" role="button" class="btn btn-primary" data-toggle="modal">Passwort ändern</button>
		</form>
	</div>
	
	<div <?php echo $displayEdit?>>
		<form method="post">
			<table class="table" style="border-top:solid; border-top-color:white">
				<tbody>
					<tr>
						<th>E-Mail:</th>
						<td>
							<div class="form-group">
								<input value="<?php echo $userRow['email']?>" name="email" type="text" class="form-control" placeholder="E-Mail" required />
							</div>
						</td>
					</tr>
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
								<input value="<?php echo $userRow['semester']?>" name="semester" type="text" class="form-control" placeholder="Fachsemester" required />
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

</body>
</html>
