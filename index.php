<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

$loader = require 'vendor/autoload.php';
$loader->add('Root', __DIR__.'/system/');
$loader->add('Controller', __DIR__.'/application/');
$loader->add('Model', __DIR__.'/application/');

use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;
use Root\OuathController;

$ouath = new OuathController();
$collector = new RouteCollector();

$collector->post('/easyapi/token', function() use($ouath){
    return $ouath->token();
});

$directory = __DIR__.'/application/Controller/';
$scanned_directory = array_diff(scandir($directory), array('..', '.'));

 foreach($scanned_directory as $filename){
     $resource = explode(".", $filename);
     $resource = $resource[0];
     $collector->controller('/easyapi/'.strtolower($resource), 'Controller\\'.ucfirst($resource));
 }

$dispatcher = new Dispatcher($collector->getData());

$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
echo $response;