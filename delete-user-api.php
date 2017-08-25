<?php

include('connect.php');

if(isset($_GET['getDeleteModal'])){
	echo $twig->render('deleteModal.template.html', 
							array());
}else if (isset($_GET['deleteUser'])){
	if(isset($_POST['yyy'])&&isset($_POST['yyy'])&&isset($_POST['yyy'])&&isset($_POST['yyy'])){
		echo "ok";
	}else{
		echo "{form : 'error'}";
	}
}else exit();
?>