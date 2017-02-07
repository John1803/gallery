<?php
namespace Core\Http;

class Headers
{
    protected $headers = [];


    protected static $contentHeaders = [
        'CONTENT_TYPE',
        'CONTENT_LENGTH',
    ];

    public function __construct($headers = null)
    {
        $this->headers = $headers;
    }

    public static function retrieveHeadersFromServer(array $headers = [])
    {
        $server = $_SERVER;

        if ($headers === null) {
            $headers = [];
        }
        foreach ($server as $headerName => $headerValue) {
            if (strpos($headerName, "HTTP_") === 0) {
                $headers[self::normalizedHeaderName(substr($headerName, 5))] = $headerValue;
            }

            if(in_array($headerName, self::$contentHeaders)) {
                $headers[self::normalizedHeaderName($headerName)] = $headerValue;
            }
        }

        return new static($headers);
    }

    /**
     * @param string $headerName
     */
    public static function isValidHeaderName($headerName)
    {
        if (!preg_match("/^[a-zA-Z0-9!#$%&'*+._`|~-]+/", $headerName)) {
            throw new \InvalidArgumentException("$headerName is not valid");
        }
    }

    /**
     * @param string $headerName
     * @return string
     */
    public static function normalizedHeaderName($headerName)
    {
        return strtr(strtolower($headerName), "_", "-");
    }
}