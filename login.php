<?php

include "header.php";

?>

<?php
session_start();
require_once 'connect.php';

if (isset($_SESSION['userSession'])!="") {
	//header('Location: tree.php');
	echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='tree.php';</SCRIPT>");
	exit;
}

if (isset($_POST['btn-login'])) {
 
	$email = strip_tags($_POST['email']);
	$password = strip_tags($_POST['password']);
	 
	$email = $con->real_escape_string($email);
	$password = $con->real_escape_string($password);
	 
	$query = $con->query("SELECT user_ID, email, password FROM users WHERE email='$email'");
	$row=$query->fetch_array();
	 
	$count = $query->num_rows; // if email/password are correct returns must be 1 row
 
	if (password_verify($password, $row['password']) && $count==1) {
		
		$sql="
			SELECT *
			FROM users
			WHERE email = '".$email."';
		";
		$result = mysqli_query($con,$sql);
		$row2 = mysqli_fetch_assoc($result);
		
		if($row2['active'] == 0){
			$msg = "<div class='alert alert-danger'>
				<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Dieser Account wurde noch nicht aktiviert!
				</div>";
		}else{
			$_SESSION['userSession'] = $row['user_ID'];
			//Set cookie if remember me checked
			$check = $_POST['rememberMe'];
			if($check == "on"){
				$series = hash("sha256", (rand(0,1000)));
				$token = hash("sha256", (rand(0,1000)));
				?><script>setCookie("<?php echo $series ?>","<?php echo $token ?>",30);</script><?php
			}
			
			//echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='tree.php';</SCRIPT>");
			
			?><script>
			function setCookie(svalue,tvalue,exdays) {
				var d = new Date();
				d.setTime(d.getTime() + (exdays*24*60*60*1000));
				var expires = "expires=" + d.toGMTString();
				document.cookie = "series" + "=" + svalue + ";" + "token" + "=" + tvalue + ";" + expires + ";path=/";
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

			function checkCookie() {
				var user=getCookie("username");
				if (user != "") {
					alert("Welcome again " + user);
				} else {
				   user = prompt("Please enter your name:","");
				   if (user != "" && user != null) {
					   setCookie("username", user, 30);
				   }
				}
			}
			</script><?php
		}
	}else{
		$msg = "<div class='alert alert-danger'>
			<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Benutzername und Passwort stimmen nicht überein!
			</div>";
		$memory_mail = $email;
	}

	$con->close();

}
?>
<body>
<div class="container">
	<h1>Willkommen zum Studienführer!</h1>
	<p>Der Studienführer sammelt die Erfahrungen vieler Wiwis am KIT, um den nächsten Generationen die Fächerwahl zu erleichtern.</p>
	<p>Um ihn zu nutzen musst du dich zuerst einloggen oder - falls noch nicht geschehen - registrieren. Wir haben uns für die Account-Variante entschieden, um eine hohe Qualität der bereitgestellten Informationen zu gewährleisten.</p>
	<p>Viel Spaß beim Stöbern!</p>
</div>
<div class="signin-form">
	<div class="container">
		<form class="form-signin" method="post" id="login-form">
			<h3 class="form-signin-heading">Hier einloggen:</h3><hr />
			
			<?php
			if(isset($msg)){
				echo $msg;
			}
			?>
			
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
					<a href="#" data-trigger="focus" data-toggle="popoverRememberMe" title="Hierzu wird ein Cookie auf deiner Festplatte gespeichert." data-content="Falls du dich innerhalb von 30 Tagen nicht wieder einloggst, wird dieser Cookie ungültig und du musst dich erneut einloggen. Mit der Auswahl dieser Checkbox und damit der Nutzung dieser Funktion akzeptierst du unsere Verwendung von Cookies.">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<script>
					$(document).ready(function () {
						$('[data-toggle="popoverRememberMe"]').popover();
						
						$('#rememberMe').change(function() {
							if ($(this).prop('checked')) {
								alert("Mit der Auswahl dieser Checkbox und damit der Nutzung dieser Funktion akzeptierst du unsere Verwendung von Cookies."); //checked
							}
						});
					});
					</script>
				</label>
			</div>
			
			<a href="#" id="openPWRModal">Passwort vergessen?</a>
			
			<hr>
			
			<div class="form-group">
				<button type="submit" class="btn btn-primary" name="btn-login" id="btn-login">
					<span class="glyphicon glyphicon-log-in"></span> &nbsp; Einloggen
				</button> 
				
				<a href="register.php" class="btn btn-default" style="float:right;">Registrieren</a>
				
			</div>  
		</form>

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
<script>
	$('#passwortvergessenmodal').on('shown.bs.modal', function () {
		$("#PWrecoveryEmailInput").focus(); //fokussiert den email input automatisch
	});
	$('#openPWRModal').click(function () {
		$('#passwortvergessenmodal').modal({
			show: true						//triggert das öffnen des modals
		});
	});
</script>
</body>
</html>