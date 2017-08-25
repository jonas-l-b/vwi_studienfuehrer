<?php

include('connect.php');

if(isset($_GET['getDeleteModal'])){
	echo $twig->render('deleteModal.template.html', 
							array());
}