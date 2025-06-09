<?php

namespace classes\database\builder;

use classes\database\QueryState;

class TableBuilder
{
    public function __construct(private readonly \PDO $pdo, private readonly string $table)
    {
    }

    public function select(array $columns = ["*"]): SelectBuilder
    {
        $builder = new SelectBuilder(new QueryState($this->table, pdo: $this->pdo));
        return $builder->select($columns);
    }

    public function update(array $data): UpdateBuilder
    {
        $builder = new UpdateBuilder(new QueryState($this->table, pdo: $this->pdo));
        return $builder->update($data);
    }

    public function delete(): DeleteBuilder
    {
        return new DeleteBuilder(new QueryState($this->table, pdo: $this->pdo));
    }

    public function insert(array $data): InsertBuilder
    {
        $builder = new InsertBuilder(new QueryState($this->table, pdo: $this->pdo));
        return $builder->insert($data);
    }
}