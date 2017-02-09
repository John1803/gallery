<?php

namespace Core\Http;

use Psr\Http\Message\RequestInterface;
use Core\Http\Uri;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request extends Message implements RequestInterface
{
    /**
     * @var string HTTP method
     */
    protected $method;

    /**
     * @var string $requestTarget
     */
    protected $requestTarget;

    /**
     * @var object UriInterface
     */
    protected $uri;

    /**
     * Supported HTTP methods
     * @var array $validHttpMethods
     */
    private $validHttpMethods = ['GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTION', 'PATCH'];

    /**
     * Request constructor.
     * @param null $method
     * @param UriInterface $uri
     * @param array $headers
     * @param StreamInterface $body
     */
    public function __construct($method = null, UriInterface $uri, array $headers, StreamInterface $body)
    {
        $this->method = $method;
        $this->headers = $headers;
        $this->uri = $uri;
        $this->body = $body;
    }

    /**
     * @param null $method
     * @return static
     */
    public static function buildFromGlobals($method = null)
    {

        if (isset($_SERVER['REQUEST_METHOD']) || $method === null) {
            $method = $_SERVER['REQUEST_METHOD'];
        }
        $uri = Uri::retrieveUriFromServer();
        $headers = Headers::retrieveHeadersFromServer();
        $body = Stream::retrieveStream();

        return new static($method, $uri, $headers, $body);
    }

    /**
     * Retrieves the message's request target.
     *
     * @return string
     */
    public function getRequestTarget()
    {
        $this->requestTarget = $this->uri->getPath();

        if ($this->uri->getQuery()) {
            $this->requestTarget .= "?" . $this->uri->getQuery();
        }

        if(empty($this->requestTarget)) {
            return "/";
        }

        return $this->requestTarget;
    }

    /**
     * Return an instance with the specific request-target.
     *
     * @param mixed $requestTarget
     * @return static
     */
    public function withRequestTarget($requestTarget)
    {
        $request = new $this;
        $request->requstTarget = $requestTarget;

        return $request;
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     * @return static
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        if (!is_string($method)) {
            throw new \InvalidArgumentException("Invalid HTTP method; must be string");
        }

        if (!in_array(strtoupper($method), $this->validHttpMethods, $method)) {
            throw  new \InvalidArgumentException("Unsupported HTTP method $method provided");
        }

        $request = clone $this;
        $request->method = $method;

        return $request;
    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $request = clone $this;
        $request->uri = $uri;

        if ($preserveHost === true) {
            if (empty($request->getHeader("HOST")) && $uri->getHost()) {
                $request->headers['HOST'] = $uri->getHost();
            } elseif ((empty($request->getHeader("HOST") && !$uri->getHost()) || $request->hasHeader("HOST"))) {
                return $request;
            }
        }

        return $request;
    }
}