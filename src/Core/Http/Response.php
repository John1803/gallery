<?php

namespace Core\Http;

use Psr\Http\Message\ResponseInterface;

class Response extends Message implements ResponseInterface
{

    /**
     * @var int $statusCode
     */
    protected $statusCode;

    /**
     * @var string reasonPhrase
     */
    protected $reasonPhrase;

    /**
     * @var array $statusCodeReasonPhrase
     */
    protected static $statusCodeReasonPhrase = [
        100 => 'Continue',
        101 => 'Switched Protocols',
        200 => 'Ok',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        417 => 'Expectation Failed',
        426 => 'Upgrade Required',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    ]
    ;

    public function __construct($statusCode = 200, $headers = null, $body = null)
    {
        $this->statusCode = $statusCode ? $statusCode : $this->checkedStatusCode($statusCode);
        $this->headers = $headers ? $headers : new Headers();
        $this->body = $body ? $body : new Stream(fopen('php://temp', 'w+'));
    }
    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return static
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $code = $this->checkedStatusCode($code);

        if (!is_string($reasonPhrase)) {
            throw new \InvalidArgumentException("$reasonPhrase is invalid");
        }

        $responseValueObject = clone $this;
        $responseValueObject->statusCode = $code;

        if (isset(self::$statusCodeReasonPhrase[$code])) {
            $responseValueObject->reasonPhrase = self::$statusCodeReasonPhrase[$code];
        }

        return $responseValueObject;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase()
    {
        if ($this->reasonPhrase) {
            return $this->reasonPhrase;
        }
        if (isset(static::$statusCodeReasonPhrase[$this->statusCode])) {
            return $this->reasonPhrase = static::$statusCodeReasonPhrase[$this->statusCode];
        }
        return '';
    }

    private function checkedStatusCode($statusCode)
    {
        if (!is_int($statusCode) || 100 < $statusCode || $statusCode > 599 ) {
            throw new \InvalidArgumentException("$statusCode is unavailable");
        }

        return $statusCode;
    }

    public function dispatch()
    {
        return $this->getBody()->getContents();
    }

}