<?php

namespace Core\Model;

class Connection extends \PDO
{
    protected $user = "root";
    protected $password = "root";
    protected $dsn = "mysql:dbname=gallery;host=localhost";

    /**
     * Connection constructor.
     * @param string $dsn
     * @param string $user
     * @param string $password
     */
    public function __construct($dsn = "", $user = "", $password = "")
    {
        parent::__construct($dsn, $user, $password);
    }

}