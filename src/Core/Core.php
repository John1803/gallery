<?php

namespace Core;

use Core\Routing\UrlMatcher;
use Core\Controller\ControllerActionDeterminer;
use Core\Http\ServerRequest;

class Core
{
    /**
     * @var UrlMatcher
     */
    protected $urlMatcher;

    /**
     * @var ControllerActionDeterminer
     */
    protected $controllerActionDeterminer;

    public function __construct(UrlMatcher $urlMatcher, ControllerActionDeterminer $controllerActionDeterminer)
    {
        $this->urlMatcher = $urlMatcher;
        $this->controllerActionDeterminer = $controllerActionDeterminer;
    }

    public function handle(ServerRequest $serverRequest)
    {
        $serverRequestPath = $serverRequest->getUri()->getPath();
        $serverRequestWithAttributes = $serverRequest->withAttributes($this->urlMatcher->match($serverRequestPath));
        $controllerAction = $this->controllerActionDeterminer->getControllerAndAction($serverRequestWithAttributes);
        $actionArguments = $this->controllerActionDeterminer->getActionArguments($controllerAction, $serverRequestWithAttributes);
        $response = call_user_func_array($controllerAction, $actionArguments);
        return $response;
    }
}