<?php

namespace enums\database;

enum SQLOperator: string
{
    case Equal = '=';
    case NotEqual = '<>';
    case Greater = '>';
    case GreaterEqual = '>=';
    case Less = '<';
    case LessEqual = '<=';
    case Like = 'LIKE';
    case NotLike = 'NOT LIKE';
}
