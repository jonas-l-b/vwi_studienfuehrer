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
*/
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
		</div>
	</nav>
";
?>
