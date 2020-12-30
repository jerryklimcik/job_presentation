<?php

namespace App\Services\Datatables\Processors;

use Exception;
use Illuminate\Http\Request;

class RenderProcessor
{
	/** @var Request */
	private Request $request;

	/** @var DataProcessor */
	private DataProcessor $dataProcessor;

	/** @var SummaryProcessor */
	private SummaryProcessor $summaryProcessor;

	/** @var integer */
	private int $totalCount;

	/**
	 * @param Request $request
	 * @param DataProcessor $dataProcessor
	 * @param SummaryProcessor $summaryProcessor
	 * @param $totalCount
	 */
	public function __construct(
		Request $request,
		DataProcessor $dataProcessor,
		SummaryProcessor $summaryProcessor,
		$totalCount
	) {
		$this->request = $request;
		$this->dataProcessor = $dataProcessor;
		$this->summaryProcessor = $summaryProcessor;
		$this->totalCount = $totalCount;
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public function render(): array
	{
		return [
			'draw' => (int)$this->request->input('data.draw'),
			'recordsTotal' => $this->totalCount,
			'recordsFiltered' => $this->totalCount,
			'data' => $this->dataProcessor->getData(),
			'input' => $this->request->all(),
			'summary' => $this->summaryProcessor->getSummary(),
		];
	}
}