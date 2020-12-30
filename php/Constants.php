<?php

namespace App\Services\Datatables;

class Constants
{
    public const SIMPLE_SEARCH = 'simpleSearch';
    public const EXTENDED_SEARCH = 'extendedSearch';
    public const SELECTED_ROWS = 'selectedRows';

    public const ALL_COLUMNS = 'all';

    // operators
    public const AND_OPERATOR = 'and';
    public const OR_OPERATOR = 'or';
    public const LIKE_OPERATOR = 'like';
    public const NOT_LIKE_OPERATOR = 'not_like';
    public const IN_OPERATOR = 'in';
    public const IS_NULL_OPERATOR = 'is_null';
    public const IS_NOT_NULL_OPERATOR = 'is_not_null';

    // filters
    public const DATE_FILTER = 'DateFilter';
    public const DATE_TIME_FILTER = 'DateTimeFilter';
    public const MONEY_FILTER = 'MoneyFilter';
    public const BOOLEAN_FILTER = 'BooleanFilter';
    public const TEXT_FILTER = 'TextFilter';
    public const CUSTOM_FILTER = 'CustomFilter';
    public const NUMBER_FILTER = 'NumberFilter';
    
    // other type formats
    public const EMAIL_TYPE = 'email';
    public const NOTE_TYPE = 'note';
}