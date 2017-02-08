<?php
require "../vendor/autoload.php";

use Core\Http\ServerRequest;

$serverRequest = ServerRequest::buildFromGlobals();
$parsedBody = $serverRequest->getParsedBody();
print_r($parsedBody);

