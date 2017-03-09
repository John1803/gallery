<?php
//require "../../../";
namespace Gallery\Controllers;

use Core\Controller\AbstractController;
use Core\Http\UploadedFile;
use Gallery\Helpers\ImageDataHandler;
use Gallery\Models\AlbumMapper;
use Gallery\Models\ImageEntity;
use Gallery\Models\ImageMapper;
use Psr\Http\Message\ServerRequestInterface;

class ImageController extends AbstractController
{
    public function imageFormAction()
    {
        $albumMapper = new AlbumMapper();
        $albums = $albumMapper->getAlbumsTree();
        return $this->getTemplating()->render($this->getResponse(),
            "/images/imagesForm.phtml",
            ["albums" => $albums, ]
        );
    }

    public function uploadAction(ServerRequestInterface $serverRequest)
    {
        $albumMapper = new AlbumMapper();
        $imageMapper = new ImageMapper();
        $imageDataHandler = new ImageDataHandler();
        $uploadedImages = $serverRequest->getUploadedFiles();
        $data = $serverRequest->getParsedBody();
        $albumId = filter_var($data['album'], FILTER_VALIDATE_INT);
        $album = $albumMapper->getAlbumById($albumId);
        /**
         * @var \Core\Http\UploadedFile $image
         */
        foreach ($uploadedImages['image'] as $image) {
            $uploadedFile = new UploadedFile($image->getClientFilename(),
                                                $image->getClientMediaType(),
                                                $image->getFile(),
                                                $image->getSize(),
                                                $image->getError()
            );

            $imageEntity = new ImageEntity($imageDataHandler->prepareData($album, $uploadedFile));
            $uploadedFile->moveTo($imageEntity->getPath());
            $imageMapper->save($imageEntity);
        }

    }
}