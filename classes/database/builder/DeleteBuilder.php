<?php

namespace classes\database\builder;

class DeleteBuilder extends BaseBuilder
{
    public function execute(): int
    {
        if (!$this->state->where) {
            return 0;
        }

        $sql = "DELETE FROM {$this->state->table}" . $this->buildWhereClause();
        $stmt = $this->state->pdo->prepare($sql);
        $stmt->execute($this->state->params);

        return $stmt->rowCount();
    }
}