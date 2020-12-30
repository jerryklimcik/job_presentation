<?php

namespace App\Services\Datatables\Engines;

use App\Services\Datatables\Helper;
use App\Services\Datatables\Processors\RenderProcessor;
use App\Services\Datatables\Searches\Interfaces\ISearchBuilder;
use App\Services\Datatables\Searches\SearchQueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryBuilderEngine extends BaseEngine
{
	/**
	 * Array with query results
	 *
	 * @var array
	 */
	private array $resultQuery;

	/**
	 * @param Request $request
	 * @param string|null $table
	 */
	public function __construct(Request $request, $table = null)
	{
		$this->builder = $table ? DB::table($table) : null;
		$this->request = $request;

		return $this;
	}

	/**
	 * Work organization
	 *
	 * @return JsonResponse
	 */
	public function make(): JsonResponse
	{
		$this->compositeQuery();
		return new JsonResponse($this->render());
	}

	/**
	 * Query builder
	 */
	private function compositeQuery(): void
	{
		$searchQueryBuilder = app(SearchQueryBuilder::class, [
			'columns' => $this->columns,
			'builder' => $this->builder,
		]);

		$this->search($searchQueryBuilder)
			->order()
			->limit()
			->fetchData();

		$this->totalRecords = $this->totalCount();
	}

	/**
	 * @return integer
	 */
	private function totalCount(): int
	{
		return $this->totalRecords ?: $this->count();
	}

	/**
	 * Database row count
	 *
	 * @return integer
	 */
	private function count(): int
	{
		$this->totalRecords = DB::select('SELECT FOUND_ROWS() AS result_number')[0]->result_number;
		return $this->totalRecords;
	}

	/**
	 * @param ISearchBuilder $searchQueryBuilder
	 * @return $this
	 */
	private function search(ISearchBuilder $searchQueryBuilder): self
	{
		if ($this->request->input('data.search.value') !== '') {
			$searchQueryBuilder->setSearch(json_decode($this->request->input('data.search.value')));
			$searchQueryBuilder->searchType()->searchData();
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	private function order(): self
	{
		if ($this->request->has('data.order')) {
			Helper::arraySortByColumn($this->columns, 'sequence');

			if ($primaryColumn = Helper::getPrimaryColumn($this->columns)) {
				$this->builder->orderBy($primaryColumn, 'asc');
			}
		}
		return $this;
	}

	/**
	 * @return $this
	 */
	private function limit(): self
	{
		if ($this->request->has('data.length') && $this->request->input('data.length') !== '-1') {
			$this->builder->skip($this->request->input('data.start'))
				->take((int)$this->request->input('data.length') > 0
					? $this->request->input('data.length')
					: 30);
		}

		return $this;
	}

	/**
	 * Query composition and fetch result
	 */
	private function fetchData(): self
	{
		// custom query builder is responsible for right column names
		if ($this->customQueryBuilder) {
			$this->resultQuery = $this->builder->get();
			return $this;
		}

		$this->resultQuery = $this->builder
			->select(DB::raw('SQL_CALC_FOUND_ROWS ' . implode(',', Helper::getColumnsParam(
					$this->columns, 'name'))))
			->get();

		return $this;
	}

	/**
	 * JSON data for datatables
	 *
	 * @return array
	 */
	public function render(): array
	{
		$renderProcessor = app(RenderProcessor::class, [
			'result' => $this->resultQuery,
			'extraColumns' => $this->extraColumns,
			'columns' => $this->columns,
			'rowClass' => $this->rowClass,
			'totalCount' => $this->totalCount(),
		]);

		return $renderProcessor->render();
	}
}