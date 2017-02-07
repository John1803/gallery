<?php
namespace Core\Http;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    /**
     * Resource modes
     *
     * @var array $modes
     */
    protected $modes = [
        'readable' => ['r', 'r+', 'w+', 'a+', 'x+', 'c+',],
        'writable' => ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+',],
    ]
    ;

    /**
     * @var resource $stream
     *
     */
    protected $stream;

    /**
     * Stream constructor.
     * @param $stream
     * @param string $mode
     */
    public function __construct($stream, $mode = 'r')
    {
        $this->stream = $stream;
    }

    /**
     * @param null $stream
     * @param string $mode
     * @return static
     */
    public static function retrieveStream($stream = null, $mode = 'r')
    {
        if(is_string($stream)) {
            $stream = fopen($stream, $mode);
        }
        elseif($stream === null or !is_resource($stream)) {
            $stream = fopen('php://temp', $mode);
        }

        return new static($stream);
    }
    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */

    public function __toString()
    {
        try {
            $this->rewind();
            return $this->getContents();
        } catch (\RuntimeException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        fclose($this->detach());
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $stream = $this->stream;
        $this->stream = null;
        return $stream;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        if ($this->stream === null) {
            return null;
        }
        $statistic = fstat($this->stream);

        return $statistic['size'];
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        if (!$this->stream) {
            throw new \RuntimeException("Cannot return the current position, no resource");
        }

        return ftell($this->stream);
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return feof($this->stream);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        if (!$this->getMetadata("seekable")) {
            return false;
        }

        return true;
    }

    /**
     * Seek to a position in the stream.
     *
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset.
     * @throws \RuntimeException on failure.
     * @return bool $soughtResult
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->stream) {
            throw new \RuntimeException("Cannot seek position, no resource");
        }

        $soughtResult = fseek($this->stream, $offset, $whence);
        return $soughtResult;
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {

        if (!$this->isSeekable()) {
            throw new \RuntimeException("The stream is not seekable");
        }

        return $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        $streamMode = $this->getMetadata("mode");

        if (!in_array($streamMode, $this->modes['writable'])) {
            return false;
        }

        return true;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        if (!$this->stream) {
            throw new \RuntimeException("Cannot write data to the stream, no resource");
        }

        if (!$this->isWritable()) {
            throw new \RuntimeException("Cannot write data to the stream, it is not writable");
        }

        if (!fwrite($this->stream, $string)) {
            throw new \RuntimeException("Cannot write data to the stream, writing process was interrupted");
        }

        return fwrite($this->stream, $string);
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        $streamMode = $this->getMetadata("mode");

        if (!in_array($streamMode, $this->modes['readable'])) {
            return false;
        }

        return true;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        if (!$this->stream) {
            throw new \RuntimeException("Cannot read data from the stream, no resource");
        }

        if (!$this->isReadable()) {
            throw new \RuntimeException("Cannot read data from the stream, it is not writable");
        }

        if (!fread($this->stream, $length)) {
            throw new \RuntimeException("Cannot write data to the stream, writing process was interrupted");
        }

        return fread($this->stream, $length);
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        if (!$this->stream) {
            throw new \RuntimeException("Stream is not readable");
        }

        if (!stream_get_contents($this->stream)) {
            throw new \RuntimeException("Error reading of stream");
        }

        return stream_get_contents($this->stream);
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        $metaData = stream_get_meta_data($this->stream);

        if ($key === null) {
            return $metaData;
        }

        if (array_key_exists($key, $metaData)  && isset($metaData[$key])) {
            return $metaData[$key];
        }

        return null;
    }
}