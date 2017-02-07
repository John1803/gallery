<?php

namespace Core\Http;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{

    /**
     * Unreserved character using in Uri path, query and fragments
     * @see https://tools.ietf.org/html/rfc3986#appendix-A
     * @const string
     */
    const UNRESERVED_CHARACTERS = "\\w\\-\\.~";


    /**
     * Unreserved character using in Uri path, query and fragments
     * @see https://tools.ietf.org/html/rfc3986#appendix-A
     * @const string
     */
    const SUB_DELIMITER_CHARACTERS = "!&',;=\\$\\(\\)\\*\\+";

    private $admittedSchemesPorts = [
        'http' => 80,
        'https' => 443,
    ]
    ;
    /**
     * @var string $scheme the scheme component of the URI
     *
     */
    protected $scheme = "";

    /**
     * @var string $authority The URI authority
     */
    protected $authority = "";

    /**
     * @var string $userInfo The URI user information
     */
    protected $userInfo = "";

    /**
     * @var string $host The URI host
     */
    protected $host = "";

    /**
     * @var int $port
     */
    protected $port;

    /**
     * @var string $path
     */
    protected $path = "";

    /**
     * @var string $query
     */
    protected $query = "";

    /**
     * @var string     *
     */
    protected $fragment = "";

    /**
     * Uri constructor.
     * @param $scheme
     * @param $userInfo
     * @param $host
     * @param $port
     * @param $path
     * @param $query
     * @param $fragment
     */


    public function __construct($scheme, $userInfo, $host, $port, $path, $query, $fragment)
    {
        $this->scheme = $this->normalizedScheme($scheme);
        $this->userInfo = $userInfo;
        $this->host = $host;
        $this->port = $port;
        $this->path = $this->normalizedPath($path);
        $this->query = $this->normalizedQueryOrFragment($query);
        $this->fragment = $this->normalizedQueryOrFragment($fragment);

    }

    /**
     * Retrieve the scheme component of the URI.
     *
     * @return string The URI scheme.
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return static
     */
    public static function retrieveUriFromServer()
    {
        $server = $_SERVER;
        //Scheme
        $securedScheme = self::get("HTTPS", $server);
        $scheme = empty($securedScheme) ? self::get("REQUEST_SCHEME", $server) : "https";

        //Authority
        $username = empty(self::get("PHP_AUTH_USER", $server)) ? "" : self::get("PHP_AUTH_USER", $server);
        $password = empty(self::get('PHP_AUTH_PW', $server)) ? "" : self::get('PHP_AUTH_PW', $server);

        $userInfo = (isset($password) && (sizeof($password) > 1)) ? $username . ":" . $password : $username;

        //Host
        $host = empty(self::get("HTTP_HOST", $server)) ? "" : self::get("HTTP_HOST", $server);

        //Port
        $port = empty(self::get("SERVER_PORT", $server)) ? "" : self::get("SERVER_PORT", $server);

        //Path
        $path = empty(self::get("REQUEST_URI", $server)) ? "" : self::get("REQUEST_URI", $server);

        //Query
        $query = empty(self::get("QUERY_STRING", $server)) ? "" : self::get("QUERY_STRING", $server);

        //Fragment
        $fragment = "";

        return new static($scheme, $userInfo, $host, $port, $path, $query, $fragment);
    }

    /**
     * Retrieve the authority component of the URI in "[user-info@]host[:port]" format
     *
     * If no authority information is present, this method MUST return an empty
     * string.
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it isn't included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority()
    {
        $userInfo = $this->getUserInfo();
        $host = $this->getHost();
        $port = $this->getPort();
        $scheme = $this->getScheme();



        return ($userInfo ? $userInfo . "@" : "")
        . $host
        . (($port === null || $this->normalizedPort($scheme, $port)) ? "" : $userInfo . $port);
    }

    /**
     * Retrieve the user information component of the URI
     *
     * If no user information is present, this method MUST return an empty
     * string.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * Retrieve the host component of the URI.
     *
     * If no host is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.2.2.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string The URI host.
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Retrieve the port component of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     *
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The URI port.
     */
    public function getPort()
    {
        return $this->normalizedPort($this->scheme, $this->port);
    }

    /**
     * Retrieve the path component of the URI.
     *
     * @return string The URI path.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * @return string The URI query string.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * If no fragment is present, this method MUST return an empty string.
     *
     * The leading "#" character is not part of the fragment and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.5.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string The URI fragment.
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Return an instance with the specified scheme.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified scheme.
     *
     * Implementations MUST support the schemes "http" and "https" case
     * insensitively, and MAY accommodate other schemes if required.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     * @return static A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
        // TODO: Implement withScheme() method.
    }

    /**
     * Return an instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string $user The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * @return static A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        // TODO: Implement withUserInfo() method.
    }

    /**
     * Return an instance with the specified host.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     * @return static A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host)
    {
        // TODO: Implement withHost() method.
    }

    /**
     * Return an instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified port.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     * @return static A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        // TODO: Implement withPort() method.
    }

    /**
     * Return an instance with the specified path.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified path.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * If the path is intended to be domain-relative rather than path relative then
     * it must begin with a slash ("/"). Paths not starting with a slash ("/")
     * are assumed to be relative to some base path known to the application or
     * consumer.
     *
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param string $path The path to use with the new instance.
     * @return static A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        // TODO: Implement withPath() method.
    }

    /**
     * Return an instance with the specified query string.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified query string.
     *
     * Users can provide both encoded and decoded query characters.
     * Implementations ensure the correct encoding as outlined in getQuery().
     *
     * An empty query string value is equivalent to removing the query string.
     *
     * @param string $query The query string to use with the new instance.
     * @return static A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
        // TODO: Implement withQuery() method.
    }

    /**
     * Return an instance with the specified URI fragment.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified URI fragment.
     *
     * Users can provide both encoded and decoded fragment characters.
     * Implementations ensure the correct encoding as outlined in getFragment().
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     * @return static A new instance with the specified fragment.
     */
    public function withFragment($fragment)
    {
        // TODO: Implement withFragment() method.
    }

    /**
     * Return the string representation as a URI reference.
     *
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString()
    {
        $uriStringRepresentation = "";
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();

        if ($scheme) {
            $uriStringRepresentation .= $scheme . ":";
        }

        if ($authority) {
            $uriStringRepresentation .= "//$authority";
        }

        if ($path) {
            if (empty($path) || "/" !== substr($path, 0, 1) ) {
                $uriStringRepresentation .= "/$path";
            }
        }

        if ($query) {
            $uriStringRepresentation .= "?$query";
        }

        if ($fragment) {
            $uriStringRepresentation .= "#$fragment";
        }

        return $uriStringRepresentation;
    }

    /**
     * If no scheme is present, this method returns an empty string
     * Normalizes URI scheme to lowercase
     * If the trailing ":" character is present this method remove it
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     *
     * @param string $scheme
     * @return string
     * @return \InvalidArgumentException If $scheme is not a string
     * @return \InvalidArgumentException If $scheme is not match to scheme's pattern described at https://tools.ietf.org/html/rfc3986#section-3.1
     */
    private function normalizedScheme($scheme)
    {
        $scheme = preg_replace("/:/", "", strtolower($scheme));

        if (empty($scheme)) {
            return '';
        }

        if (!is_string($scheme) || !preg_match("/(?:^[a-z]+)(?:[a-z]|\\d|\\+|-|.)*/", $scheme)) {
            return new \InvalidArgumentException("$scheme doesn't match to standards");
        }

        return $scheme;

    }



    /**
     * Percent-encode but not double-encode any character of URI path
     * Implements ability to handle empty, absolute (starting with a slash) or
     * rootless (not starting with a slash)
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @param string $path
     * @return string
     */

    private function normalizedPath($path)
    {
        $path = preg_replace_callback("/(?:[^" . self::UNRESERVED_CHARACTERS
            . self::SUB_DELIMITER_CHARACTERS
            . "@:%\\/]+|%(?![a-fA-F0-9]{2}))/",
            [$this, "percentEncoded"],
            $path)
        ;

        if (empty($path)) {
            return $path;
        }

        if ($path[0] !== "/") {
            return $path;
        }

        return $path;
    }

    /**
     * Percent-encode but not double-encode any character of URI query or fragment
     * If leading "?" character this method remove it
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     *
     * @param string $queryOrFragment
     * @return string
     * @return \InvalidArgumentException If $queryOrFragment is not a string
     */
    private function normalizedQueryOrFragment($queryOrFragment)
    {
        if (!is_string($queryOrFragment)) {
            return new \InvalidArgumentException("$queryOrFragment must be string");
        }

        $queryOrFragment = preg_replace_callback("/(?:[^" . self::UNRESERVED_CHARACTERS
            . self::SUB_DELIMITER_CHARACTERS
            . "@:%\\/?]+|%(?![a-fA-F0-9]{2}))/",
            [$this, "percentEncoded"],
            $queryOrFragment)
        ;

        if (!empty($queryOrFragment) &&
            ((strpos($queryOrFragment, "?") === 0) || (strpos($queryOrFragment, "#") === 0)))  {
            $queryOrFragment = substr($queryOrFragment, 1);
        }

        return $queryOrFragment;
    }

    /**
     * Percent-encode character according to regex
     * @param array $match
     * @return string
     */
    private function percentEncoded(array $match)
    {
        return rawurlencode($match[0]);
    }

    /**
     * @param $scheme
     * @param $port
     * @return bool|null
     */
    private function normalizedPort($scheme, $port)
    {
        if (!$port && !$scheme) {
            return null;
        }

        if (!$port) {
            return null;
        }

        return (isset($port) && ($port !== $this->admittedSchemesPorts[$scheme])) ? $port : null;
    }

    private static function get($key, array $value, $default = null)
    {
        if (array_key_exists($key, $value)) {
            return $value[$key];
        }

        return $default;
    }
}