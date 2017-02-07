<?php

namespace Core\Controller;

use Psr\Http\Message\ServerRequestInterface;

class ControllerActionDeterminer
{
    public function getControllerAndAction(ServerRequestInterface $request)
    {
        if (!$controllerAction = $request->getAttribute("controllerAction")) {
            throw new \Exception("$controllerAction is not define");
        }

        list($class, $action) = explode(":", $controllerAction);

        if (!class_exists($class)) {
            throw new \Exception("$class is not exist");
        }

        return [new $class(), $action];
    }
}