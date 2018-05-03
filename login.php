<?php
session_start();
require_once 'connect.php';
?>

<?php

include "header.php";
/*
//Für testzwecke:
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/
?>

<html>
<head>

  <style>
  body {
      position: relative;
  }
  ul.nav-pills {
      top: 20px;
      position: fixed;
  }
  div.col-sm-10 div {
  }
  
  @media screen and (max-width: 810px) {
    #section1, #section2, #section3, #section4  {
        margin-left: 150px;
    }
  }
  </style>
</head>

<script>
function setCookie(svalue,tvalue,uvalue,exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires=" + d.toGMTString();
	document.cookie = "vwistudi_series" + "=" + svalue + ";" + expires + ";path=/";
	document.cookie = "vwistudi_token" + "=" + tvalue + ";" + expires + ";path=/";
	document.cookie = "vwistudi_user" + "=" + uvalue + ";" + expires + ";path=/";

	//In Datenbank schreiben
	$.ajax({
		url: "setCookieInDB.php",
		type: "post",
		data: {series: svalue, token: tvalue, user_id: uvalue},
		success: function (data){
		},
		error: function() {
			alert("error");
		}
	});
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

</script>

<?php

//Weiterleiten, falls bereits eingeloggt
if (isset($_SESSION['userSession'])!="") {
	echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='tree.php';</SCRIPT>");
	exit;
//Checken, ob valider Cookie existiert und ggfls. einloggen
}else{
	if(isset($_COOKIE["vwistudi_series"]) && isset($_COOKIE["vwistudi_token"]) && isset($_COOKIE["vwistudi_user"])){
		$cSeries = $_COOKIE["vwistudi_series"];
		$cToken = $_COOKIE["vwistudi_token"];
		$cUser = $_COOKIE["vwistudi_user"];

		$result = mysqli_query($con, "SELECT * FROM remember_me WHERE series = '$cSeries' AND token = '$cToken' AND user_id = '$cUser'");
		if(mysqli_num_rows($result) == 1){
			//echo "LOGIN!";
			$_SESSION['userSession'] = $cUser;
			$u_logger->info("Der User mit der ID $cUser hat sich soeben über Cookie eingeloggt.");
			if (isset($_GET['url'])) {
				$url = $_GET['url'];
					echo ("<SCRIPT LANGUAGE='JavaScript'>document.location.href='".basename($url)."';</SCRIPT>"); //NUR UNTER LOCALHOST GETESTET
					//echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='tree.php';</SCRIPT>");
			}else{
				echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='tree.php';</SCRIPT>");
			}
		}elseif(mysqli_num_rows($result) > 1){
			echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=cookie_error';</SCRIPT>");
		}else{
			//Besucht ein Nutzer, der eingeloggt bleibt, die Seite, wird $series behalten und $token geändert.
			//So kann man mit einem gestohlenen Cookie sich nur so lange einloggen, bis der eigentliche Nutzer das tut (und den token ändert)
			//Wird hier nun ein Cookie präsentiert, der zwar eine Entsprechung von series und user_id in der Datenbank hat, dessen token aber nicht übereinstimmt, so ist der Cookie wahrscheinlich gestohlen
			$theft = mysqli_query($con, "SELECT * FROM remember_me WHERE series = '$cSeries' AND user_id = '$cUser' AND token != '$cToken'");
			if(mysqli_num_rows($theft) != 0){

				$subject = "[VWI-Studienführer] Jemand hat versucht, sich in deinen Studienführer-Account einzuloggen";

				if(mysqli_query($con, "DELETE FROM remember_me WHERE user_id = '$cUser'")){
					$body = "jemand hat versucht, sich mit einem von dir gestohlenen Cookie in deinen Studienführer-Account einzuloggen. Wir haben sicherheithalber alle deine Auto-Login-Daten gelöscht, sodass du dich bei deinem nächsten wieder einloggen musst.";
					$landing = "<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=cookie_theft';</SCRIPT>";
				}else{
					$body = "jemand hat versucht, sich mit einem von dir gestohlenen Cookie in deinen Studienführer-Account einzuloggen. Leider ist beim Löschen deiner Auto-Login-Daten ein Fehler aufgetreten. Lösche deine Cookies über das Browser-Menü und melde dich erneut mit der Eingeloggt-bleiben-Funktion beim Studienführer an, um deine Daten zu überschreiben. Falls du Fragen hast, wende dich direkt an die VWI-ESTIEM Hochschulgruppe.";
					$landing = "<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=cookie_theft_error';</SCRIPT>";
				}

				$result1 = mysqli_query($con, "SELECT email FROM users WHERE user_ID = '$cUser'");
				$row1 = mysqli_fetch_assoc($result1);

				EmailService::getService()->sendEmail($row1['email'], $row1['username'], $subject, $body);

				echo $landing;
			}
		}
	}
}
$disable = false;

if (isset($_POST['btn-login']) && $_POST['password'] != "") {

	$email = $_POST['email'];
	$password = $_POST['password'];

	$stmt = $con->prepare(" INSERT INTO anti_brute_force (user_id, login_failures)
							SELECT user_ID, 1
							FROM users
							WHERE email = ?
							ON DUPLICATE KEY UPDATE login_failures = login_failures + 1");
	if($stmt == false){
		$error = $con->errno . ' ' . $con->error;
		echo $error; // 1054 Unknown column 'foo' in 'field list'
		exit;
	}
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$stmt->close();

	$stmt1 = $con->prepare(" SELECT login_failures
							FROM anti_brute_force
							LEFT JOIN users
							ON anti_brute_force.user_id = users.user_ID
							WHERE users.email = ?");
	$stmt1->bind_param("s", $email);
	$stmt1->execute();
	$res = $stmt1->get_result();
	$stmt1->close();
	if($res == null || $res->fetch_assoc()['login_failures']< ConfigService::getService()->getConfig('login_tries_before_blocking')){

		$query = $con->query("SELECT user_ID, email, password, active FROM users WHERE email='$email'");
		$row=$query->fetch_array();

		$count = $query->num_rows; // if email/password are correct returns must be 1 row

		if (password_verify($password, $row['password']) && $count==1) {

			$con->query("DELETE FROM anti_brute_force WHERE user_id = ". $row['user_ID']);

			if($row['active'] == 0){
				$msg = "
					<div class='alert alert-warning'>
						<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Dieser Account wurde noch nicht aktiviert!
						&nbsp;
						<button class=\"btn btn-basic\" onclick=\"reSendActivation('".$email."')\" >Aktivierungslink erneut zuschicken</button>
					</div>
					";
			}else{
				$_SESSION['userSession'] = $row['user_ID'];
				//header('Location: tree.php');

				//Set cookie if remember me checked
				if(isset($_POST['rememberMe'])){
					$series = hash("sha256", (rand(0,1000)));
					$token = hash("sha256", (rand(0,1000)));

					?>
					<!--Infos unsichtbar speichern, um sie so JS übergeben zu können-->
					<span id="series" style="display:none"><?php echo $series ?></span>
					<span id="token" style="display:none"><?php echo $token ?></span>
					<span id="user-id" style="display:none"><?php echo $row['user_ID'] ?></span>

					<script>
					var s = $('#series').text();
					var t = $('#token').text();
					var u = $('#user-id').text();

					setCookie(s,t,u,30);
					</script>

					<?php
				}
				$u_logger->info("Der User mit der ID". $row['user_ID'] ."hat sich soeben eingeloggt.");
				echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='tree.php';</SCRIPT>");
			}
		}else{
			$msg = "<div class='alert alert-danger'>
				<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Email und Passwort stimmen nicht überein!
				</div>";
			$memory_mail = $email;
		}

		$con->close();
	}else{
		$disable = true;
		$msg = "<div class='alert alert-danger'>
				<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Du hast zu oft versucht, dich anzumelden und dabei ein falsches Passwort verwendet. Bitte setze dein Passwort jetzt zurück.
				</div>";
	}
}
?>


<body data-spy="scroll" data-target="#myScrollspy" data-offset="20">

<div class="container">
	<div class="row">
		<nav class="col-sm-2" id="myScrollspy">
			<ul class="nav nav-pills nav-stacked">
				<li class="active"><a href="#section1">Einloggen - Registrieren</a></li>
				<li><a href="#section2">Studienführer?</a></li>
				<li><a href="#section3">FAQ</a></li>
				<li><a href="#section4">Kontakt</a></li>
			</ul>
		</nav>
		<div class="col-sm-10">
			
			<div id="section1">
				<br>
				<h1 style="text-align:center">Willkommen beim Studienführer!</h1>
				<br>
				<div class="signin-form well" style="border-radius:5px">
					<h3 id="loginStart" class="form-signin-heading">Hier einloggen</h3><hr />
					<div id="alertMessage">
					<?php
					if(isset($msg)){
						echo $msg;
					}
					?>
					</div>
					
					<form class="form-signin" method="post" id="login-form">
						<div class="form-group">
							<input value="<?php if(isset($memory_mail)) echo $memory_mail ?>" type="email" class="form-control" placeholder="E-Mail" name="email" required />
						<span id="check-e"></span>
						</div>

						<div class="form-group">
							<input type="password" class="form-control" placeholder="Passwort" name="password" required />
						</div>

						<div class="checkbox">
							<label>
								<input type="checkbox" name="rememberMe" id="rememberMe"> Eingeloggt bleiben
								<a href="#" data-trigger="focus" data-toggle="popoverRememberMe" title="Um eingeloggt zu bleiben wird ein Cookie auf deiner Festplatte gespeichert." data-content="Nach 30 Tagen wird dieser Cookie ungültig und du musst dich erneut einloggen. Mit der Auswahl dieser Checkbox und damit der Nutzung dieser Funktion akzeptierst du die Verwendung der nötigen Cookies. Loggst du dich aus, werden die Cookies gelöscht. Cookies können außerdem jederzeit über deinen Browser gelöscht werden.">
									<span class="glyphicon glyphicon-question-sign"></span>
								</a>
								<script>
								$(document).ready(function () {
									$('[data-toggle="popoverRememberMe"]').popover();
								});
								</script>
							</label>
						</div>

						<a href="#" id="openPWRModal">Passwort vergessen/Passwort zurücksetzen</a>

						<hr>

						<div class="form-group">
							<button type="submit" class="btn btn-primary" name="btn-login" id="btn-login">
								<span class="glyphicon glyphicon-log-in"></span> &nbsp; Einloggen
							</button>

							<a href="register.php" class="btn btn-default" style="float:right;">Registrieren</a>

						</div>
					</form>
				</div>
				<br><hr><br>
			</div>
			
			<div id="section2">
				<br>
				<h1 style="text-align:center">Was ist der Studienführer?</h1>
				<br>
				<div style="margin-left:10%; margin-right:10%" id="myCarousel" class="carousel slide" data-ride="carousel">
				<!-- Indicators -->
				<ol class="carousel-indicators">
					<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
					<li data-target="#myCarousel" data-slide-to="1"></li>
					<li data-target="#myCarousel" data-slide-to="2"></li>
					<li data-target="#myCarousel" data-slide-to="3"></li>
				</ol>

				<!-- Wrapper for slides -->
				<div class="carousel-inner">
					<div class="item active">
						<img src="pictures/carousel/carousel_one.jpg" style="width:100%;">
					</div>

					<div class="item">
						<img src="pictures/carousel/carousel_two.jpg" style="width:100%;">
					</div>

					<div class="item">
						<img src="pictures/carousel/carousel_three.jpg" style="width:100%;">
					</div>

					<div class="item">
						<img src="pictures/carousel/carousel_four.jpg" style="width:100%;">
					</div>
				</div>

				<!-- Left and right controls -->
				<a class="left carousel-control" href="#myCarousel" data-slide="prev">
					<span class="glyphicon glyphicon-chevron-left"></span>
					<span class="sr-only">Previous</span>
				</a>
				<a class="right carousel-control" href="#myCarousel" data-slide="next">
					<span class="glyphicon glyphicon-chevron-right"></span>
					<span class="sr-only">Next</span>
				</a>
				</div>
				
				<div style="text-align:center;font-size: 1.4em;">
					<br>
					<p><b>Der Studienführer sammelt die Erfahrungen vieler Wiwis am KIT, um den nächsten Generationen die Fächerwahl zu erleichtern.</b></p>
					<p>Um ihn zu nutzen musst du dich zuerst einloggen oder - falls noch nicht geschehen - registrieren. Auf diesem Weg stellen wir die höchstmögliche Qualität der Informationen sicher.</p>
					<p><b>Viel Spaß beim Stöbern!</b></p>
				</div>
				<br><hr><br>
			</div>	  
			
			<div id="section3">         
				<br>
				<h1 style="text-align:center">Frequently Asked Questions</h1>
				<br>
				
				<!-- FAQs auch in about.php. Auch dort aktualisieren!-->
				<div class="panel-group" id="accordion">
					<div class="panel panel-default">
					  <div class="panel-heading">
						<h4 class="panel-title">
						  <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Warum muss ich mich für die Nutzung des Studienführers registrieren?</a>
						</h4>
					  </div>
					  <div id="collapse1" class="panel-collapse collapse">
						<div class="panel-body">Die Registrierung der einzelnen Nutzer ist notwendig, damit wir eine hohe Datenqualität gewährleisten können. 
						So wird beispielsweise verhindert, dass Nutzer die gleiche Veranstaltung mehrere Male bewerten. Durch die Bindung der Registrierung an
						die KIT-E-Mail-Adresse verhindern wir außerdem, dass Fake-Accounts angelegt werden können.<br>
						Weiterhin können wir so weitere Services wie das Speichern von Favoriten oder die Benachrichtigung für
						beantwortete Fragen zur Verfügung stellen.</div>
					  </div>
					</div>
					<div class="panel panel-default">
					  <div class="panel-heading">
						<h4 class="panel-title">
						  <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Fallen Kosten für die Registrierung und Nutzung des Studienführers an?</a>
						</h4>
					  </div>
					  <div id="collapse2" class="panel-collapse collapse">
						<div class="panel-body">Nein. Wir sind auch Studenten des KIT und haben den Studienführer ohne Gewinnabsichten entwickelt. Der Studienführer wird
						auch in Zukunft kostenlos bleiben: Kostenlos von Studenten für Studenten.</div>
					  </div>
					</div>
					<div class="panel panel-default">
					  <div class="panel-heading">
						<h4 class="panel-title">
						  <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">Wer hat den Studienführer entwickelt und wer betreibt ihn?</a>
						</h4>
					  </div>
					  <div id="collapse3" class="panel-collapse collapse">
						<div class="panel-body">Der Studienführer ist ein Projekt der VWI-ESTIEM Karlsruhe Hochschulgruppe e.V.</div>
					  </div>
					</div>
				</div>
				
				<br><hr><br>
			</div>
			
			<div id="section4">         
				<br>
				<h1 style="text-align:center">Kontakt</h1>
				<br>
				<p>Um mit uns bezüglich des Studienführers in Kontakt zu treten, wende dich bitte an <a href="mailto:studienfuehrer@vwi-karlsruhe.de">studienfuehrer@vwi-karlsruhe.de</a>.</p>
				<p>Informationen zu unserer Hochschulgruppe findest du unter <a target="_blank" href="http://www.vwi-karlsruhe.de">vwi-karlsruhe.de</a>.</p>
				
				<!--Damit man ein bisschen weiter runterscrollen kann-->
				<div id="whiteSpace"></div>
				<script>$('#whiteSpace').height(screen.height/1.5);</script>
			</div>
			
		</div>
	</div>
</div>

<!-- End of page. Modal für Passwort vergessen -->
<div id="passwortvergessenmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h2 class="modal-title">Passwort vergessen</h2> <!-- Dynamisch Name anpassen! -->
	</div>
	<div class="modal-body">
		<p id="pwrecovery"></p>
		<form action="recoverPW.php" method="POST">
			<p>Trage hier die E-Mail-Adresse ein, mit der du dich registriert hast:</p>

			<div class="form-group">
				<input type="email" class="form-control" id="PWrecoveryEmailInput" placeholder="E-Mail-Adresse" name="email" required />
			</div>

			<button type="submit" class="btn btn-primary" >Passwort zurücksetzen</button>
		</form>

	</div><!-- End of Modal body -->
	</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->

<!--Modal für Aktivierungsmail -->
<div id="resendactivationmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h2 class="modal-title">Aktivierungslink erneut zusenden</h2> <!-- Dynamisch Name anpassen! -->
	</div>
	<div class="modal-body">
		<div id="resendactivationmessage">
			<p>Durch Klick auf den Button senden wir dir erneut den Aktivierungslink an die von dir angegebene E-Mail-Adresse <strong><span id="activationMail">"uxxxx@student.kit.edu<span></strong>.<br>Falls du dich beim Eingeben deiner E-Mail vertippt hast, registriere dich bitte erneut.</p>
			<button class="btn btn-primary" onclick="reSendActivation_submit()">Link zuschicken</button>
			<button class="btn btn-default" data-dismiss="modal">Schließen</button>
		</div>
	</div><!-- End of Modal body -->
	</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<script>
	$('#passwortvergessenmodal').on('shown.bs.modal', function () {
		$("#PWrecoveryEmailInput").focus(); //fokussiert den email input automatisch
	});
	$('#openPWRModal').click(function () {
		$('#passwortvergessenmodal').modal({
			show: true						//triggert das öffnen des modals
		});
	});
	
	function reSendActivation(email){
		$('#resendactivationmodal').modal('show');
		$('#activationMail').html(email);
	};
	
	function reSendActivation_submit(){
		$.ajax({
			type: "POST",
			url: "reSendActivation2.php",
			data: {email: $('#activationMail').text().trim()},
			success: function(data) {
				if(data.trim() == "erfolg"){
					$('#resendactivationmessage').html("<div class='alert alert-success'><span class='glyphicon glyphicon-ok-sign'></span> &nbsp; Dein Aktivierungslink wurde erfolgreich verschickt!</div><button class=\"btn btn-default\" data-dismiss=\"modal\" onClick=\"closeModal()\">Schließen</button>");
				}else{
					$('#resendactivationmessage').html("<div class='alert alert-danger'><span class='glyphicon glyphicon-info-sign'></span> &nbsp; Beim Verschicken deines Aktivierungslinks ist ein Fehler aufgetreten. Bitte setze dich mit VWI-ESTIEM in Verbindung.</div><button class=\"btn btn-default\" data-dismiss=\"modal\" onClick=\"closeModal()\">Schließen</button>");
				}
			}
		});
	}
	
	function closeModal(){
		$('#alertMessage').empty();
	}
</script>

</body>
</html>
