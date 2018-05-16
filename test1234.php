<?php

class User{
  public $name = 'Nutzer';
  public $admin = false;
}

$testuser = new User();
$testuser->name = "Roberto";
$testuser->admin = true;

require_once 'vendor/autoload.php';

$loader = new Twig_Loader_Filesystem('templates/');
$twig = new Twig_Environment($loader, array(
    'cache' => false
));

$template = $twig->load('veranstaltungsseite.template.html');
echo $template->render(array('name' => 'AFIB123', 'user' => $testuser));

?>
