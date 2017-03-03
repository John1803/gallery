<?php
namespace Core\Http;

use Core\Http\Headers;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;


class Message implements MessageInterface
{

    /**
     * @var string protocolVersion
     */
    protected $protocolVersion = "1.1";

    /**
     * @var array $headers
     */
    protected $headers = [];

    /**
     * @var StreamInterface
     */
    protected $body;

    /**
     * @return string HTTP protocol version
     */

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version)
    {
        $messageValueObject = clone $this;
        $messageValueObject->protocolVersion = $version;
        return $messageValueObject;
    }

    /**
     * @return array HTTP headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $header case-insensitive header field name
     * @return bool
     */
    public function hasHeader($header)
    {

        return array_key_exists(Headers::normalizedHeaderName($header), $this->getHeaders());
    }

    public function getHeader($headerName)
    {
        $headerName = Headers::normalizedHeaderName($headerName);
        $headers = $this->getHeaders();
        if ($this->hasHeader($headerName)) {
            return is_array($headers[$headerName]) ? $headers[$headerName] : [$headers[$headerName]];
        }

        return [];
    }

    /**
     * @param string $headerName case-insensitive header field name
     * @return string
     */

    public function getHeaderLine($headerName)
    {
        $header = is_array($this->getHeader($headerName)) ? $this->getHeader($headerName) : [$this->getHeader($headerName)];
        if (isset($header)) {
            return implode(",", $header);
        }

        return "";
    }

    /**
     * @param string $headerName
     * @param string|\string[] $headerValue
     * @return Message
     */
    public function withHeader($headerName, $headerValue)
    {
        Headers::isValidHeaderName($headerName);
        $headerValueObject = clone $this;
        if ($this->hasHeader($headerName)) {
            unset($headerValueObject->headers[$headerName]);
        }

        $headerValueObject->headers[$headerName] = $headerValue;

        return  $headerValueObject;
    }

    /**
     * @param string $headerName
     * @param string|\string[] $headerValue
     * @throws \InvalidArgumentException for invalid header name via isValidHeaderName($headerName)
     * @return Message
     */
    public function withAddedHeader($headerName, $headerValue)
    {
        Headers::isValidHeaderName($headerName);
        $headerValueObject = clone $this;
        $headerValueObject->headers[$headerName] = $headerValue;

        return $headerValueObject;
    }

    /**
     * @param string $headerName
     * @return Message
     */
    public function withoutHeader($headerName)
    {
        $headerValueObject = clone $this;
        if ($this->hasHeader($headerName)) {
            unset($headerValueObject->headers[$headerName]);
        }

        return $headerValueObject;
    }

    /**
     * @return StreamInterface
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param StreamInterface $body
     * @return Message
     */
    public function withBody(StreamInterface $body)
    {
        $bodyValueObject = clone $this;
        $bodyValueObject->body = $body;
        return $bodyValueObject;
    }
}