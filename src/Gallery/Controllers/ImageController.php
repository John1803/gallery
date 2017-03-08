<?php
//require "../../../";
namespace Gallery\Controllers;

use Core\Controller\AbstractController;
use Core\Http\UploadedFile;
use Gallery\Models\AlbumMapper;
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
                                                $image->getError());
            $albumPath = $album->getPath(). DIRECTORY_SEPARATOR . $uploadedFile->getClientFilename();
            $uploadedFile->moveTo($albumPath);
        }

        var_dump($uploadedImages);

    }
}