<?php

namespace Gallery\Controllers;

use Core\Controller\AbstractController;
use Core\Helpers\Filesystem;
use Gallery\Helpers\AlbumDataHandler;
use Gallery\Models\AlbumEntity;
use Gallery\Models\AlbumMapper;
use Psr\Http\Message\ServerRequestInterface;

class AlbumController extends AbstractController
{

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
        $albumMapper = new AlbumMapper();
        $albumHandlerData = new AlbumDataHandler();
        $albumData = $serverRequest->getParsedBody();
        $albumAncestorId = filter_var($albumData['parent'], FILTER_VALIDATE_INT);

        if (true === (bool)$albumAncestorId &&
            0 <= $albumMapper->getAlbumById($albumAncestorId)->getLvl()) {
            $albumAncestor = $albumMapper->getAlbumById($albumAncestorId);
            $albumHandledData = $albumHandlerData->prepareDescendantData($albumAncestor, $albumData);
            $albumData = $albumHandlerData->mergeReceivedHandledData($albumData, $albumHandledData);
        }  else if ($albumLastSibling = $albumMapper->getAlbumWithMaxRightProperty()) {
            $albumHandledData = $albumHandlerData->prepareSiblingData($albumLastSibling);
            $albumData = $albumHandlerData->mergeReceivedHandledData($albumData, $albumHandledData);
        }

        $handledAlbumData = $albumHandlerData->filterData($albumData);
        $album = new AlbumEntity($handledAlbumData);
        $filesystem->mkdir($album->getPath());
        $albumMapper->save($album);

        // TODO: Redirect

        return $this->getTemplating()->render($this->getResponse(),
                                             "/albums/albumRedirect.phtml",
                                                ["albumId" => $albumAncestorId,
                                                "albumTitle" => $album->getTitle(), ]
        );
    }

    public function editAction()
    {
        $albumMapper = new AlbumMapper();
        $albums = $albumMapper->getRootAlbums();

        return $this->getTemplating()->render($this->getResponse(),
                                                "/albums/albumsEdit.phtml",
                                                ["albums" => $albums, ]
        );
    }

    public function editAlbumAction($id)
    {
        $albumMapper = new AlbumMapper();
        $albumAncestor = $albumMapper->getAlbumById($id);
        $albums = $albumMapper->getDirectDescendantAlbums($id);

        return $this->getTemplating()->render($this->getResponse(),
                                                "/albums/albumsEdit.phtml",
                                                ["albums" => $albums,
                                                "albumId" => $id,
                                                "albumTitle" => $albumAncestor->getTitle(), ]
        );
    }
    public function updateAction()
    {
        // TODO: update created albums
    }
    public function deleteAction()
    {
        // TODO: deletes created albums
    }

    public function albumFormCreationAction(ServerRequestInterface $serverRequest)
    {
        $ancestorId = filter_var($serverRequest->getParsedBody()["parent"], FILTER_VALIDATE_INT);
        return $this->getTemplating()->render($this->getResponse(),
                                                "/albums/albumForm.phtml",
                                                ["albumId" => $ancestorId, ]
        );
    }
}