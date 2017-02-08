<?php
require "../vendor/autoload.php";

use Core\Http\ServerRequest;

$serverRequest = ServerRequest::buildFromGlobals();
$body = $serverRequest->getBody()->getContents();
$parsedBody = $serverRequest->getParsedBody();
print_r($parsedBody);
print_r($body);

