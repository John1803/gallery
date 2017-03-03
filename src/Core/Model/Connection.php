<?php

namespace Core\Model;

class Connection extends \PDO
{
    protected $user = "root";
    protected $password = "root";
    protected $dsn = "mysql:dbname=stoaj;host=localhost";

    /**
     * Connection constructor.
     * @param string $dsn
     * @param string $user
     * @param string $password
     */
    public function __construct($dsn = "", $user = "", $password = "")
    {
        parent::__construct($this->dsn, $this->user, $this->password, [\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]);

    }

}