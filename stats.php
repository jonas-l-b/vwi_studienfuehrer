<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div id="load">
	<br><br><div class="loader"><div></div></div><br><br>
	<p style="text-align:center">Die Bewertungsstatistiken werden geladen.</p>
</div>

<div id="main" class="container">
</div>

<script>
$( document ).ready(function() {

	$.ajax({
		type: "POST",
		url: "load_stats.php",
		success: function(data) {
			$("#main").html(data);
		}
	});
	
	$(document).ajaxStop(function () {
		$("#load").hide();
	});
	
});
</script>

</body>