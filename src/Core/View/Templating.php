<?php

namespace Core\View;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Templating
 * @package Core\View
 */
class Templating
{
    /**
     * Path to templates
     * @var string
     */
    protected $templatePath;

    public function __construct($templatePath = "")
    {
        $this->templatePath = $templatePath;
    }

    /**
     * @param ResponseInterface $response
     * @param $template
     * @param array $data
     * @return string
     */
    public function render(ResponseInterface $response, $template, array $data)
    {
        $output = $this->retrieve($template, $data);
        $response->getBody()->write($output);
        return $response;
    }

    /**
     * @param $template
     * @param $data
     * @return string
     */
    private function retrieve($template, $data)
    {
        ob_start();
        $this->extractToTemplate($this->templatePath . $template, $data);
        $output = ob_get_clean();

        return $output;
    }

    /**
     * @param $template
     * @param array $data
     */
    private function extractToTemplate($template, array $data)
    {
        extract($data);
        include $template;
    }
}