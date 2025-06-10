<?php

namespace classes;

final class Router
{
    private string $defaultModule;
    private string $defaultAction;

    public function __construct(string $defaultModule, string $defaultAction)
    {
        $this->defaultModule = $defaultModule;
        $this->defaultAction = $defaultAction;
    }

    public function parse(?string $route): array
    {
        $route ??= "$this->defaultModule/$this->defaultAction";
        [$module, $action] = array_pad(explode('/', $route, 2), 2, null);
        return [
            $module ?: $this->defaultModule,
            $action ?: $this->defaultAction,
        ];
    }
}