<?php

namespace Gallery\Helpers;

use Gallery\Models\AlbumEntity;
use Psr\Http\Message\UploadedFileInterface;

class ImageDataHandler
{
    public function prepareData(AlbumEntity $album, UploadedFileInterface $uploadedFile)
    {
        $image = [];

        $image['albumId'] = $album->getId();
        $image['title'] = filter_var($uploadedFile->getClientFilename(), FILTER_SANITIZE_STRING);
        $image['mediaType'] = filter_var($uploadedFile->getClientMediaType(), FILTER_SANITIZE_STRING);
        $image['path'] = $album->getPath(). DIRECTORY_SEPARATOR . $image['title'];
        $image['size'] = filter_var($uploadedFile->getSize(), FILTER_SANITIZE_STRING);

        return $image;
    }
}