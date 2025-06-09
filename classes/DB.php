<?php

namespace classes;

class DB
{
    public \PDO $pdo;

    public function __construct($host, $name, $login, $password)
    {
        $this->pdo = new \PDO("mysql:host={$host};dbname={$name}", $login, $password,);
    }
}