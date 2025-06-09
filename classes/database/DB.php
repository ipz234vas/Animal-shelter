<?php

namespace classes\database;

use classes\database\builder\TableBuilder;

class DB
{
    public \PDO $pdo;

    public function __construct($host, $name, $login, $password)
    {
        $this->pdo = new \PDO("mysql:host={$host};dbname={$name}", $login, $password,);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function query(string $table): TableBuilder
    {
        return new TableBuilder($this->pdo, $table);
    }
}