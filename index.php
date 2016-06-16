<?php
define('SYSPATH', 'system/');
define('APPPATH', 'app/');
define('VIEWS', 'views/');

// Załączenie autoloadera
include SYSPATH.'autoload.php';

// Ustawienie strefy czasowej
date_default_timezone_set('Europe/Warsaw');

// Wyświetlanie błędów
error_reporting(E_ALL);

$app = new \System\Application;
$app->init('index', 'index');
$app->run();
?>