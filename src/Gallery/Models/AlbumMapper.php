<?php

namespace Gallery\Models;

use Core\Model\AbstractModel;

class AlbumMapper extends AbstractModel
{
    public function getRootAlbums()
    {
        $sql = "SELECT a.*
            FROM albums a
            WHERE a.lvl = 0;";
        $stmt = $this->getConnection()->query($sql);

        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = new AlbumEntity($row);
        }
        return $results;
    }

    public function getAlbumsTree()
    {
        $sql = "SELECT descendant.id, descendant.title, descendant.path, descendant.lft, descendant.rgt, descendant.lvl 
                FROM albums AS descendant
                JOIN albums AS ancestor
                ON descendant.lft BETWEEN ancestor.lft AND ancestor.rgt
                GROUP BY descendant.title
                ORDER BY descendant.lft;";

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();

        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = new AlbumEntity($row);
        }
        return $results;
    }

    public function getDirectDescendantAlbums($id)
    {
        $sql = "SELECT descendant.id, descendant.title, descendant.path, descendant.lft, descendant.rgt, descendant.lvl
                FROM albums AS descendant
                JOIN albums AS ancestor
                ON ancestor.id = :id
                AND descendant.lvl > ancestor.lvl
                AND descendant.lvl < ancestor.lvl + 2
                AND descendant.lft
                BETWEEN ancestor.lft AND ancestor.rgt;";

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindParam(":id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = new AlbumEntity($row);
        }
        return $results;

    }
    
    public function getAlbumWithMaxRightProperty()
    {
        $maxRight = $this->getMaxRightValue();
        $sql = "SELECT * FROM albums AS a WHERE a.rgt = :maxValue AND a.lvl = 0;";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindParam(":maxValue", $maxRight, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result) {
            return new AlbumEntity($result);
        }

        return null;
    }

    public function getAlbumById($id)
    {
        $sql = "SELECT a.id, a.title, a.path, a.lft, a.rgt, a.lvl
                FROM albums AS a
                WHERE a.id = :id";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindParam(":id", $id, \PDO::PARAM_INT)   ;
        $stmt->execute();
        $result = $stmt->fetch();

        return new AlbumEntity($result);
    }
    public function save(AlbumEntity $album)
    {

        $sql = "UPDATE albums SET rgt = rgt + 2 WHERE rgt >= :leftPosition;
                UPDATE albums SET lft = lft + 2 WHERE lft >= :leftPosition;

                INSERT INTO albums(title, path, lft, rgt, lvl) 
                VALUES(:title, :path, :leftPosition, :leftPosition + 1, :levelPosition);";

        $this->getConnection()->beginTransaction();
        $this->getConnection()->exec("LOCK TABLES albums WRITE;");
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindParam(":title", $album->getTitle(), \PDO::PARAM_STR);
        $stmt->bindParam(":path", $album->getPath(), \PDO::PARAM_STR);
        $stmt->bindParam(":leftPosition", $album->getLft(), \PDO::PARAM_INT);
        $stmt->bindParam(":levelPosition", $album->getLvl(), \PDO::PARAM_INT);
        $result = $stmt->execute();
        $this->getConnection()->commit();
        $this->getConnection()->exec("UNLOCK TABLES;");

        if (!$result) {
            throw new \Exception("could not save record");
        }
    }

    private function getMaxRightValue()
    {
        $sql = "SELECT @maxRight := MAX(rgt) AS maxRight FROM albums";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['maxRight'];
    }
}