<?php

namespace Core\Routing;

use Core\Routing\RoutesSet;
use Psr\Http\Message\ServerRequestInterface;

class UrlMatcher
{
    /**
     * $var RoutesSet
     */
    protected $routesSet;

    /**
     * @var ServerRequestInterface
     */

    protected $request;
    /**
     * @param RoutesSet $routesSet
     * @param ServerRequestInterface $request
     */
    public function __construct(RoutesSet $routesSet, ServerRequestInterface $request)
    {
        $this->routesSet = $routesSet;
        $this->request = $request;
    }

    /**
     * @param $path
     * @return array
     * @throws \Exception
     */
    public function match($path)
    {
        if ($matchedInformation = $this->setsInRoutesSet($path, $this->routesSet)) {
            return $matchedInformation;
        }

        throw new \Exception("$path is not match to any route in routes set");
    }

    /**
     * @param $path
     * @param \Core\Routing\RoutesSet $routesSet
     * @return array
     */
    private function setsInRoutesSet($path, RoutesSet $routesSet)
    {
        foreach ($routesSet as $identifier => $route) {
            $composedRoute = $route->composedToArray();

            if (!preg_match($composedRoute['regexPattern'], $path, $matches)) {
                continue;
            }

            return (array_merge($composedRoute, $this->filteredMatches($matches)));
        }
    }

    /**
     * @param array $matches
     * @return array
     */
    private function filteredMatches(array $matches)
    {
        $result = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}