<?php

namespace App\Services\Datatables\Searches\ExtendedSearch\WhereBuilder;

use Illuminate\Database\Query\Builder;

abstract class WhereBuilder
{
	/**
	 * @var Builder
	 */
	protected Builder $builder;

	protected $column;
	protected $operator;
	protected $search_text;
	protected $andor;

	/**
	 * @param Builder $builder
	 * @param $column
	 * @param $operator
	 * @param $search_text
	 * @param $andor
	 * @return Builder
	 */
	public function add(Builder $builder, $column, $operator, $search_text, $andor): Builder
	{
		$this->builder = $builder;
		$this->column = $column;
		$this->operator = $operator;
		$this->search_text = $search_text;
		$this->andor = $andor;

		return $this->operatorDecider();
	}

	/**
	 * @return Builder
	 */
	protected function operatorDecider(): Builder
	{
		switch ($this->operator) {
			case 'like':
				return $this->likeOperator();

			case 'not_like':
				return $this->notLikeOperator();

			case 'in':
				return $this->inOperator();

			case 'is_null':
				return $this->nullOperator();

			case 'is_not_null':
				return $this->notNullOperator();

			default:
				return $this->defaultOperator();
		}
	}

	/**
	 * @return Builder
	 */
	abstract protected function likeOperator(): Builder;

	/**
	 * @return Builder
	 */
	abstract protected function notLikeOperator(): Builder;

	/**
	 * @return Builder
	 */
	abstract protected function nullOperator(): Builder;

	/**
	 * @return Builder
	 */
	abstract protected function notNullOperator(): Builder;

	/**
	 * @return Builder
	 */
	abstract protected function defaultOperator(): Builder;
}