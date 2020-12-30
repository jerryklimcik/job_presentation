<?php

namespace App\Services\Datatables\Searches\SelectedRows;

use App\Services\Datatables\Interfaces\ISearch;
use App\Services\Datatables\Searches\ExtendedSearch\WhereBuilder\WhereQuery;
use App\Services\Datatables\Searches\Interfaces\ISearchWrapper;

class SelectedRows implements ISearch
{
    /** @var ISearchWrapper */
    private ISearchWrapper $wrapper;

    /** @var WhereQuery */
    private WhereQuery $whereQuery;

    public function __construct(ISearchWrapper $wrapper, WhereQuery $whereQuery)
    {
        $this->wrapper = $wrapper;
        $this->whereQuery = $whereQuery;
    }

    /**
     * @return bool
     */
    public function searchData(): bool
    {
        if (empty($this->wrapper->getSearch()->selectedRows)) {
            return false;
        }

        foreach ($this->wrapper->getColumns() as $column) {
            if (isset($column['primaryColumn']) && $column['primaryColumn']) {
                $this->wrapper->getBuilder()->where(function ($query) use ($column) {
                    $this->whereQuery->add($query, $column['name'], 'in', $this->wrapper->getSearch()->selectedRows,
                        'and');
                });
                return true;
            }
        }

        return false;
    }
}