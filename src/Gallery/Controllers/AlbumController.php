<?php

namespace Gallery\Controllers;

use Core\Controller\AbstractController;
use Core\Helpers\Filesystem;
use Gallery\Helpers\AlbumDataHandler;
use Gallery\Models\Album;
use Gallery\Models\AlbumEntity;
use Gallery\Models\AlbumMapper;
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
        $albumMapper = new AlbumMapper();
        $albumHandlerData = new AlbumDataHandler();
        $albumData = $serverRequest->getParsedBody();
        $albumAncestorId = filter_var($albumData['parent'], FILTER_VALIDATE_INT);
//        $albumLastSibling = $albumMapper->getAlbumWithMaxRightProperty();

        if (true === (bool)$albumAncestorId &&
            0 === $albumMapper->getAlbumById($albumAncestorId)->getLvl()) {
            $albumAncestor = $albumMapper->getAlbumById($albumAncestorId);
            $albumHandledData = $albumHandlerData->prepareDescendantLeftRightLevelPosition($albumAncestor);
            $albumData = $albumHandlerData->mergeReceivedHandledData($albumData, $albumHandledData);
        } else if (true === (bool)$albumAncestorId &&
            $albumMapper->getAlbumById($albumAncestorId)->getLvl() > 0) {
            $albumAncestor = $albumMapper->getAlbumById($albumAncestorId);
            $albumHandledData = $albumHandlerData->prepareDescendantLeftRightLevelPosition($albumAncestor);
            $albumData = $albumHandlerData->mergeReceivedHandledData($albumData, $albumHandledData);
        } else if ($albumLastSibling = $albumMapper->getAlbumWithMaxRightProperty()) {
//            $albumLastSibling = $albumMapper->getAlbumWithMaxRightProperty();
            $albumHandledData = $albumHandlerData->prepareSiblingLeftRightLevelPosition($albumLastSibling);
            $albumData = $albumHandlerData->mergeReceivedHandledData($albumData, $albumHandledData);
        }

//        if (0 === $albumAncestorId &&
//            !(is_null($albumLastSibling)) &&
//            0 === $albumLastSibling->getLvl()) {
//            $albumLastSibling = $albumMapper->getAlbumWithMaxRightProperty();
//            $albumHandledData = $albumHandlerData->prepareSiblingLeftRightLevelPosition($albumLastSibling);
//            $albumData = $albumHandlerData->mergeReceivedHandledData($albumData, $albumHandledData);
//        } else if ($albumAncestor = $albumMapper->getAlbumById($albumAncestorId)) {
//            $albumAncestor = $albumMapper->getAlbumById($albumAncestorId);
//            $albumHandledData = $albumHandlerData->prepareDescendantLeftRightLevelPosition($albumAncestor);
//            $albumData = $albumHandlerData->mergeReceivedHandledData($albumData, $albumHandledData);
//        }


        $handledAlbumData = $albumHandlerData->filterData($albumData);
        $album = new AlbumEntity($handledAlbumData);
        $filesystem->mkdir($album->getPath());
        $albumMapper->save($album);
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
        $albums = $albumMapper->getDirectDescendantAlbums($id);

        return $this->getTemplating()->render($this->getResponse(),
                                                "/albums/albumsEdit.phtml",
                                                ["albums" => $albums,
                                                "albumId" => $id, ]
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