<?php

namespace Core\Http;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class UploadedFile
 * @package Http
 */
class UploadedFile implements UploadedFileInterface
{
    /**
     * Full path to the file
     *
     * @var string
     */
    protected $file;

    /**
     * @var string $clientFilename
     */
    protected $clientFilename;

    /**
     * @var string $clientMediaType
     */
    protected $clientMediaType;

    /**
     * @var int $size
     */
    protected $size;

    /**
     * @var string $error
     */
    protected $error;

    /**
     * @var StreamInterface
     */
    protected $stream;

    /**
     * Indicates that file did not move
     *
     * @var boolean $moved
     */
    private $moved = false;

    /**
     * UploadedFile constructor.
     * @param $clientFilename
     * @param $clientMediaType
     * @param $file
     * @param $size
     * @param $error
     */
    public function __construct($clientFilename = null, $clientMediaType = null, $file = null, $size = null, $error = null)
    {
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
        $this->file = $file;
        $this->size = $size;
        $this->error = $error;
    }

    /**
     * @return array
     */
    public static function retrieveUploadedFiles()
    {
        if(isset($_FILES)) {
            return static::normalizedUploadedFilesTree($_FILES);
        }

        return [];
    }

    /**
     * @param array $uploadedFiles
     * @return array
     */
    private static function normalizedUploadedFilesTree(array $uploadedFiles) {

        $normalizedUploadedFilesTree = [];

        foreach ($uploadedFiles as $attachment => $uploadedFileInfo) {
            $fileMetadata = [];
            if (!is_array($uploadedFileInfo['name'])) {
                $normalizedUploadedFilesTree[$attachment] = new static(
                    $uploadedFileInfo['name'],
                    $uploadedFileInfo['type'],
                    $uploadedFileInfo['tmp_name'],
                    $uploadedFileInfo['size'],
                    $uploadedFileInfo['error']
                );
            } else {
                foreach ($uploadedFileInfo['name'] as $fileIndex => $name) {
                    $fileMetadata[$fileIndex]['name'] = $uploadedFileInfo['name'][$fileIndex];
                    $fileMetadata[$fileIndex]['type'] = $uploadedFileInfo['type'][$fileIndex];
                    $fileMetadata[$fileIndex]['tmp_name'] = $uploadedFileInfo['tmp_name'][$fileIndex];
                    $fileMetadata[$fileIndex]['size'] = $uploadedFileInfo['size'][$fileIndex];
                    $fileMetadata[$fileIndex]['error'] = $uploadedFileInfo['error'][$fileIndex];

                    $normalizedUploadedFilesTree[$attachment] = static::normalizedUploadedFilesTree($fileMetadata);
                }
            }
        }

        return $normalizedUploadedFilesTree;
    }
    /**
     * Retrieve a stream representing the uploaded file.
     *
     * This method MUST return a StreamInterface instance, representing the
     * uploaded file. The purpose of this method is to allow utilizing native PHP
     * stream functionality to manipulate the file upload, such as
     * stream_copy_to_stream() (though the result will need to be decorated in a
     * native PHP stream wrapper to work with such functions).
     *
     * If the moveTo() method has been called previously, this method MUST raise
     * an exception.
     *
     * @return StreamInterface Stream representation of the uploaded file.
     * @throws \RuntimeException in cases when no stream is available or can be
     *     created.
     */
    public function getStream()
    {
        if (!$this->moved) {
            throw new \RuntimeException("moveTo() method has been called previously");
        }

        if ($this->stream === null) {
            $this->stream = new Stream(fopen($this->file, 'r'));
        }

        return $this->stream;
    }

    /**
     * Move the uploaded file to a new location.
     *
     * Use this method as an alternative to move_uploaded_file(). This method is
     * guaranteed to work in both SAPI and non-SAPI environments.
     * Implementations must determine which environment they are in, and use the
     * appropriate method (move_uploaded_file(), rename(), or a stream
     * operation) to perform the operation.
     *
     * $targetPath may be an absolute path, or a relative path. If it is a
     * relative path, resolution should be the same as used by PHP's rename()
     * function.
     *
     * The original file or stream MUST be removed on completion.
     *
     * If this method is called more than once, any subsequent calls MUST raise
     * an exception.
     *
     * When used in an SAPI environment where $_FILES is populated, when writing
     * files via moveTo(), is_uploaded_file() and move_uploaded_file() SHOULD be
     * used to ensure permissions and upload status are verified correctly.
     *
     * If you wish to move to a stream, use getStream(), as SAPI operations
     * cannot guarantee writing to stream destinations.
     *
     * @see http://php.net/is_uploaded_file
     * @see http://php.net/move_uploaded_file
     * @param string $targetPath Path to which to move the uploaded file.
     * @throws \InvalidArgumentException if the $targetPath specified is invalid.
     * @throws \RuntimeException on any error during the move operation, or on
     *     the second or subsequent call to the method.
     *
     * @return bool
     */

    public function moveTo($targetPath)
    {

        if ($this->moved) {
            throw new \RuntimeException("Second or subsequent call to the method");
        }

        if (php_sapi_name() === "cli") {
            rename($this->clientFilename, $targetPath);
        }

        if (strpos($targetPath, "://") === 0) {

        }

        if (!is_uploaded_file($this->file)) {
            throw new \RuntimeException("$this->clientFilename is not valid");
        }

        if (!move_uploaded_file($this->file, $targetPath)) {
            throw new \RuntimeException("Could not move file to directory");
        }

        return $this->moved = true;
    }

    /**
     * Retrieve the file size.
     *
     * Implementations SHOULD return the value stored in the "size" key of
     * the file in the $_FILES array if available, as PHP calculates this based
     * on the actual size transmitted.
     *
     * @return int|null The file size in bytes or null if unknown.
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Retrieve the error associated with the uploaded file.
     *
     * The return value MUST be one of PHP's UPLOAD_ERR_XXX constants.
     *
     * If the file was uploaded successfully, this method MUST return
     * UPLOAD_ERR_OK.
     *
     * Implementations SHOULD return the value stored in the "error" key of
     * the file in the $_FILES array.
     *
     * @see http://php.net/manual/en/features.file-upload.errors.php
     * @return int One of PHP's UPLOAD_ERR_XXX constants.
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Retrieve the filename sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious filename with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "name" key of
     * the file in the $_FILES array.
     *
     * @return string|null The filename sent by the client or null if none
     *     was provided.
     */
    public function getClientFilename()
    {
        return $this->clientFilename;
    }

    /**
     * Retrieve the media type sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious media type with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "type" key of
     * the file in the $_FILES array.
     *
     * @return string|null The media type sent by the client or null if none
     *     was provided.
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }

    /**
     * Non-PSR7 method
     *
     * Get full path to the file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
}