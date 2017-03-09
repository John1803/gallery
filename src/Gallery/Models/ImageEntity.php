<?php
/**
 * Created by PhpStorm.
 * User: will-o-the-wisp
 * Date: 09/03/17
 * Time: 12:23
 */

namespace Gallery\Models;


class ImageEntity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $albumId;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var string
     */
    protected $mediaType;
    /**
     * ImageEntity constructor.
     * @param array $data
     */

    public function __construct(array $data)
    {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        $this->albumId = $data['albumId'];
        $this->title = $data['title'];
        $this->path = $data['path'];
        $this->size = $data['size'];
        $this->mediaType = $data['mediaType'];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getAlbumId(): int
    {
        return $this->albumId;
    }

    /**
     * @param int $albumId
     */
    public function setAlbumId(int $albumId)
    {
        $this->albumId = $albumId;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size)
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    /**
     * @param string $mediaType
     */
    public function setMediaType(string $mediaType)
    {
        $this->mediaType = $mediaType;
    }



}