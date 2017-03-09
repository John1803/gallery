<?php

namespace Gallery\Models;

use Core\Model\AbstractModel;

class ImageMapper extends AbstractModel
{
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