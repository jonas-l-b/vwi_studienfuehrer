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
					<?php echo $admin;?>
				</ul>
				
				<div id="searchfield">
					<form class="navbar-form navbar-left">
					  <div class="input-group">
						<input type="text" class="form-control suchen-autocomplete" name="suchfeld" placeholder="Suchen">
						<div class="input-group-btn">
						  <button class="btn btn-default" type="submit">
							<i class="glyphicon glyphicon-search"></i>
						  </button>
						</div>
					  </div>
					</form>
				</div>
				
				<ul class="nav navbar-nav navbar-right">
					<li>
						<a data-toggle="dropdown" style="cursor: pointer; cursor: hand;"><span class="glyphicon glyphicon-user"></span> Hallo <?php echo $name;?>!</a>
						<ul class="dropdown-menu">
							<li><a href="userProfile.php">Profil</a></li>
							<li><a href="logout.php">Logout</a></li>
						</ul>
					</li>		
				</ul>
			</div>
		</div>
</nav>
<script>
$(function(){
  var currencies = [
    { value: 'Afghan afghani', data: 'AFN' },
    { value: 'Albanian lek', data: 'ALL' },
    { value: 'Algerian dinar', data: 'DZD' },
    { value: 'European euro', data: 'EUR' },
    { value: 'Angolan kwanza', data: 'AOA' },
    { value: 'East Caribbean dollar', data: 'XCD' },
    { value: 'Vietnamese dong', data: 'VND' },
    { value: 'Yemeni rial', data: 'YER' },
    { value: 'Zambian kwacha', data: 'ZMK' },
    { value: 'Zimbabwean dollar', data: 'ZWD' },
  ];
  $('.suchen-autocomplete').autocomplete({
    lookup: currencies,
	containerclass: "suchcontainer",
    onSelect: function (suggestion) {
    // some function here
    }
  });
});
</script>
