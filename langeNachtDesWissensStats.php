<?php

include('connect.php');

include('header.php');

?>
<body>
<div class="container">
	<br />
	<br />
	<h1 style="text-align:center">Lange Nacht des Wissens</h1>
	
	<br ><br ><br ><br >
	
<div class="row">
<div class="col-md-3"></div>
<div class="col-md-6">
	<div class="ui statistics" style="text-align:center">
  <div class="statistic">
    <div class="value" id="dozVal">
      0
    </div>
    <div class="label">
      Dozenten
    </div>
  </div>
  <div class="statistic">
    <div class="value" id="verVal">
      0
    </div>
    <div class="label">
      Veranstaltungen
    </div>
  </div>
  <div class="statistic">
    <div class="value" id="modVal">
      0
    </div>
    <div class="label">
      Module
    </div>
  </div>
  <div class="statistic">
    <div class="value" id="insVal">
      0
    </div>
    <div class="label">
      Institute
    </div>
  </div>
</div>
</div>
<div class="col-md-3"></div>
	</div>
	
</div>
<script src="res/lib/countUp.min.js"></script>
<script>
	$(document).ready(function(){
		setInterval(function(){
			$.get( "langeNachtDesWissensStatsAPI.php",
					 function( data ) {
			  data = JSON.parse(data);
				var numAnim = new CountUp("dozVal", $( "#dozVal" ).text() , data.dozVal, 0,data.dozVal-$( "#dozVal" ).text());
				if (!numAnim.error) {
					numAnim.start();
				} else {
					console.error(numAnim.error);
				}
				var numAnim = new CountUp("modVal", $( "#modVal" ).text() , data.modVal, 0,data.modVal-$( "#modVal" ).text());
				if (!numAnim.error) {
					numAnim.start();
				} else {
					console.error(numAnim.error);
				}
				var numAnim = new CountUp("verVal", $( "#verVal" ).text() , data.verVal, 0, data.verVal-$( "#verVal" ).text());
				if (!numAnim.error) {
					numAnim.start();
				} else {
					console.error(numAnim.error);
				}
				var numAnim = new CountUp("insVal", $( "#insVal" ).text() , data.insVal, 0, data.insVal-$( "#insVal" ).text());
				if (!numAnim.error) {
					numAnim.start();
				} else {
					console.error(numAnim.error);
				}
			});
		}, 30000);
	});
</script>

</body>
</html>