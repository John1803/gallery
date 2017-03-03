<?php

namespace Core\Controller;

use Core\Http\Response;
use Core\View\Templating;

abstract class AbstractController
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Templating
     */
    protected $templating;

    public function getResponse()
    {
        if (!isset($this->response)) {
            return $this->response = new Response();
        }

        return $this->response;
    }

    public function getTemplating()
    {
        if (!isset($this->templating)) {
            return $this->templating = new Templating("../src/Gallery/templates/");
        }

        return $this->templating;
    }
}