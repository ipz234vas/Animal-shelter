<?php

namespace classes\database\builder;

use classes\database\QueryState;
use classes\database\SQLOperator;

abstract class BaseBuilder
{
    public function __construct(protected QueryState $state)
    {
    }

    public function where(string $column, SQLOperator $op, mixed $value): static
    {
        return $this->addCondition('AND', "$column {$op->value} ?", [$value]);
    }

    public function orWhere(string $column, SQLOperator $op, mixed $value): static
    {
        return $this->addCondition('OR', "$column {$op->value} ?", [$value]);
    }

    public function orWhereGroup(callable $group): static
    {
        return $this->addGroup('OR', $group);
    }

    public function andWhereGroup(callable $group): static
    {
        return $this->addGroup('AND', $group);
    }

    public function whereIn(string $column, array $values): static
    {
        return $this->whereInList($column, $values, false);
    }

    public function whereNotIn(string $column, array $values): static
    {
        return $this->whereInList($column, $values, true);
    }

    private function whereInList(string $column, array $values, bool $negate = false): static
    {
        if (empty($values)) {
            return $this;
        }

        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $sql = $column . ($negate ? " NOT IN ($placeholders)" : " IN ($placeholders)");
        return $this->addCondition('AND', $sql, $values);
    }

    public function whereBetween(string $column, mixed $min, mixed $max): static
    {
        return $this->addCondition('AND', "$column BETWEEN ? AND ?", [$min, $max]);
    }

    public function whereNull(string $column): static
    {
        return $this->addCondition('AND', "$column IS NULL");
    }

    public function whereNotNull(string $column): static
    {
        return $this->addCondition('AND', "$column IS NOT NULL");
    }

    protected function addCondition(string $boolean, string $expr, array $params = []): static
    {
        $this->state->where[] = ['boolOp' => $boolean, 'expr' => $expr, 'params' => $params];
        $this->state->params = array_merge($this->state->params, $params);
        return $this;
    }

    protected function addGroup(string $outerBool, callable $group): static
    {
        $subState = new QueryState($this->state->table);
        $subBuilder = new static($subState);
        $group($subBuilder);

        if ($subState->where === []) {
            return $this;
        }

        $expr = '(' . $subBuilder->buildWhereClause(false) . ')';
        $this->state->where[] = ['boolOp' => $outerBool, 'expr' => $expr, 'params' => $subState->params];
        $this->state->params = array_merge($this->state->params, $subState->params);
        return $this;
    }

    protected function buildWhereClause(bool $withKeyword = true): string
    {
        if ($this->state->where === []) {
            return '';
        }

        $pieces = [];
        foreach ($this->state->where as $index => ['boolOp' => $bool, 'expr' => $expr]) {
            $prefix = $index === 0 ? '' : " $bool ";
            $pieces[] = $prefix . $expr;
        }

        $clause = implode('', $pieces);
        return $withKeyword ? ' WHERE ' . $clause : $clause;
    }
}