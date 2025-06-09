<?php

namespace classes;

class Config
{
    public static ?Config $instance;
    private array $params;

    private function __construct()
    {
        /** @var array $Config */
        $directory = 'config';
        $config_files = scandir($directory);
        foreach ($config_files as $config_file) {
            if (str_ends_with($config_file, '.php')) {
                $path = $directory . '/' . $config_file;
                include_once $path;
            }
        }
        $this->params = [];
        foreach ($Config as $config) {
            foreach ($config as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public static function getInstance(): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __set(string $name, $value): void
    {
        $this->params[$name] = $value;
    }

    public function __get(string $name)
    {
        return $this->params[$name] ?? null;
    }
}