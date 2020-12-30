<?php

namespace App\Services\Datatables\Processors;

use App\Services\Datatables\Helper;
use Closure;
use Exception;

class DataProcessor
{
	/** @var array */
	private array $results;

	/** @var array */
	private array $extraColumns;

	/** @var array */
	private array $columns;

	/** @var Closure */
	private Closure $rowClass;

	/** @var SummaryProcessor */
	private SummaryProcessor $summaryProcessor;

	/** @var string */
	protected string $rowNumber;

	/** @var DataTransformerProcessor */
	private DataTransformerProcessor $dataTransformerProcessor;

	/**
	 * @param array|object $results
	 * @param array $extraColumns
	 * @param array $columns
	 * @param Closure|null $rowClass
	 * @param SummaryProcessor $summaryProcessor
	 * @param DataTransformerProcessor $dataTransformerProcessor
	 * @param string $rowNumber
	 */
	public function __construct(
		array|object $results,
		array $extraColumns,
		array $columns,
		Closure $rowClass = null,
		SummaryProcessor $summaryProcessor,
		DataTransformerProcessor $dataTransformerProcessor,
		string $rowNumber
	) {
		$this->results = $results;
		$this->extraColumns = $extraColumns;
		$this->columns = $columns;
		$this->rowClass = $rowClass;
		$this->summaryProcessor = $summaryProcessor;
		$this->rowNumber = $rowNumber;

		$this->dataTransformerProcessor = $dataTransformerProcessor;
	}

	/**
	 * Output data processing
	 *
	 * @return array
	 * @throws Exception
	 */
	public function getData(): array
	{
		$row_number = $this->rowNumber;
		$output = [];
		foreach ($this->results as $row) {
			$row_number++;

			$value = $this->dataTransformerProcessor->transform($row);

			if (!empty($this->extraColumns)) {
				$value = $this->addExtraColumns($value, $row);
			}

			$value = $this->sortArrayByDefinition($value);

			if ($this->rowClass) {
				$value = $this->addRowClass($value);
			}

			$value = $this->flatten($value);

			$this->addToSummaryRow($row);
			$output[] = $value;
		}

		return $output;
	}

	/**
	 * Processing of extra columns
	 *
	 * @param $data
	 * @param $row
	 * @return array
	 */
	private function addExtraColumns($data, $row): array
	{
		foreach ($this->extraColumns as $extraColumn) {
			$compiledContent = Helper::compileContent($extraColumn['callback']['content'], $row);
			$data = array_merge($data, [$extraColumn['info']['name'] => $compiledContent]);
		}

		return $data;
	}

	/**
	 * Row settings
	 *
	 * @param array $row
	 * @return array
	 */
	private function addRowClass(array $row): array
	{
		$row['DT_RowClass'] = Helper::compileContent($this->rowClass, $row);
		return $row;
	}

	/**
	 * Column sum from row
	 *
	 * @param $row
	 */
	protected function addToSummaryRow($row): void
	{
		$this->summaryProcessor->addToSummaryRow($row);
	}

	/**
	 * Array without keys
	 *
	 * @param array $columns
	 * @return array
	 */
	public function flatten(array $columns): array
	{
		$flattenArray = [];
		$exceptions = ['DT_RowClass'];
		foreach ($columns as $key => $value) {
			if (in_array($key, $exceptions)) {
				$flattenArray[$key] = $value;
				continue;
			}
			$flattenArray[] = $value;
		}

		return $flattenArray;
	}

	/**
	 * @param array $row
	 * @return array
	 */
	private function sortArrayByDefinition(array $row): array
	{
		$allColumns = $this->columns;
		foreach ($this->extraColumns as $extraColumn) {
			$allColumns[] = $extraColumn['info'];
		}

		$columnsDefinition = [];
		foreach ($allColumns as $cd) {
			$columnsDefinition[$cd['sequence']] = $cd['name'];
		}
		ksort($columnsDefinition);

		$orderedRow = [];
		foreach ($columnsDefinition as $i) {
			$orderedRow[$i] = $row[$i];
		}

		return $orderedRow;
	}
}