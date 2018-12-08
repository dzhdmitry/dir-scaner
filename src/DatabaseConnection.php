<?php

namespace Scanner;

class DatabaseConnection
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(string $host, string $port, string $database, string $user, ?string $password)
    {
        $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s;', $host, $port, $database);
        $this->pdo = new \PDO($dsn, $user, $password);

        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @param string $query
     * @return \PDOStatement
     */
    public function prepare($query)
    {
        return $this->pdo->prepare($query);
    }
}
