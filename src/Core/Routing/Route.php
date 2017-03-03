<?php

namespace Core\Routing;

use Core\Helpers\Transformer;

class Route
{
    /**
     * @var string
     */
    protected $pathPattern;

    /**
     * @var string
     */
    protected $regexPattern;

    /**
     * @var array
     */
    protected $settings;

    /**
     * Route constructor.
     * @param $pathPattern
     * @param regexPattern
     * @param array $settings
     */
    public function __construct($pathPattern, $regexPattern, array $settings)
    {
        $this->pathPattern = $pathPattern;
        $this->regexPattern = $regexPattern;
        $this->settings = $settings;
    }

    /**
     * @return string
     */
    public function getPathPattern()
    {
        return $this->pathPattern;
    }

    /**
     * @param string $pathPattern
     */
    public function setPathPattern($pathPattern)
    {
        $this->pathPattern = $pathPattern;
    }

    /**
     * @return string
     */
    public function getRegexPattern()
    {
        return $this->regexPattern;
    }

    /**
     * @param string $regexPattern
     */
    public function setRegexPattern($regexPattern)
    {
        $this->regexPattern = $regexPattern;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    public function composedToArray()
    {
        $result = get_object_vars($this);
        $result = Transformer::multidimensionalToSingleArray($result);
        return $result;
    }
}