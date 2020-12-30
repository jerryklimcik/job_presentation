<?php

namespace App\Services\Datatables\Searches\ExtendedSearch;

use App\Services\Datatables\Constants;
use App\Services\Datatables\Interfaces\ISearch;
use App\Services\Datatables\Searches\Interfaces\ISearchWrapper;
use App\Services\Datatables\Searches\SelectedRows\SelectedRows;
use Exception;
use Illuminate\Database\Query\Builder;

class ExtendedSearch implements ISearch
{
	/** @var ISearchWrapper */
	private ISearchWrapper $wrapper;

	/** @var SearchEverywhere */
	private SearchEverywhere $searchEverywhere;

	/** @var SearchInColumn */
	private SearchInColumn $searchInColumn;

	/** @var SelectedRows */
	private SelectedRows $selectedRows;

	public function __construct(
		ISearchWrapper $wrapper,
		SearchEverywhere $searchEverywhere,
		SearchInColumn $searchInColumn,
		SelectedRows $selectedRows
	) {
		$this->wrapper = $wrapper;
		$this->searchEverywhere = $searchEverywhere;
		$this->searchInColumn = $searchInColumn;
		$this->selectedRows = $selectedRows;
	}

	/**
	 * @return boolean
	 * @throws Exception
	 */
	public function searchData(): bool
	{
		try {
			$this->wrapper->getBuilder()->where(function ($query) {
				if (isset($this->wrapper->getSearch()->default->text) && trim($this->wrapper->getSearch()->default->text) !== '') {
					$this->searchInDefault($query);
				}

				if (isset($this->wrapper->getSearch()->filters) && count($this->wrapper->getSearch()->filters)) {
					$this->searchInAnotherFilters($query);
				}

				if (!empty($this->wrapper->getSearch()->selectedRows)) {
					$this->selectedRows->searchData();
				}
			});
		} catch (Exception $e) {
			throw new Exception('Error in extended searching: ' . $e);
		}

		return true;
	}

	/**
	 * @param $query
	 * @return Builder
	 */
	private function searchInDefault($query): Builder
	{
		if ($this->wrapper->getSearch()->default->column === Constants::ALL_COLUMNS) {
			return $this->searchEverywhere
				->searchInAllColumns($query,
					$this->wrapper->getSearch()->default->operator,
					$this->wrapper->getSearch()->default->text
				);
		}

		return $this->searchInColumn
			->where($query,
				$this->wrapper->getSearch()->default->column,
				$this->wrapper->getSearch()->default->operator,
				$this->wrapper->getSearch()->default->text
			);
	}

	/**
	 * @param $query
	 * @return bool
	 */
	private function searchInAnotherFilters($query): bool
	{
		foreach ($this->wrapper->getSearch()->filters as $filter) {
			if (trim($filter->text) === '') {
				continue;
			}

			if ($filter->column === Constants::ALL_COLUMNS) {
				$this->searchEverywhere->searchInAllColumns($query, $filter->operator, $filter->text, $filter->andor);
			} else {
				$this->searchInColumn->where($query, $filter->column, $filter->operator, $filter->text, $filter->andor);
			}
		}

		return true;
	}
}