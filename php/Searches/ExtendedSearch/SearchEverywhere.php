<?php

namespace App\Services\Datatables\Searches\ExtendedSearch;

use App\Services\Datatables\Constants;
use App\Services\Datatables\Searches\ExtendedSearch\WhereBuilder\WhereQuery;
use App\Services\Datatables\Searches\ExtendedSearch\WhereBuilder\WhereRawQuery;
use Illuminate\Database\Query\Builder;

class SearchEverywhere
{
	/** @var array */
	protected array $columns = [];

	/** @var WhereQuery */
	private WhereQuery $whereQuery;

	/** @var WhereRawQuery */
	private WhereRawQuery $whereRawQuery;

	public function __construct(array $columns, WhereQuery $whereQuery, WhereRawQuery $whereRawQuery)
	{
		$this->columns = $columns;
		$this->whereQuery = $whereQuery;
		$this->whereRawQuery = $whereRawQuery;
	}

	/**
	 * @param Builder $builder
	 * @param $operator
	 * @param $text
	 * @param string $andor
	 * @return Builder
	 */
	public function searchInAllColumns(Builder $builder, $operator, $text, $andor = Constants::AND_OPERATOR): Builder
	{
		if ($andor === Constants::AND_OPERATOR) {
			return $builder->where(function ($query) use ($operator, $text) {
				$this->whereInAllColumns($query, $operator, $text);
			});
		}

		return $builder->orWhere(function ($query) use ($operator, $text) {
			$this->whereInAllColumns($query, $operator, $text);
		});
	}

	/**
	 * @param Builder $query
	 * @param $operator
	 * @param $text
	 * @return bool
	 */
	private function whereInAllColumns(Builder $query, $operator, $text): bool
	{
		foreach ($this->columns as $column) {
			if (!isset($column['searchable']) || !$column['searchable']) {
				continue;
			}

			if (isset($column['filter']) && $column['filter'] === Constants::DATE_FILTER) {
				$this->whereRawQuery->add($query, 'DATE_FORMAT(' . $column['name'] . ', "%d.%m.%Y")', $operator, $text,
					Constants::OR_OPERATOR);
				continue;
			}

			if (isset($column['filter']) && $column['filter'] === Constants::DATE_TIME_FILTER) {
				$this->whereRawQuery->add($query, 'DATE_FORMAT(' . $column['name'] . ', "%d.%m.%Y %H:%i:%s")',
					$operator, $text, Constants::OR_OPERATOR);
				continue;
			}

			$this->whereQuery->add($query, $column['name'], $operator, $text, Constants::OR_OPERATOR);
		}

		return true;
	}
}