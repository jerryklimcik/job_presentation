<?php

namespace App\Services\Datatables\Searches\SimpleSearch;

use App\Services\Datatables\Interfaces\ISearch;
use App\Services\Datatables\Interfaces\IWhereSearch;
use App\Services\Datatables\Searches\Interfaces\ISearchWrapper;
use Exception;

class SimpleSearch implements ISearch
{
	/** @var ISearchWrapper */
	private ISearchWrapper $wrapper;

	/** @var IWhereSearch */
	private IWhereSearch $whereSearch;

	public function __construct(ISearchWrapper $wrapper, IWhereSearch $whereSearch)
	{
		$this->wrapper = $wrapper;
		$this->whereSearch = $whereSearch;
	}

	/**
	 * @return boolean
	 * @throws Exception
	 */
	public function searchData(): bool
	{
		try {
			$this->wrapper->getBuilder()->where(function ($query) {
				return $this->whereSearch->search($this->wrapper, $query);
			});

			return true;

		} catch (Exception $e) {
			throw new Exception('Error in searching: ' . $e);
		}
	}
}