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
        if (!$this->state->data || !$this->state->where) {
            return 0;
        }

        $setPairs = implode(', ', array_map(fn($f) => "$f = ?", array_keys($this->state->data)));

        $sql = "UPDATE {$this->state->table} SET $setPairs" . $this->buildWhereClause();

        $stmt = $this->state->pdo->prepare($sql);

        $params = array_values($this->state->data);
        if (!empty($this->state->params)) {
            $params = array_merge($params, array_values($this->state->params));
        }

        $stmt->execute($params);

        return $stmt->rowCount();
    }
}