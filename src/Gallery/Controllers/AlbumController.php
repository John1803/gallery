<?php

namespace Gallery\Controllers;

use Core\Controller\AbstractController;
use Core\Helpers\Filesystem;
use Gallery\Models\Album;
use Psr\Http\Message\ServerRequestInterface;

class AlbumController extends AbstractController
{

    const ALBUMS_DIR = "../web/albums/";

    public function indexAction()
    {
        // TODO: shows created albums
    }
    public function showAction($level)
    {
        // TODO: shows created albums
    }
    public function createAction(ServerRequestInterface $serverRequest)
    {
        $filesystem = new Filesystem();
        $albumsData = $serverRequest->getParsedBody();

        /**
         * Ability to create few subalbums
         */

        if (is_array($albumsData['title'])) {
            foreach ($albumsData['title'] as $key => $value) {
                $albumsData['title'][$key] = self::ALBUMS_DIR . filter_var($albumsData['title'][$key], FILTER_SANITIZE_STRING);
            }
        } else {
            $albumsData['title'] = self::ALBUMS_DIR . filter_var($albumsData['title'], FILTER_SANITIZE_STRING);
        }

        $filesystem->mkdir($albumsData['title']);
    }
    public function updateAction()
    {
        // TODO: update created albums
    }
    public function deleteAction()
    {
        // TODO: deletes created albums
    }

    public function albumFormCreationAction()
    {
        return $this->getTemplating()->render($this->getResponse(), '/albums/albumForm.phtml');
    }
}