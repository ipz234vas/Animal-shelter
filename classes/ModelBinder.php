<?php

namespace classes;

namespace classes;

use attributes\binding\Collection;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;

class ModelBinder
{
    /**
     * @throws ReflectionException
     */
    public function bind(string $class, array $src, ModelState $ms, string $path = ''): object
    {
        $obj = new $class();
        $ref = new ReflectionClass($class);

        foreach ($ref->getProperties() as $prop) {
            $propName = $prop->getName();
            $valExists = array_key_exists($propName, $src);

            $propType = $prop->getType();
            $propPath = $path ? "$path.$propName" : $propName;

            if (!$valExists) {
                Validator::check($ms, $propPath, null, $prop);
                continue;
            }

            $raw = $src[$propName];

            if ($propType instanceof ReflectionNamedType && !$propType->isBuiltin()) {
                $child = $this->bind($propType->getName(), (array)$raw, $ms, $propPath);
                $prop->setValue($obj, $child);

            } elseif ($this->isCollection($prop)) {
                $itemType = $prop->getAttributes(Collection::class)[0]->newInstance()->itemType;
                $arr = [];
                foreach ((array)$raw as $idx => $childRaw) {
                    $arr[] = $this->bind($itemType, (array)$childRaw, $ms, "$propPath[$idx]");
                }
                $prop->setValue($obj, $arr);

            } else {
                $prop->setValue($obj, $raw);
            }

            Validator::check($ms, $propPath, $prop->getValue($obj), $prop);
        }

        return $obj;
    }

    private function isCollection(ReflectionProperty $prop): bool
    {
        return !empty($prop->getAttributes(Collection::class));
    }
}
