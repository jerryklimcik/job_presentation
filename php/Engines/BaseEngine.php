<?php

namespace App\Services\Datatables\Engines;

use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseEngine implements IDataTableEngine
{
	/** @var Request */
	protected Request $request;

	/** @var Builder */
	protected Builder $builder;

	/** @var array */
	protected array $columns;

	/** @var array */
	protected array $extraColumns = [];

	/** @var integer */
	protected int $totalRecords = 0;

	/** @var Closure */
	protected Closure $rowClass;

	/** @var bool */
	protected bool $customQueryBuilder = false;

	/**
	 * Work organization
	 *
	 * @return JsonResponse
	 */
	abstract public function make(): JsonResponse;

	/**
	 * JSON data for datatables
	 *
	 * @return array
	 */
	abstract public function render(): array;

	/**
	 * @return Builder
	 */
	public function getBuilder(): Builder
	{
		return $this->builder;
	}

	/**
	 * Set builder if view does not exists
	 *
	 * @param Builder $builder
	 * @return $this
	 */
	public function setBuilder(Builder $builder): self
	{
		$this->builder = $builder;
		$this->customQueryBuilder = true;

		return $this;
	}

	/**
	 * Set DT_RowClass for row class
	 *
	 * @param string|callable $callback
	 * @return $this
	 */
	public function setRowClass(string|callable $callback): self
	{
		$this->rowClass = $callback;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getExtraColumns(): array
	{
		return $this->extraColumns;
	}

	/**
	 * @param $name
	 * @param $content
	 * @param $columnInfo
	 * @return $this
	 */
	public function addColumn($name, $content, $columnInfo): self
	{
		$this->extraColumns[] = [
			'info' => $columnInfo,
			'callback' => [
				'name' => $name,
				'content' => $content,
			],
		];

		return $this;
	}

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
	): self {
		$this->addColumn('delete', function ($callback) use ($id_column, $checkColumn, $lockMessage, $checkPermission) {
			if ($checkPermission !== null && $checkPermission === false) {
				return '<a href="javascript:void(null);" class="deleteLock" data-balloon="' . __('general.You does not have permission for this action') . '" data-balloon-pos="right">
                        <i class="fa fa-lock" aria-hidden="true"></i>
                    </a>';
			}

			if ($checkColumn === '' || $callback->{$checkColumn}) {
				return '
                    <a href="javascript:void(null);" class="deleteModal" data-id="' . $callback->{$id_column} . ' ">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </a>';
			}

			return '<a href="javascript:void(null);" class="deleteLock" data-balloon="' . $lockMessage . '" data-balloon-pos="right">
                        <i class="fa fa-lock" aria-hidden="true"></i>
                    </a>';

		}, $extraColumns[array_search('delete', array_column($extraColumns, 'name'), true)]);

		return $this;
	}

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
	): self {
		$this->addColumn('edit',
			function ($callback) use ($id_column, $route, $checkColumn, $lockMessage, $checkPermission) {
				if ($checkPermission !== null && $checkPermission === false) {
					return '<a href="javascript:void(null);" class="deleteLock" data-balloon="' . __('general.You does not have permission for this action') . '" data-balloon-pos="right">
                        <i class="fa fa-lock" aria-hidden="true"></i>
                    </a>';
				}

				if ($checkColumn === '' || $callback->{$checkColumn}) {
					return '
                    <a href="' . route($route, $callback->{$id_column}) . '">
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                    </a>';
				}

				return '
                    <a href="javascript:void(null);" class="editLock" data-balloon-length="fit" data-balloon="' . $lockMessage . '" data-balloon-pos="right">
                        <i class="fa fa-lock" aria-hidden="true"></i>
                    </a>';

			}, $extraColumns[array_search('edit', array_column($extraColumns, 'name'), true)]);

		return $this;
	}

	/**
	 * @param array $columns
	 * @return $this
	 */
	public function setColumns(array $columns): self
	{
		$this->columns = $columns;
		return $this;
	}
}