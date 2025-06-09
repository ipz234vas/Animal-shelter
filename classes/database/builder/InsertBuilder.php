<?php

namespace classes\database\builder;

use classes\database\QueryState;

class InsertBuilder
{
    public function __construct(private QueryState $state)
    {
    }

    public function insert(array $data): static
    {
        $this->state->data = $data;
        return $this;
    }

    public function execute(): int
    {
        if (!$this->state->data) {
            return 0;
        }

        $cols = array_keys($this->state->data);
        $placeholders = array_map(fn($c) => ":$c", $cols);

        $sql = "INSERT INTO {$this->state->table} (" . implode(', ', $cols) . ') VALUES (' . implode(', ', $placeholders) . ')';
        $stmt = $this->state->pdo->prepare($sql);
        $stmt->execute($this->state->data);

        return (int)$this->state->pdo->lastInsertId();
    }
}
