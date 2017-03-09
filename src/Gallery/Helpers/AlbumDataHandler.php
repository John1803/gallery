<?php

namespace Gallery\Helpers;

use Gallery\Models\AlbumEntity;

class AlbumDataHandler
{
    private $path = ".." . DIRECTORY_SEPARATOR . "web" . DIRECTORY_SEPARATOR . "albums" . DIRECTORY_SEPARATOR;

    /**
     * @var array
     */
    protected $data;

    private function getPath()
    {
        return $this->path;
    }

    public function filterData(array $data)
    {
        $album = [];
        $album['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING);

        if (strpos($data['path'], $this->getPath()) === 0) {
            $album['path'] = $data['path'];
        } else {
            $album['path'] = $this->getPath() . $album['title'];
        }

        $album['lft'] = $data['lft'] ? filter_var($data['lft'], FILTER_VALIDATE_INT) : 1;
        $album['rgt'] = $data['rgt'] ? filter_var($data['rgt'], FILTER_VALIDATE_INT) : 2;
        $album['lvl'] = $data['lvl'] ? filter_var($data['lvl'], FILTER_VALIDATE_INT) : 0;

        return $album;
    }

    public function prepareDescendantData(AlbumEntity $album, $albumData)
    {
        $albumData['lft'] = $album->getRgt();
        $albumData['rgt'] = $album->getRgt() + 1;
        $albumData['lvl'] = $album->getLvl() + 1;
        $albumData['path'] = $album->getPath() . DIRECTORY_SEPARATOR . $albumData['title'];

        return $albumData;
    }

    public function prepareSiblingData(AlbumEntity $album)
    {
        $albumData['lft'] = $album->getRgt() + 1;
        $albumData['rgt'] = $album->getRgt() + 2;
        $albumData['lvl'] = $album->getLvl();


        return $albumData;
    }

    public function prepareDescendantPath(AlbumEntity $album, $albumData)
    {
        $albumData['path'] = $album->getPath() . DIRECTORY_SEPARATOR . $albumData['title'];
        return $albumData;
    }

    public function mergeReceivedHandledData(array $albumFormData, array $albumHandledData)
    {
        return array_merge($albumFormData, $albumHandledData);
    }
}