<?php

namespace Core\Model;

abstract class AbstractModel extends Connection
{
    /**
     * @var Connection
     */
    protected $connection;

    public function getConnection()
    {
        if (!isset($this->connection)) {
            return $this->connection = new Connection();
        }

        return $this->connection;
    }
}