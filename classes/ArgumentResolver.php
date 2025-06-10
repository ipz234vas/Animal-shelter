<?php

namespace classes;

use classes\exceptions\HttpException;
use ReflectionException;
use ReflectionMethod;

final class ArgumentResolver
{
    private ModelBinder $binder;

    public function __construct(ModelBinder $binder)
    {
        $this->binder = $binder;
    }

    /**
     * @throws HttpException
     * @throws ReflectionException
     */
    public function resolve(ReflectionMethod $method, array $src, ModelState $ms): array
    {
        $args = [];

        foreach ($method->getParameters() as $p) {
            $t = $p->getType();

            if ($t && !$t->isBuiltin()) {
                $args[] = $this->binder->bind($t->getName(), $src, $ms);
            } else {
                $name = $p->getName();
                if (array_key_exists($name, $src))
                    $args[] = $src[$name];
                elseif ($p->isDefaultValueAvailable())
                    $args[] = $p->getDefaultValue();
                elseif ($p->allowsNull())
                    $args[] = null;
                else throw new HttpException(400, "Missing parameter: $name");
            }
        }

        return $args;
    }
}

