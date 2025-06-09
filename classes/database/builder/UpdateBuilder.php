<?php

namespace classes\database\builder;

class UpdateBuilder extends BaseBuilder
{
    public function update(array $data): static
    {
        $this->state->data = $data;
        return $this;
    }

    public function execute(): int
    {
        if (!$this->state->data) {
            return 0;
        }
        if (!$this->state->where) {
            return 0;
        }

        $setPairs = implode(', ', array_map(fn($f) => "$f = :u_$f", array_keys($this->state->data)));
        $sql = "UPDATE {$this->state->table} SET $setPairs" . $this->buildWhereClause();

        $stmt = $this->state->pdo->prepare($sql);
        foreach ($this->state->data as $k => $v) {
            $stmt->bindValue(":u_$k", $v);
        }
        $stmt->execute($this->state->params);

        return $stmt->rowCount();
    }
}