<?php

//Debug flag. Should be FALSE on production deployments
define('DEBUG',TRUE);

mb_internal_encoding("UTF-8");
###### Application Settings ######
define("DOMAIN",$_SERVER['HTTP_HOST']); //Domain name where the app is running
define("DIRECTORY",'badger/'); //Directory where the app is installed
define("USE_SSL",FALSE); //Whether or not to use SSL
(USE_SSL) ? define("SSL","s") : define("SSL",'');
define('APP_URL',"http".SSL."://".DOMAIN."/".DIRECTORY);
define('APP_NAME','BadgeR'); //Name of the application

//Date format
define('DATE_FORMAT','Y-m-d H:i:s'); //Date format

###### Database settings for the stats database ######
define('DB_METHOD', 'mysql');//Probably won't need to change
define('DB_NAME', 'badger');
define('DB_USER', 'root');
define('DB_PASS', '123');
define('DB_HOST', 'localhost');//Probably won't need to change
define('DB_PORT', 3306);
define('TBL_PREFIX','bg_');

###### DO NOT EDIT BELOW THIS LINE ######
require_once('inc/autoload.php');
require_once('inc/vendor/autoload.php');

require_once('inc/functions.php');

define("ROOTPATH", __DIR__);
PHP_Timer::start();
session_start();