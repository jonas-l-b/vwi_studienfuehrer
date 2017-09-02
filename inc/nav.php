<?php

//Admin im Menü
$admin = "";
if($userRow['admin']!=0){
	$admin = "
		<li>
			<a data-toggle=\"dropdown\" style=\"cursor: pointer; cursor: hand;\">Admin</a>
			<ul class=\"dropdown-menu\">
				<li><a id=\"linkToAdminEdit\" href=\"admin.php\">Daten bearbeiten</a></li>
				<li><a id=\"linkToAdminMessages\" href=\"admin.php#messages\">Nachrichten</a></li>
				<li><a id=\"linkToAdminNotifications\" href=\"admin.php#notifications\">Benachrichtigungen</a></li>
			</ul>
		</li>
	";
}

//Name
$name = $userRow['first_name'];

?>

<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container">
		    <div class="navbar-header">
			  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>                        
			  </button>
			</div>
			
			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav">
					<li><a href="tree.php">Übersicht Studienführer</a></li>
					<li><a href="https://www.vwi-karlsruhe.de" target="_blank">vwi-karlsruhe.de</a></li>
					<li><a id="contact" style="cursor: pointer; cursor: hand;">Kontakt</a></li>
					<?php echo $admin;?>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li>
						<a data-toggle="dropdown" style="cursor: pointer; cursor: hand;"><span class="glyphicon glyphicon-user"></span> Hallo <?php echo $name;?>!</a>
						<ul class="dropdown-menu">
							<li><a id="linkToUserProfile" href="userProfile.php">Profil</a></li>
							<li><a id="linkToUserFavorites" href="userProfile.php#favourites">Favoriten</a></li>
							<li class="divider"></li>
							<li><a href="logout.php">Logout</a></li>
						</ul>
					</li>		
				</ul>				
				<div id="searchfield">
					<form action="tree.php" method="get" class="navbar-form">
					  <div style="display:table;" class="input-group">
						<input type="text"  class="form-control suchen-autocomplete" name="suchfeld" placeholder="Suchen">
						<!--<span style="width: 1%;" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>-->
						<div style="width:1%" class="input-group-btn">
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
<div id="contactModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
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
