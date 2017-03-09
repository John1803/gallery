<?php

namespace Gallery\Controllers;

use Core\Controller\AbstractController;
use Gallery\Models\AlbumMapper;
use Gallery\Models\ImageMapper;

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

    public function showImagesAlbumsAction($id)
    {
       $albumMapper = new AlbumMapper();
       $imageMapper = new ImageMapper();
       $albums = $albumMapper->getDirectDescendantAlbums($id);
       $images = $imageMapper->getImagesOfAlbum($id);

        return $this->getTemplating()->render($this->getResponse(),
                                                "gallery/albumsImages.phtml",
                                                ["albums" => $albums,
                                                "images" => $images, ]
        );


    }
}