<?php

namespace Gallery\Controller;

use Core\View\Templating;
use Core\Http\Response;

class HelloController
{
    public function indexAction($name)
    {
        $response = new Response();
        $templating = new Templating('../src/Gallery/templates/');
        return $templating->render($response, "template.phtml", ['name' => $name]);
    }
}
