<?php

namespace Gallery\Models;

class AlbumEntity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $path;


    /**
     * Field is used to store the tree left value
     *
     * @var int
     */
    protected $lft;

    /**
     * Field is used to store the tree right value
     *
     * @var int
     */
    protected $rgt;

    /**
     * Field is used to store the tree level value
     *
     * @var int
     */
    protected $lvl;

    /**
     * @var
     */

    /**
     * AlbumEntity constructor.
     * @param array $data
     */

    public function __construct(array $data)
    {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        $this->title = $data['title'];
        $this->path = $data['path'];
        $this->lft = $data['lft'];
        $this->rgt = $data['rgt'];
        $this->lvl = $data['lvl'];
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
    public function getLft(): int
    {
        return $this->lft;
    }

    /**
     * @param int $lft
     */
    public function setLft(int $lft)
    {
        $this->lft = $lft;
    }

    /**
     * @return int
     */
    public function getRgt(): int
    {
        return $this->rgt;
    }

    /**
     * @param int $rgt
     */
    public function setRgt(int $rgt)
    {
        $this->rgt = $rgt;
    }

    /**
     * @return int
     */
    public function getLvl(): int
    {
        return $this->lvl;
    }

    /**
     * @param int $lvl
     */
    public function setLvl(int $lvl)
    {
        $this->lvl = $lvl;
    }


}