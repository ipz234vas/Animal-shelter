<?php

namespace classes;

class ModelState
{
    private array $errors = [];

    public function add(string $path, string $message): void
    {
        $this->errors[$path][] = $message;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function all(): array
    {
        return $this->errors;
    }

    public function get(string $path): array
    {
        return $this->errors[$path] ?? [];
    }

    public function first(string $path): ?string
    {
        return $this->errors[$path][0] ?? null;
    }
}
