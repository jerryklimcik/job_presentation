<?php

namespace App\Services\Datatables\Processors;

use App\Helper\Currency;
use App\Services\Datatables\Constants;

class SummaryProcessor
{
	/** @var array */
	private array $columns;

	/** @var array */
	private array $summaryColumns = [];

	public function __construct(array $columns)
	{
		$this->columns = $columns;
		$this->init();
	}

	/**
	 * @return array
	 */
	public function getSummaryColumns(): array
	{
		return $this->summaryColumns;
	}

	/**
	 * Columns to summary
	 */
	private function init(): void
	{
		foreach ($this->columns as $column) {
			if (!isset($column['filter']) || $column['filter'] !== Constants::MONEY_FILTER) {
				continue;
			}

			if (isset($column['filterData']['summary_row']) && $column['filterData']['summary_row']) {
				$this->summaryColumns[$column['name']] = [
					'currency_column' => $column['filterData']['currency_column'],
				];
			}
		}
	}

	/**
	 * @param $row
	 * @return bool
	 */
	public function addToSummaryRow($row): bool
	{
		if (empty($this->summaryColumns)) {
			return false;
		}

		foreach ($row as $column_name => $value) {
			if (!array_key_exists($column_name, $this->summaryColumns)) {
				continue;
			}

			if ($this->summaryColumns[$column_name]['currency_column']) {
				$column = $this->summaryColumns[$column_name]['currency_column'];
				$currency = $row->{$column};
			} else {
				$currency = Currency::defaultCurrencyId();
			}

			if (!isset($this->summaryColumns[$column_name]['value'][$currency])) {
				$this->summaryColumns[$column_name]['value'][$currency] = $value;
			} else {
				$this->summaryColumns[$column_name]['value'][$currency] += $value;
			}
		}

		return true;
	}

	/**
	 * @return array
	 */
	public function getSummary(): array
	{
		$summary = [];
		if (empty($this->summaryColumns)) {
			return $summary;
		}

		foreach ($this->summaryColumns as $column_name => $column) {
			if (!array_key_exists('value', $column)) {
				continue;
			}

			foreach ($column['value'] as $id_currency => $value) {
				$value = Currency::printMoney($value, $id_currency);
				$summary[$column_name]['value'][$id_currency] = $value;
			}
		}

		return $summary;
	}
}