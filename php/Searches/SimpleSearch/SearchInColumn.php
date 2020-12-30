<?php

namespace App\Services\Datatables\Searches\SimpleSearch;

use App\Services\Datatables\Constants;
use App\Services\Datatables\Helper;
use App\Services\Datatables\Interfaces\IWhereSearch;
use App\Services\Datatables\Searches\Interfaces\ISearchWrapper;
use Illuminate\Database\Query\Builder;

class SearchInColumn implements IWhereSearch
{
	/** @var ISearchWrapper */
	private ISearchWrapper $wrapper;

	/** @var Builder */
	private Builder $query;

	/**
	 * @param ISearchWrapper $wrapper
	 * @param Builder $query
	 * @return bool
	 */
	public function search(ISearchWrapper $wrapper, Builder $query): bool
	{
		$this->wrapper = $wrapper;
		$this->query = $query;

		if (Helper::isDateColumn($this->wrapper->getColumns(), $this->wrapper->getSearch()->selectedColumn)) {
			return $this->inDateColumn();
		}

		return $this->inColumn();
	}

	/**
	 * @return bool
	 */
	private function inDateColumn(): bool
	{
		if (isset($this->wrapper->getSearch()->searchInput1, $this->wrapper->getSearch()->searchInput2) &&
			$this->wrapper->getSearch()->searchInput1 !== '' &&
			$this->wrapper->getSearch()->searchInput2 !== ''
		) {
			$this->query->whereRaw('DATE_FORMAT(' . $this->wrapper->getSearch()->selectedColumn . ', "%d.%m.%Y %H:%i:%s") like "%' . $this->wrapper->getSearch()->searchInput1 . '%"');

			if ($this->wrapper->getSearch()->operator === Constants::AND_OPERATOR) {
				$this->query->whereRaw('DATE_FORMAT(' . $this->wrapper->getSearch()->selectedColumn . ', "%d.%m.%Y %H:%i:%s") like "%' . $this->wrapper->getSearch()->searchInput2 . '%"');
			} else {
				$this->query->orWhereRaw('DATE_FORMAT(' . $this->wrapper->getSearch()->selectedColumn . ', "%d.%m.%Y %H:%i:%s") like "%' . $this->wrapper->getSearch()->searchInput2 . '%"');
			}
			return true;
		}

		if (isset($this->wrapper->getSearch()->searchInput1) && $this->wrapper->getSearch()->searchInput1 !== '') {
			$this->query->whereRaw('DATE_FORMAT(' . $this->wrapper->getSearch()->selectedColumn . ', "%d.%m.%Y %H:%i:%s") like "%' . $this->wrapper->getSearch()->searchInput1 . '%"');
			return true;
		}

		if (isset($this->wrapper->getSearch()->searchInput2) && $this->wrapper->getSearch()->searchInput2 !== '') {
			$this->query->whereRaw('DATE_FORMAT(' . $this->wrapper->getSearch()->selectedColumn . ', "%d.%m.%Y %H:%i:%s") like "%' . $this->wrapper->getSearch()->searchInput2 . '%"');
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	private function inColumn(): bool
	{
		if (isset($this->wrapper->getSearch()->searchInput1, $this->wrapper->getSearch()->searchInput2) &&
			$this->wrapper->getSearch()->searchInput1 !== '' &&
			$this->wrapper->getSearch()->searchInput2 !== ''
		) {
			$this->query->where($this->wrapper->getSearch()->selectedColumn, 'like',
				'%' . $this->wrapper->getSearch()->searchInput1 . '%');

			if ($this->wrapper->getSearch()->operator === Constants::AND_OPERATOR) {
				$this->query->where($this->wrapper->getSearch()->selectedColumn, 'like',
					'%' . $this->wrapper->getSearch()->searchInput2 . '%');
			} else {
				$this->query->orWhere($this->wrapper->getSearch()->selectedColumn, 'like',
					'%' . $this->wrapper->getSearch()->searchInput2 . '%');
			}
			return true;
		}

		if (isset($this->wrapper->getSearch()->searchInput1) && $this->wrapper->getSearch()->searchInput1 !== '') {
			$this->query->where($this->wrapper->getSearch()->selectedColumn, 'like',
				'%' . $this->wrapper->getSearch()->searchInput1 . '%');
			return true;
		}

		if (isset($this->wrapper->getSearch()->searchInput2) && $this->wrapper->getSearch()->searchInput2 !== '') {
			$this->query->where($this->wrapper->getSearch()->selectedColumn, 'like',
				'%' . $this->wrapper->getSearch()->searchInput2 . '%');
			return true;
		}

		return false;
	}
}