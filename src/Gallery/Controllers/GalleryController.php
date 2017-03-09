<?php

namespace Gallery\Controllers;

use Core\Controller\AbstractController;
use Gallery\Models\AlbumMapper;

class GalleryController extends AbstractController
{
    public function indexAction()
    {
        $albumMapper = new AlbumMapper();
        $albums = $albumMapper->getRootAlbums();

        return $this->getTemplating()->render($this->getResponse(),
                                                "gallery/gallery.phtml",
                                                ['albums' => $albums, ]
        );
    }

    public function showImagesAlbumsAction($level)
    {
       $albumMapper = new AlbumMapper();
        $albums = $albumMapper->getRootAlbums();

        return $this->getTemplating()->render($this->getResponse(),
            "albums/albumsEdit.phtml",
            ['albums' => $albums, ]
        );


    }
}