<?php

namespace Core\Routing;

class RoutesSet extends \ArrayObject
{
    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @param $identifier
     * @param Route $route
     */
    public function add($identifier, Route $route)
    {
        $this->routes[$identifier] = $route;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }
    /**
     * TO DO if need
     *
     */
    public function all() {}
    public function get($identifier) {}
    public function has($identifier) {}
    public function remove($identifier) {}
}