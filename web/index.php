<?php

require "../vendor/autoload.php";
require "../app/routesSet.php";

use Core\Http\ServerRequest;
use Core\Routing\UrlMatcher;
use Core\Controller\ControllerActionDeterminer;
use Core\Core;

$serverRequest = ServerRequest::buildFromGlobals();
$urlMatcher = new UrlMatcher($routesSet, $serverRequest);
$controllerActionDeterminer = new ControllerActionDeterminer();
$core = new Core($urlMatcher, $controllerActionDeterminer);
/**
 * @var \Core\Http\Response $response
 */
$response = $core->handle($serverRequest);
$response->dispatch();

