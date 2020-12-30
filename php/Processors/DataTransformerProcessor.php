<?php

namespace App\Services\Datatables\Processors;

use App\Services\Datatables\Constants;
use Carbon\Carbon;
use Exception;

class DataTransformerProcessor
{
	/** @var array */
	private array $columns;

	/**
	 * @param $columns
	 */
	public function __construct($columns)
	{
		$this->columns = $columns;
	}

	/**
	 * Column transform by setting
	 *
	 * @param $row
	 * @return array
	 * @throws Exception
	 */
	public function transform($row): array
	{
		$filtered_data = [];
		foreach ($this->columns as $column) {
			if (array_key_exists($column['name'], $row)) {

				if (isset($column['type'])) {
					$filtered_data[$column['name']] = $this->transformDecider($column['type'], $row->{$column['name']});
					continue;
				}

				$filtered_data[$column['name']] = $this->transformDecider($column['filter'] ?? null, $row->{$column['name']});
			}
		}

		return $filtered_data;
	}

	/**
	 * @param $filterType
	 * @param $value
	 * @return string
	 * @throws Exception
	 */
	private function transformDecider($filterType, $value): string
	{
		if (is_null($filterType) || trim($value) === '') {
			return $value;
		}

		switch ($filterType) {
			case Constants::BOOLEAN_FILTER:
				return $this->booleanFilter($value);
				break;

			case Constants::DATE_FILTER:
				return $this->dateFilter($value);
				break;

			case Constants::DATE_TIME_FILTER:
				return $this->dateTimeFilter($value);
				break;

			case Constants::EMAIL_TYPE:
				return $this->emailType($value);
				break;

			case Constants::NOTE_TYPE:
				return $this->noteType($value);
				break;

			default:
				return $value;
				break;
		}
	}

	/**
	 * @param $value
	 * @return string
	 */
	private function booleanFilter($value): string
	{
		if ($value === 1) {
			return __('general.Yes');
		}
		return __('general.No');
	}

	/**
	 * @param $date
	 * @return string
	 * @throws Exception
	 */
	private function dateFilter($date): string
	{
		try {
			return Carbon::createFromFormat('Y-m-d', $date)->format('d.m.Y');
		} catch (Exception $e) {
			throw new Exception($e);
		}
	}

	/**
	 * @param $date
	 * @return string
	 * @throws Exception
	 */
	private function dateTimeFilter($date): string
	{
		try {
			return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y H:i:s');
		} catch (Exception $e) {
			throw new Exception($e);
		}
	}

	/**
	 * @param $value
	 * @return string
	 */
	private function emailType($value): string
	{
		return '<a href="mailto:' . $value . '">' . $value . '</a>';
	}

	/**
	 * @param $value
	 * @return string
	 */
	private function noteType($value): string
	{
		if (strlen($value) < 50) {
			return $value;
		}

		return '<span data-balloon-length="fit" data-balloon="' . $value . '" data-balloon-pos="top">' . substr($value, 0, 50) . "..." . '</span>';
	}
}