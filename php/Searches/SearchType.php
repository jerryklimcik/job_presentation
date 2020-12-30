<?php

namespace App\Services\Datatables\Searches;

use App\Services\Datatables\Constants;
use App\Services\Datatables\Interfaces\ISearch;
use App\Services\Datatables\Searches\ExtendedSearch\ExtendedSearch;
use App\Services\Datatables\Searches\ExtendedSearch\SearchEverywhere as ExtendedSearchEverywhere;
use App\Services\Datatables\Searches\ExtendedSearch\SearchInColumn as ExtendedSearchInColumn;
use App\Services\Datatables\Searches\ExtendedSearch\WhereBuilder\WhereQuery;
use App\Services\Datatables\Searches\Interfaces\ISearchWrapper;
use App\Services\Datatables\Searches\SelectedRows\SelectedRows;
use App\Services\Datatables\Searches\SimpleSearch\SearchEverywhere;
use App\Services\Datatables\Searches\SimpleSearch\SearchInColumn;
use App\Services\Datatables\Searches\SimpleSearch\SimpleSearch;
use InvalidArgumentException;

class SearchType
{
	/** @var ISearchWrapper */
	private ISearchWrapper $wrapper;

	public function __construct(ISearchWrapper $wrapper)
	{
		$this->wrapper = $wrapper;
	}

	/**
	 * @return ISearchWrapper
	 */
	public function getWrapper(): ISearchWrapper
	{
		return $this->wrapper;
	}

	/**
	 * @param $type
	 * @return ISearch|SelectedRows|SimpleSearch|ExtendedSearch
	 */
	public function pickSearch($type): ISearch|SelectedRows|SimpleSearch|ExtendedSearch
	{
		switch ($type) {
			case Constants::SIMPLE_SEARCH:
				$whereSearch = $this->wrapper->getSearch()->selectedColumn !== Constants::ALL_COLUMNS ? new SearchInColumn() : new SearchEverywhere();
				return new SimpleSearch($this->wrapper, $whereSearch);

			case Constants::EXTENDED_SEARCH:
				$whereQuery = new WhereQuery();
				return new ExtendedSearch(
					$this->wrapper,
					app(ExtendedSearchEverywhere::class, ['columns' => $this->wrapper->getColumns()]),
					new ExtendedSearchInColumn($whereQuery),
					new SelectedRows($this->wrapper, $whereQuery)
				);

			case Constants::SELECTED_ROWS:
				return new SelectedRows($this->wrapper, new WhereQuery());

			default:
				throw new InvalidArgumentException();
		}
	}
}