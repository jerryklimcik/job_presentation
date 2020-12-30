<?php

namespace App\Services\Datatables\Searches;

use App\Services\Datatables\Searches\Interfaces\ISearchWrapper;
use Illuminate\Database\Query\Builder;
use stdClass;

class SearchWrapper implements ISearchWrapper
{
    /**@var array */
    private $columns;

    /** @var \Illuminate\Database\Query\Builder */
    private $builder;

    /** @var \stdClass */
    private $search;

    public function __construct(array $columns, Builder $builder)
    {
        $this->columns = $columns;
        $this->builder = $builder;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

	/**
	 * @param array $columns
	 */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

	/**
	 * @param \Illuminate\Database\Query\Builder $builder
	 */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return \stdClass
     */
    public function getSearch()
    {
        return $this->search;
    }

	/**
	 * @param stdClass $search
	 */
    public function setSearch(stdClass $search)
    {
        $this->search = $search;
    }

}