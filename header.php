<!Doctype html>
<html lang="de">
<head>
	<style>
		@media (min-width: 768px) {
			.row {
				display:flex;
				<!--align-items:center;-->
			}
		}
	</style>
	

	<title>Studienführer VWI-ESTIEM Karlsruhe</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="apple-mobile-web-app-title" content="Studienführer-VWI-ESTIEM">
    <meta name="application-name" content="Studienführer-VWI-ESTIEM">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="res/css/style.css">
	<link rel="stylesheet" href="res/css/circle.css">
	<link rel="stylesheet" href="res/css/tree.css">
	<link rel="stylesheet" href="res/css/admin.css">
	<link rel="stylesheet" href="res/css/autocomplete.css">
	<link rel="stylesheet" href="res/css/index.css">
	<link rel="stylesheet" href="res/css/achievements.css">
	<link rel="stylesheet" href="res/lib/bootstrap-combobox/css/bootstrap-combobox.css">
	<link rel="stylesheet" href="res/css/userProfile.css">
	<link rel="stylesheet" href="res/css/snackbar.css">
	<link rel="stylesheet" type="text/css" href="semantic/dist/components/dropdown.min.css">
	<link rel="stylesheet" type="text/css" href="semantic/dist/components/transition.min.css">
	<link rel="stylesheet" type="text/css" href="semantic/dist/components/statistic.min.css">
	<link rel="shortcut icon" href="pictures/logo_studienfuehrer.ico"/><!-- das erste ist moeglicherweise redundant - noch checken -->
	<link rel="apple-touch-icon" sizes="57x57" href="pictures/logo/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="pictures/logo/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="pictures/logo/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="pictures/logo/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="pictures/logo/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="pictures/logo/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="pictures/logo/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="pictures/logo/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="pictures/logo/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="pictures/logo/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="pictures/logo/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="pictures/logo/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="pictures/logo/favicon-16x16.png">
	<link rel="manifest" href="pictures/logo/manifest.json">
	<meta name="msapplication-TileImage" content="pictures/logo/ms-icon-144x144.png">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="theme-color" content="#ffffff"><!-- To be changed to layout theme color -->

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-113288561-1"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());
	  gtag('config', 'UA-113288561-1', { 'anonymize_ip': true });
		<?php
		if(isset($userRow['user_ID'])){
			echo "gtag('set', 'user_id', '" . $userRow['user_ID'] . "'); // Set the user ID using signed-in user_id.";
		}
		?>
	</script>


	<script>
		//Alte Browser Warnung
		var $buoop = {
				notify:{i:-2,f:-2,o:-2,s:-1,c:-2},
				insecure:true,
				unsupported:true,
				api:5,
				test: false,
				text: "Buuuuh! Du benutzt ja {brow_name}. Der ist viel zu alt, der Browser! Hör mal, es ist ohnehin schon ganz schön viel Arbeit verschiedene Browser zu unterstützen, geschweige denn auf veraltete Versionen Rücksicht zu nehmen. Wenn die Version so alt ist wie Deine, dann tun wir das auch nicht mehr. Wenn bei dir jetzt auf der Seite hier also alles kaputt ist, dann schau nicht uns an, denn jetzt liegt es an dir! Gib Dir einen Ruck! <b>Aktualisiere</b> Deinen Browser - zum Beispiel hier: <a{up_but}>Aktualisieren</a>.",
				noclose: true,
				style: "top"
			};
		function $buo_f(){
		 var e = document.createElement("script");
		 e.src = "//browser-update.org/update.min.js";
		 document.body.appendChild(e);
		};
		try {document.addEventListener("DOMContentLoaded", $buo_f,false)}
		catch(e){window.attachEvent("onload", $buo_f)}
	</script>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="res/lib/jquery.autocomplete.js"></script>
	<script src="res/lib/bootstrap-combobox/js/bootstrap-combobox.js"></script>
	<script src="res/js/script.js"></script>
	<script src="semantic/dist/components/dropdown.min.js"></script>
	<script src="semantic/dist/components/transition.min.js"></script>
	
	<?php
	function time_elapsed_string($datetime, $full = false) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => 'Jahr',
			'm' => 'Monat',
			'w' => 'Woche',
			'd' => 'Tag',
			'h' => 'Stunde',
			'i' => 'Minute',
			's' => 'Sekunde',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				if($v == 'Jahr' || $v == 'Monat' || $v == 'Tag'){
					$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 'en' : '');
				}
				else {
					$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 'n' : '');
				}
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? 'vor ' . implode(', ', $string) : 'gerade eben';
	}
	?>
	
</head>
