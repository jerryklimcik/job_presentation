<?php

namespace App\Services\Datatables\Searches\Interfaces;

use Illuminate\Database\Query\Builder;
use stdClass;

interface ISearchWrapper
{
	/* @return array */
	public function getColumns(): array;

	/**
	 * @param array $columns
	 */
	public function setColumns(array $columns);

	/** @return Builder
	 */
	public function getBuilder(): Builder;

	/**
	 * @param Builder $builder
	 */
	public function setBuilder(Builder $builder);

	/** @return stdClass */
	public function getSearch(): stdClass;

	/**
	 * @param stdClass $search
	 */
	public function setSearch(stdClass $search);
}