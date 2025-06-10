<?php

namespace classes;

use classes\exceptions\HttpException;
use ReflectionMethod;

final class ArgumentResolver
{
    /**
     * @throws HttpException
     */
    public function resolve(ReflectionMethod $method, array $source): array
    {
        $args = [];

        foreach ($method->getParameters() as $param) {
            $name = $param->getName();

            if (array_key_exists($name, $source)) {
                $args[] = $source[$name];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } elseif ($param->allowsNull()) {
                $args[] = null;
            } else {
                throw new HttpException(400, "Missing parameter: {$name}");
            }
        }

        return $args;
    }
}
