<?php

//Admin im Menü
$admin = "";
if($userRow['admin']!=0){

	if(mysqli_num_rows(mysqli_query($con, "SELECT * FROM messages WHERE processed = 0")) > 0){ //Für Briefumschlag bei "Posteingang"
		$envelope = "<span class=\"glyphicon glyphicon-envelope\"></span>&nbsp;";
		$space = "  "; //Lausige Lösung, wusste aber grad nicht, wie sonst
	}else{
		$envelope = "";
		$space = "";
	}

	$admin = "
		<li>
			<a data-toggle=\"dropdown\" style=\"cursor: pointer; cursor: hand;\">Admin <span class=\"caret\"></span></a>
			<ul class=\"dropdown-menu\">
				<li><a id=\"linkToAdminEdit\" href=\"admin.php\">Daten bearbeiten</a></li>
				<li><a id=\"linkToAdminMessages\" href=\"admin.php#messages\">Posteingang".$space.$envelope."</a></li>
				<li><a id=\"linkToAdminNotifications\" href=\"admin.php#notifications\">Benachrichtigungen</a></li>
				<li><a id=\"linkToAdminList\" href=\"admin.php#adminList\">Admin-Liste</a></li>
				<li><a id=\"linkToUserProfiles\" href=\"admin.php#userProfiles\">Nutzerprofile</a></li>
			</ul>
		</li>
	";
}

//Name
$name = $userRow['first_name'];

?>
<nav class="navbar navbar-default navbar-fixed-top menu">
	<div class="container">
		    <div class="navbar-header">
			  <a id="menulogohandy" class="navbar-brand" href="tree.php"><img src="pictures/nav1.png" alt="VWI-ESTIEM Hochschulgruppe Karlsruhe e.V.">
				</a>
			  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			  </button>
			</div>
			<a id="menulogo" class="navbar-brand" href="tree.php"><img src="pictures/nav1.png" alt="VWI-ESTIEM Hochschulgruppe Karlsruhe e.V.">
				</a>
			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav">
					<li style="background-color: #F0F8FF"><a target="_blank" href="http://www.vwi-karlsruhe.de">vwi-karlsruhe.de</a></li>
					<li><a id="contact" style="cursor: pointer; cursor: hand;">Kontakt</a></li>
					<?php echo $admin;?>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li>
						<a data-toggle="dropdown" style="cursor: pointer; cursor: hand;"><span class="glyphicon glyphicon-user"></span> Hallo <?php echo $name;?>! <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a id="linkToUserProfile" href="userProfile.php">Profil <span class="pull-right"><span class="glyphicon glyphicon-list-alt"></span></span></a></li>
							<li><a id="linkToUserFavorites" href="userProfile.php#favourites">Favoriten <span class="pull-right"><span class="glyphicon glyphicon-star"></span></span></a></li>
							<li class="divider"></li>
							<li><a href="logout.php">Logout <span class="pull-right"><span class="glyphicon glyphicon-log-out"></span></span></a></li>
						</ul>
					</li>
				</ul>
				<div id="searchfield" style="padding:15px;">
					<form action="tree.php" method="get" class="navbar-form">
					  <div style="display:table;" class="input-group">
						<input type="text" class="form-control suchen-autocomplete" name="suchfeld" placeholder="Suchen">
						<!--<span style="width: 1%;" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>-->
						<div class="input-group-btn" style="width:1%">
						  <button class="btn btn-default">
							<i class="glyphicon glyphicon-search"></i>
						  </button>
						</div>
					  </div>
					</form>
				</div>
			</div>
		</div>
</nav>
<div class="aftermenu"></div>
<div id="contactModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true" onClick="window.location.reload()">&times;</button>
		<h2 class="modal-title">Kontakt</h2>
	</div>
		<div class="modal-body">
			<div id="contactModalBody"></div>
		</div><!-- End of Modal body -->
	</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->

<script>
$(function(){
  $('.suchen-autocomplete').autocomplete({
    serviceUrl: 'search-autocomplete-api.php',
	autoSelectFirst: true,
	groupBy: 'category',
	paramName: 'query',
	type: "GET",
	maxHeight: 400,
	containerclass: "suchcontainer",
    onSelect: function (suggestion) {
		if(suggestion.data.dest != "")
			window.location.href = suggestion.data.dest;
		else
			$('.suchen-autocomplete').val("Übersicht Startseite");
    }
  });

});
</script>

<script>
$(document).ready(function(){
	$("#contact").click(function(){
		$('#contactModal').modal('show');
		$('#contactModalBody').html('<br /><br /><div class="loader"><div></div></div><br /><br />');
		$('#contactModalBody').load("contactModal.php", function( response, status, xhr ) {
		  if ( status == "error" ) {
			$('#contactModalBody').html('<strong>Daten können nicht geladen werden.</strong>');
		  }
		});
	});
});
</script>
