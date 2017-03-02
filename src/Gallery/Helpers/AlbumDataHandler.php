<?php

namespace Gallery\Helpers;

use Gallery\Models\AlbumEntity;

class AlbumDataHandler
{
    const ALBUMS_DIR = "../web/albums/";

    /**
     * @var array
     */
    protected $data;

    public function filterData(array $data)
    {
        $album = [];
        $album['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING);
        $album['path'] = self::ALBUMS_DIR . $album['title'];
        $album['lft'] = $data['lft'] ? filter_var($data['lft'], FILTER_VALIDATE_INT) : 1;
        $album['rgt'] = $data['rgt'] ? filter_var($data['rgt'], FILTER_VALIDATE_INT) : 2;
        $album['lvl'] = $data['lvl'] ? filter_var($data['lvl'], FILTER_VALIDATE_INT) : 0;

        return $album;
    }

    public function prepareDescendantLeftRightLevelPosition(AlbumEntity $album)
    {
        $albumData['lft'] = $album->getRgt();
        $albumData['rgt'] = $album->getRgt() + 1;
        $albumData['lvl'] = $album->getLvl() + 1;

        return $albumData;
    }

    public function prepareSiblingLeftRightLevelPosition(AlbumEntity $album)
    {
        $albumData['lft'] = $album->getLft() + 2;
        $albumData['rgt'] = $album->getRgt() + 2;
        $albumData['lvl'] = $album->getLvl();

        return $albumData;
    }

    public function mergeReceivedHandledData(array $albumFormData, array $albumHandledData)
    {
        return array_merge($albumFormData, $albumHandledData);
    }
}