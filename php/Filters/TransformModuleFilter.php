<?php

namespace App\Services\Datatables\Filters;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class TransformModuleFilter
{
    /** @var Request */
    private Request $request;

    /** @var Builder */
    private Builder $builder;

    /** @var string */
    private string $module;

    /** @var  array */
    private array $translatedFilters = [];

    public function __construct(Request $request, Builder $builder, $module)
    {
        $this->request = $request;
        $this->builder = $builder;
        $this->module = $module;
    }

    /**
     * @return array
     */
    public function getTranslatedFilters(): array
    {
        return $this->translatedFilters;
    }

    /**
     * Check if module filters exists and transform columns
     * 
     * @return bool
     */
    public function transform(): bool
    {
        if (!$this->request->has('data.moduleFilterData')) {
            return false;
        }

        $this->checkFilters($this->request->input('data.moduleFilterData'));
        foreach ($this->translatedFilters as $column => $value) {
            $this->builder->where($column, $value);
        }

        return true;
    }

    /**
     * @param array $filters
     */
    private function checkFilters(array $filters): void
    {
        if (isset($filters['year']) && $filters['year'] !== '-1') {
            $this->translatedFilters[$this->translateYear()] = $filters['year'];
        }

        if (isset($filters['month']) && $filters['month'] !== '-1') {
            $this->translatedFilters[$this->translateMonth()] = $filters['month'];
        }

        if (isset($filters['typeMachine']) && $filters['typeMachine'] !== '-1') {
            $this->translatedFilters[$this->translateTypeMachine()] = $filters['typeMachine'];
        }

        if (isset($filters['currency']) && $filters['currency'] !== '-1') {
            $this->translatedFilters[$this->translateCurrency()] = $filters['currency'];
        }
    }

    /**
     * Year translator
     *
     * @return string
     */
    private function translateYear(): string
    {
        $translator = [
            'accountroom' => 'account_after_year',
            'account'     => 'account_after_year_account',
        ];

        return $translator[$this->module];
    }

    /**
     * Month translator
     *
     * @return string
     */
    private function translateMonth(): string
    {
        $translator = [
            'accountroom' => 'account_after_month',
            'account'     => 'account_after_month_account',
        ];

        return $translator[$this->module];
    }

    /**
     * Type machine translator
     *
     * @return string
     */
    private function translateTypeMachine(): string
    {
        $translator = [
            'accountroom' => 'id_type_machine',
            'account'     => 'id_type_machine',
        ];

        return $translator[$this->module];
    }

    /**
     * Currency translator
     *
     * @return string
     */
    private function translateCurrency(): string
    {
        $translator = [
            'accountroom' => 'id_currency',
            'account'     => 'id_currency',
        ];

        return $translator[$this->module];
    }
}