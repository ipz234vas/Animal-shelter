<?php

namespace models;

use classes\Core;
use classes\database\builder\TableBuilder;
use classes\database\SQLOperator;

class Model
{
    protected array $fieldsArray;
    protected static string $primaryKey = "id";
    protected static string $table = '';

    public function __construct()
    {
        $this->fieldsArray = [];
    }

    public function __set(string $name, $value)
    {
        $this->fieldsArray[$name] = $value;
    }

    public function __get(string $name)
    {
        return $this->fieldsArray[$name] ?? null;
    }

    public static function deleteById(int $id): bool
    {
        $deletedRows = self::asQuery()->delete()->where(static::$primaryKey, SQLOperator::Equal, $id)->execute();
        return !!$deletedRows;
    }

    public static function getById(int $id): ?array
    {
        return self::asQuery()->select()->where(static::$primaryKey, SQLOperator::Equal, $id)->first();
    }

    public static function getAll($columns = ["*"]): array
    {
        return self::asQuery()->select($columns)->fetch();
    }

    public function save(): bool
    {
        $value = $this->{static::$primaryKey};
        if (empty($value)) {
            $updatedRows = self::asQuery()->insert($this->fieldsArray)->execute();
        } else {
            $updatedRows =
                self::asQuery()
                    ->update($this->fieldsArray)
                    ->where(static::$primaryKey, SQLOperator::Equal, $value)
                    ->execute();
        }
        return !!$updatedRows;
    }

    public static function asQuery(): TableBuilder
    {
        return Core::getInstance()->db->query(static::$table);
    }
}