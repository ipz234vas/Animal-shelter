<?php

namespace classes\database;

class QueryState
{
    public function __construct(
        public string $table,
        public ?\PDO  $pdo = null,
        public array  $columns = ['*'],
        public array  $joins = [],
        public array  $where = [],
        public array  $params = [],
        public array  $orderBy = [],
        public ?int   $limit = null,
        public ?int   $offset = null,
        public array  $data = [],
        public array  $groupBy = []
    )
    {
    }
}