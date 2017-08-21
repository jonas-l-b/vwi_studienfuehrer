<?php

//Admin im Menü
$admin = "";
if($userRow['admin']!=0){
	$admin = "<li><a href=\"admin.php\">Admin</a></li>";
}

//Name
$name = $userRow['first_name'];
/*	
echo "
	<nav class=\"navbar navbar-default navbar-fixed-top\">
		<div class=\"container\">

			<ul class=\"nav navbar-nav\">
				<li><a href=\"tree.php\">Übersicht Studienführer</a></li>
				<li><a href=\"http://www.vwi-karlsruhe.de\">vwi-karlsruhe.de</a></li>
				".$admin."
			</ul>
			
			<ul class=\"nav navbar-nav navbar-right\">
				<li>
					<a data-toggle=\"dropdown\" style=\"cursor: pointer; cursor: hand;\"><span class=\"glyphicon glyphicon-user\"></span> Hallo ".$name."!</a>
					<ul class=\"dropdown-menu\">
						<li><a href=\"userProfile.php\">Profil</a></li>
						<li><a href=\"logout.php\">Logout</a></li>
					</ul>
				</li>		
			</ul>
			
		</div>
	</nav>
";

echo "
	<nav class=\"navbar navbar-default navbar-fixed-top\">
		<div class=\"container\">
		    <div class=\"navbar-header\">
			  <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\"#myNavbar\">
				<span class=\"icon-bar\"></span>
				<span class=\"icon-bar\"></span>
				<span class=\"icon-bar\"></span>                        
			  </button>
			</div>
			
			<div class=\"collapse navbar-collapse\" id=\"myNavbar\">
				<ul class=\"nav navbar-nav\">
					<li><a href=\"tree.php\">Übersicht Studienführer</a></li>
					<li><a href=\"http://www.vwi-karlsruhe.de\">vwi-karlsruhe.de</a></li>
					$admin
				</ul>
				
				<ul class=\"nav navbar-nav navbar-right\">
					<li>
						<a data-toggle=\"dropdown\" style=\"cursor: pointer; cursor: hand;\"><span class=\"glyphicon glyphicon-user\"></span> Hallo $name!</a>
						<ul class=\"dropdown-menu\">
							<li><a href=\"userProfile.php\">Profil</a></li>
							<li><a href=\"logout.php\">Logout</a></li>
						</ul>
					</li>		
				</ul>
			</div>
		</div>
	</nav>
";
*/
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
					<li id="dummy"></li>
					<?php echo $admin;?>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li>
						<a data-toggle="dropdown" style="cursor: pointer; cursor: hand;"><span class="glyphicon glyphicon-user"></span> Hallo <?php echo $name;?>!</a>
						<ul class="dropdown-menu">
							<li><a href="userProfile.php">Profil</a></li>
							<li><a href="userProfile.php#favourites">Favoriten</a></li>
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
		$('#dummy').load("contactModal.php",function(result){
			$('#contactModal').modal({show:true});
		});
	});
});
</script>
