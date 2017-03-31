<?php require_once("config.php");?>

<?php

$user = new user(TRUE);
var_dump($user);

$user = new user(TRUE, 2);
var_dump($user);