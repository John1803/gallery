<?php

namespace Gallery\Models;

use Core\Model\AbstractModel;

class ImageMapper extends AbstractModel
{
    public function getImagesOfAlbum($id)
    {
        $sql = "SELECT img.id, img.album_id, img.title, img.path, img.size, img.mediaType
                FROM images AS img
                  JOIN albums AS descendant
                    ON descendant.id = img.album_id
                        AND descendant.id = :id";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindParam(":id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = new ImageEntity($row);
        }
        return $results;
    }

    public function save(ImageEntity $image)
    {
        $sql = "INSERT INTO images(album_id, title, path, size, mediaType) 
                VALUES(:album_id, :title, :path, :size, :mediaType);";

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindParam(":album_id", $image->getAlbumId(), \PDO::PARAM_INT);
        $stmt->bindParam(":title", $image->getTitle(), \PDO::PARAM_STR);
        $stmt->bindParam(":path", $image->getPath(), \PDO::PARAM_STR);
        $stmt->bindParam(":size", $image->getSize(), \PDO::PARAM_INT);
        $stmt->bindParam(":mediaType", $image->getTitle(), \PDO::PARAM_STR);
        $result = $stmt->execute();

        if (!$result) {
            throw new \Exception("Could not save record");
        }
    }
}