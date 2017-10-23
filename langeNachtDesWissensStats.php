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

<script>
	$(document).ready(function(){
		setInterval(function(){
			$.get( "langeNachtDesWissensStatsAPI.php",
					 function( data ) {
			  data = JSON.parse(data);
			  $( "#modVal" ).text( data.modVal );
			  $( "#dozVal" ).text( data.dozVal );
			  $( "#verVal" ).text( data.verVal );
			  $( "#insVal" ).text( data.insVal );
			});
		}, 10000);
	});
</script>

</body>
</html>