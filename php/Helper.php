<?php

namespace App\Services\Datatables;

use Illuminate\Contracts\Support\Arrayable;

class Helper
{
	/**
	 * Convert array with objects to associative array
	 *
	 * @param mixed $row
	 * @return array
	 */
	public static function convertToArray(mixed $row): array
	{
		$data = $row instanceof Arrayable ? $row->toArray() : (array)$row;
		foreach (array_keys($data) as $key) {
			if (is_object($data[$key]) || is_array($data[$key])) {
				$data[$key] = self::convertToArray($data[$key]);
			}
		}

		return $data;
	}

	/**
	 * @param string|callable $content
	 * @param mixed $param
	 *
	 * @return callable|string
	 */
	public static function compileContent(callable|string $content, mixed $param): callable|string
	{
		if (is_string($content)) {
			return $content;
		}

		if (is_callable($content)) {
			return $content($param);
		}

		return $content;
	}

	/**
	 * @param array $item
	 * @param array $array
	 *
	 * @return array
	 */
	public static function includeInArray(array $item, array $array): array
	{
		return array_merge($array, [$item['name'] => $item['content']]);
	}

	/**
	 * Multidimensional array sorting by column and direction
	 *
	 * @param $arr
	 * @param $col
	 * @param int $dir
	 */
	public static function arraySortByColumn(&$arr, $col, $dir = SORT_ASC): void
	{
		$sort_col = [];
		foreach ($arr as $key => $row) {

			if (empty($arr[$key])) {
				unset($arr[$key]);
				continue;
			}
			$sort_col[$key] = $row[$col];
		}

		array_multisort($sort_col, $dir, $arr);
	}

	/**
	 * @param array $columns
	 * @param $searchParam
	 *
	 * @return array
	 */
	public static function getColumnsParam(array $columns, $searchParam): array
	{
		return array_reduce($columns, static function ($carry, $col) use ($searchParam) {
			$carry[] = $col[$searchParam];
			return $carry;
		}, []);
	}

	/**
	 * @param array $columns
	 * @param $selectedColumn
	 * @return bool
	 */
	public static function isDateColumn(array $columns, $selectedColumn): bool
	{
		foreach ($columns as $key => $column) {
			if ($column['name'] === $selectedColumn) {
				return ($column['filter'] === 'DateFilter' || $column['filter'] === 'DateTimeFilter');
			}
		}

		return false;
	}

	/**
	 * @param array $columns
	 * @return bool
	 */
	public static function getPrimaryColumn(array $columns): bool
	{
		foreach ($columns as $key => $column) {
			if (isset($column['primaryColumn']) && $column['primaryColumn']) {
				return $column['name'];
			}
		}

		return false;
	}
}