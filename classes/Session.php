<?php

namespace classes;

class Session
{
    public function set($name, $value): void
    {
        $_SESSION[$name] = $value;
    }

    public function setValues($assocArray): void
    {
        foreach ($assocArray as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function get($name): ?string
    {
        return $_SESSION[$name] ?? null;
    }

    public function remove($name): void
    {
        unset($_SESSION[$name]);
    }
}