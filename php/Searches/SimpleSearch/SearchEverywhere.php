<?php

namespace App\Services\Datatables\Searches\SimpleSearch;

use App\Services\Datatables\Constants;
use App\Services\Datatables\Interfaces\IWhereSearch;
use App\Services\Datatables\Searches\Interfaces\ISearchWrapper;
use Illuminate\Database\Query\Builder;

class SearchEverywhere implements IWhereSearch
{
	/** @var ISearchWrapper */
	private ISearchWrapper $wrapper;

	/**
	 * @param ISearchWrapper $wrapper
	 * @param Builder $query
	 * @return bool
	 */
	public function search(ISearchWrapper $wrapper, Builder $query): bool
	{
		$this->wrapper = $wrapper;
		$query1 = $query;

		if (isset($this->wrapper->getSearch()->searchInput1, $this->wrapper->getSearch()->searchInput2) &&
			$this->wrapper->getSearch()->searchInput1 !== '' &&
			$this->wrapper->getSearch()->searchInput2 !== ''
		) {
			$this->searchInAllColumnsByKeyword($this->wrapper->getSearch()->searchInput1, $query1);
			$this->searchInAllColumnsByKeyword($this->wrapper->getSearch()->searchInput2, $query1,
				$this->wrapper->getSearch()->operator);
			return true;
		}


		if (isset($this->wrapper->getSearch()->searchInput1) && $this->wrapper->getSearch()->searchInput1 !== '') {
			$this->searchInAllColumnsByKeyword($this->wrapper->getSearch()->searchInput1, $query1);
			return true;
		}

		if (isset($this->wrapper->getSearch()->searchInput2) && $this->wrapper->getSearch()->searchInput2 !== '') {
			$this->searchInAllColumnsByKeyword($this->wrapper->getSearch()->searchInput2, $query1);
			return true;
		}

		return false;
	}

	/**
	 * @param $keyword
	 * @param Builder $query
	 * @param string $andor
	 */
	private function searchInAllColumnsByKeyword($keyword, Builder $query, $andor = Constants::AND_OPERATOR): void
	{
		$columns = $this->wrapper->getColumns();
		if ($andor === Constants::AND_OPERATOR) {
			$query->where(function ($subquery) use ($keyword, $columns) {
				$this->loopColumns($subquery, $keyword, $columns);
			});
		} else {
			$query->orWhere(function ($subquery) use ($keyword, $columns) {
				$this->loopColumns($subquery, $keyword, $columns);
			});
		}
	}

	/**
	 * @param $subquery
	 * @param $keyword
	 * @param array $columns
	 */
	private function loopColumns($subquery, $keyword, array $columns): void
	{
		foreach ($columns as $column) {
			if (isset($column['searchable']) && $column['searchable']) {
				if ($column['filter'] === 'DateFilter' || $column['filter'] === 'DateTimeFilter') {
					$subquery->orWhereRaw('DATE_FORMAT(' . $column['name'] . ', "%d.%m.%Y %H:%i:%s") like "%' . $keyword . '%"');
					continue;
				}
				$subquery->orWhere($column['name'], 'like', '%' . $keyword . '%');
			}
		}
	}
}