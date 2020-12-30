<?php

namespace App\Services\Datatables\Searches\ExtendedSearch;

use App\Services\Datatables\Constants;
use App\Services\Datatables\Searches\ExtendedSearch\WhereBuilder\WhereQuery;
use Illuminate\Database\Query\Builder;

class SearchInColumn
{
	/** @var WhereQuery */
	private WhereQuery $whereQuery;

	public function __construct(WhereQuery $whereQuery)
	{
		$this->whereQuery = $whereQuery;
	}

	/**
	 * @param Builder $builder
	 * @param $column
	 * @param $operator
	 * @param $search_text
	 * @param string $andor
	 * @return Builder
	 */
	public function where(Builder $builder, $column, $operator, $search_text, $andor = Constants::AND_OPERATOR): Builder
	{
		if ($andor === Constants::AND_OPERATOR) {
			return $builder->where(function ($query) use ($column, $operator, $search_text, $andor) {
				$this->whereQuery->add($query, $column, $operator, $search_text, $andor);
			});
		}

		return $builder->orWhere(function ($query) use ($column, $operator, $search_text, $andor) {
			$this->whereQuery->add($query, $column, $operator, $search_text, $andor);
		});
	}
}