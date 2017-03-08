<?php

namespace Core\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * @var string $attributes
     */
    protected $attributes = [];

    /**
     * @var array $serverParams
     */
    protected $serverParams;

    /**
     * @var array $cookieParams
     */
    protected $cookieParams;

    /**
     * @var array $queryParams
     */
    protected $queryParams;

    /**
     * @var array $uploadedFiles
     */
    protected $uploadedFiles;

    /**
     * @var
     */
    protected $parsedBody;

    /**
     * ServerRequest constructor.
     * @param null $method
     * @param UriInterface $uri
     * @param Headers $headers
     * @param StreamInterface $body
     * @param array $attributes
     * @param null $serverParams
     * @param null $cookieParams
     * @param null $queryParams
     * @param array $uploadedFiles
     */
    public function __construct(
        $method,
        UriInterface $uri,
        array $headers,
        StreamInterface $body,
        array $attributes,
        $serverParams = null,
        $cookieParams = null,
        $queryParams = null,
        array $uploadedFiles)
    {
        parent::__construct($method, $uri, $headers, $body);
        $this->attributes = $attributes;
        $this->serverParams = $serverParams;
        $this->cookieParams = $cookieParams;
        if ($this->getUri()->getQuery()) {
            parse_str($this->getUri()->getQuery(), $this->queryParams);
        }
        $this->uploadedFiles = $uploadedFiles;
    }

    /**
     * @param null $method
     * @param null $uri
     * @param null $headers
     * @param null $body
     * @param null $attributes
     * @param null $serverParams
     * @param null $cookieParams
     * @param null $queryParams
     * @param null $uploadedFiles
     * @return static
     */
    public static function buildFromGlobals(
        $method = null,
        $uri = null,
        $headers = null,
        $body = null,
        $attributes = null,
        $serverParams = null,
        $cookieParams = null,
        $queryParams = null,
        $uploadedFiles = null)
    {

        $method = $_SERVER['REQUEST_METHOD'];

        if ($serverParams === null) {
            $serverParams = $_SERVER;
        }

        if ($cookieParams === null) {
            $cookieParams = $_COOKIE ? $_COOKIE : [];
        }

        $uri = Uri::retrieveUriFromServer();
        $headers = Headers::retrieveHeadersFromServer();
        $body = $body === null ? Stream::retrieveStream("php://input") : Stream::retrieveStream("php://input");
        $attributes = [];
        $uploadedFiles = UploadedFile::retrieveUploadedFiles();

        $serverRequest = new static($method, $uri, $headers, $body, $attributes, $serverParams, $cookieParams, $queryParams, $uploadedFiles);

        if ($method === 'POST') {
            $serverRequest = $serverRequest->withParsedBody($_POST);
        }

        return $serverRequest;
    }

    /**
     * Retrieve server parameters.
     *
     * Retrieves data related to the incoming request environment,
     * typically derived from PHP's $_SERVER superglobal. The data IS NOT
     * REQUIRED to originate from $_SERVER.
     *
     * @return array
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * Retrieve cookies.
     *
     * Retrieves cookies sent by the client to the server.
     *
     * The data MUST be compatible with the structure of the $_COOKIE
     * superglobal.
     *
     * @return array
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
     * Return an instance with the specified cookies.
     *
     * The data IS NOT REQUIRED to come from the $_COOKIE superglobal, but MUST
     * be compatible with the structure of $_COOKIE. Typically, this data will
     * be injected at instantiation.
     *
     * This method MUST NOT update the related Cookie header of the request
     * instance, nor related values in the server params.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated cookie values.
     *
     * @param array $cookies Array of key/value pairs representing cookies.
     * @return static
     */
    public function withCookieParams(array $cookies)
    {
        // TODO: Implement withCookieParams() method.
    }

    /**
     * Retrieve query string arguments.
     *
     * Retrieves the deserialized query string arguments, if any.
     *
     * Note: the query params might not be in sync with the URI or server
     * params. If you need to ensure you are only getting the original
     * values, you may need to parse the query string from `getUri()->getQuery()`
     * or from the `QUERY_STRING` server param.
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * Return an instance with the specified query string arguments.
     *
     * These values SHOULD remain immutable over the course of the incoming
     * request. They MAY be injected during instantiation, such as from PHP's
     * $_GET superglobal, or MAY be derived from some other value such as the
     * URI. In cases where the arguments are parsed from the URI, the data
     * MUST be compatible with what PHP's parse_str() would return for
     * purposes of how duplicate query parameters are handled, and how nested
     * sets are handled.
     *
     * Setting query string arguments MUST NOT change the URI stored by the
     * request, nor the values in the server params.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated query string arguments.
     *
     * @param array $query Array of query string arguments, typically from
     *     $_GET.
     * @return static
     */
    public function withQueryParams(array $query)
    {
        // TODO: Implement withQueryParams() method.
    }

    /**
     * Retrieve normalized file upload data.
     *
     * This method returns upload metadata in a normalized tree, with each leaf
     * an instance of Psr\Http\Message\UploadedFileInterface.
     *
     * These values MAY be prepared from $_FILES or the message body during
     * instantiation, or MAY be injected via withUploadedFiles().
     *
     * @return array An array tree of UploadedFileInterface instances; an empty
     *     array MUST be returned if no data is present.
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * Create a new instance with the specified uploaded files.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param array $uploadedFiles An array tree of UploadedFileInterface instances.
     * @return static
     * @throws \InvalidArgumentException if an invalid structure is provided.
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        // TODO: Implement withUploadedFiles() method.
    }

    /**
     * Retrieve any parameters provided in the request body.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method MUST
     * return the contents of $_POST.
     *
     * Otherwise, this method may return any results of deserializing
     * the request body content; as parsing returns structured content, the
     * potential types MUST be arrays or objects only. A null value indicates
     * the absence of body content.
     *
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * Return an instance with the specified body parameters.
     *
     * These MAY be injected during instantiation.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, use this method
     * ONLY to inject the contents of $_POST.
     *
     * The data IS NOT REQUIRED to come from $_POST, but MUST be the results of
     * deserializing the request body content. Deserialization/parsing returns
     * structured data, and, as such, this method ONLY accepts arrays or objects,
     * or a null value if nothing was available to parse.
     *
     * As an example, if content negotiation determines that the request data
     * is a JSON payload, this method could be used to create a request
     * instance with the deserialized parameters.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param null|array|object $data The deserialized body data. This will
     *     typically be in an array or object.
     * @return static
     * @throws \InvalidArgumentException if an unsupported argument type is
     *     provided.
     */
    public function withParsedBody($data)
    {
        switch ($this->getContentType()) {
            case 'application/json':
                $data = json_decode($this->getBody(), true);
                break;
            case 'application/xml':
            case 'text/xml':
                $backup = libxml_disable_entity_loader(true);
                $data = simplexml_load_string($this->getBody());
                libxml_disable_entity_loader($backup);
                break;
            case 'application/x-www-form-urlencoded':
                parse_str($this->getBody()->getContents(), $data);
                break;
            case 'multipart/form-data':
                $this->parsedBody = $data;
                break;
            default:
                $data = null;
                break;
        }

        if ($this->parsedBody === null) {
            $this->parsedBody = $data;
            return $this;
        }

        $responseValueObject = clone $this;
        $responseValueObject->parsedBody = $data;

        return $responseValueObject;
    }

    /**
     * Retrieve attributes derived from the request.
     *
     * The request "attributes" may be used to allow injection of any
     * parameters derived from the request: e.g., the results of path
     * match operations; the results of decrypting cookies; the results of
     * deserializing non-form-encoded message bodies; etc. Attributes
     * will be application and request specific, and CAN be mutable.
     *
     * @return array Attributes derived from the request.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Retrieve a single derived request attribute.
     *
     * Retrieves a single derived request attribute as described in
     * getAttributes(). If the attribute has not been previously set, returns
     * the default value as provided.
     *
     * This method obviates the need for a hasAttribute() method, as it allows
     * specifying a default value to return if the attribute is not found.
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @param mixed $default Default value to return if the attribute does not exist.
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }

    /**
     * Return an instance with the specified derived request attribute.
     *
     * This method allows setting a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated attribute.
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @param mixed $value The value of the attribute.
     * @return static
     */
    public function withAttribute($name, $value)
    {
        $serverRequestValueObject = clone $this;
        $serverRequestValueObject->attributes[$name] = $value;
        return $serverRequestValueObject;
    }

    /**
     * Return an instance that removes the specified derived request attribute.
     *
     * This method allows removing a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the attribute.
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @return static
     */
    public function withoutAttribute($name)
    {
        // TODO: Implement withoutAttribute() method.
    }

    /**
     * Return an instance with the specified derived request attribute
     * Non-PSR7 method
     *
     * @param $attributes
     * @return ServerRequest
     */
    public function withAttributes($attributes)
    {
        $serverRequestValueObject = clone $this;
        foreach ($attributes as $name => $value) {
            $serverRequestValueObject->attributes[$name] = $value;
        }

        return $serverRequestValueObject;
    }

    /**
     * @return array|null
     */

    private function getContentType()
    {
        $contentType = $this->getHeader("Content-Type")[0] ? $this->getHeader("Content-Type")[0] : null;
        if (strpos($contentType, ";")) {
            $contentType = explode(";", $contentType);
            return $contentType[0];
        }

        return $contentType;
    }
}