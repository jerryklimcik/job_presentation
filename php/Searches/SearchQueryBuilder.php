<?php

namespace App\Services\Datatables\Searches;

use App\Services\Datatables\Constants;
use App\Services\Datatables\Interfaces\ISearch;
use App\Services\Datatables\Searches\Interfaces\ISearchBuilder;
use stdClass;

class SearchQueryBuilder implements ISearchBuilder
{
	/** @var SearchType */
	private SearchType $searchType;

	public function __construct(SearchType $searchType)
	{
		$this->searchType = $searchType;
	}

	/**
	 * @return ISearch
	 */
	public function searchType(): ISearch
	{
		return $this->searchType->pickSearch($this->searchType->getWrapper()->getSearch()->type ?? Constants::SELECTED_ROWS);
	}

	/**
	 * @param stdClass $search
	 * @return void
	 */
	public function setSearch(stdClass $search): void
	{
		$this->searchType->getWrapper()->setSearch($search);
	}
}