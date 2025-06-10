<?php

namespace classes\database\builder;

use enums\database\SQLOrderDirection;

class SelectBuilder extends BaseBuilder
{
    public function select(array $columns = ["*"]): static
    {
        $this->state->columns = $columns;
        return $this;
    }

    public
    function join(string $table, string $on, string $type = 'INNER'): static
    {
        $this->state->joins[] = "$type JOIN $table ON $on";
        return $this;
    }

    public
    function orderBy(string $expression, SQLOrderDirection $direction = SQLOrderDirection::Ascending): static
    {
        $this->state->orderBy[] = "$expression {$direction->value}";
        return $this;
    }

    public
    function limit(int $limit): static
    {
        $this->state->limit = $limit;
        return $this;
    }

    public
    function offset(int $offset): static
    {
        $this->state->offset = $offset;
        return $this;
    }

    public
    function fetch(): array
    {
        $stmt = $this->prepare();
        $stmt->execute($this->state->params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public
    function first(): ?array
    {
        $this->limit(1);
        return $this->fetch()[0] ?? null;
    }

    public
    function count(): int
    {
        $backup = $this->state->columns;
        $this->state->columns = ['COUNT(*) AS cnt'];

        $stmt = $this->prepare();
        $stmt->execute($this->state->params);
        $cnt = (int)$stmt->fetchColumn();

        $this->state->columns = $backup;
        return $cnt;
    }

    private
    function prepare(): \PDOStatement
    {
        $sql = $this->buildSQL();
        return $this->state->pdo->prepare($sql);
    }

    private
    function buildSQL(): string
    {
        $sql = 'SELECT ' . implode(', ', $this->state->columns) . ' FROM ' . $this->state->table;

        if ($this->state->joins) {
            $sql .= ' ' . implode(' ', $this->state->joins);
        }

        $sql .= $this->buildWhereClause();

        if ($this->state->orderBy) {
            $sql .= ' ORDER BY ' . implode(', ', $this->state->orderBy);
        }

        if ($this->state->limit !== null) {
            $sql .= ' LIMIT ' . $this->state->limit;
            if ($this->state->offset !== null) {
                $sql .= ' OFFSET ' . $this->state->offset;
            }
        }

        return $sql;
    }
}