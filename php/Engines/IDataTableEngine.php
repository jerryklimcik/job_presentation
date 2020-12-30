<?php

namespace App\Services\Datatables\Engines;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;

interface IDataTableEngine
{

	/**
	 * Column settings
	 *
	 * @param array $columns
	 * @return $this
	 */
	public function setColumns(array $columns): self;

	/**
	 * Work organization
	 *
	 * @return JsonResponse
	 */
	public function make(): JsonResponse;

	/**
	 * JSON data for datatables
	 *
	 * @return array
	 */
	public function render(): array;

	/**
	 * @param $name
	 * @param $content
	 * @param $columnInfo
	 * @return $this
	 */
	public function addColumn($name, $content, $columnInfo): self;

	/**
	 * @param array $extraColumns
	 * @param string $id_column
	 * @param string $checkColumn
	 * @param string $lockMessage
	 * @param bool|null $checkPermission
	 * @return $this
	 */
	public function addDeleteColumn(
		array $extraColumns,
		string $id_column,
		string $checkColumn = '',
		string $lockMessage = '',
		bool|null $checkPermission = null
	): self;

	/**
	 * @param array $extraColumns
	 * @param string $route
	 * @param string $id_column
	 * @param string $checkColumn
	 * @param string $lockMessage
	 * @param bool|null $checkPermission
	 * @return $this
	 */
	public function addEditColumn(
		array $extraColumns,
		string $route,
		string $id_column,
		string $checkColumn = '',
		string $lockMessage = '',
		bool|null $checkPermission = null
	): self;

	/**
	 * Set row class by callback
	 *
	 * @param string|callable $callback
	 * @return $this
	 */
	public function setRowClass(string|callable $callback): self;

	/**
	 * Set builder if view does not exists
	 *
	 * @param Builder $builder
	 * @return $this
	 */
	public function setBuilder(Builder $builder): self;
}