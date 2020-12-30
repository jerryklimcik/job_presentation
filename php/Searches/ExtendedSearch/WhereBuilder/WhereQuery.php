<?php

namespace App\Services\Datatables\Searches\ExtendedSearch\WhereBuilder;

use App\Services\Datatables\Constants;
use Illuminate\Database\Query\Builder;

class WhereQuery extends WhereBuilder
{
    /**
     * @return Builder
     */
    protected function likeOperator(): Builder
    {
        if ($this->andor === Constants::AND_OPERATOR) {
            return $this->builder->where($this->column, 'LIKE', '%' . $this->search_text . '%');
        }

        return $this->builder->orWhere($this->column, 'LIKE', '%' . $this->search_text . '%');
    }

    /**
     * @return Builder
     */
    protected function notLikeOperator(): Builder
    {
        if ($this->andor === Constants::AND_OPERATOR) {
            return $this->builder->where($this->column, 'not like', '%' . $this->search_text . '%');
        }

        return $this->builder->orWhere($this->column, 'not like', '%' . $this->search_text . '%');
    }

    /**
     * @return Builder
     */
    protected function inOperator(): Builder
    {
        if ($this->andor === Constants::AND_OPERATOR) {
            return $this->builder->whereIn($this->column, $this->search_text);
        }

        return $this->builder->orWhereIn($this->column, $this->search_text);
    }

    /**
     * @return Builder
     */
    protected function nullOperator(): Builder
    {
        if ($this->andor === Constants::AND_OPERATOR) {
            return $this->builder->whereNull($this->column);
        }

        return $this->builder->orWhereNull($this->column);
    }

    /**
     * @return Builder
     */
    protected function notNullOperator(): Builder
    {
        if ($this->andor === Constants::AND_OPERATOR) {
            return $this->builder->whereNotNull($this->column);
        }

        return $this->builder->orWhereNotNull($this->column);
    }

    /**
     * @return Builder
     */
    protected function defaultOperator(): Builder
    {
        if ($this->andor === Constants::AND_OPERATOR) {
            return $this->builder->where($this->column, $this->operator, $this->search_text);
        }

        return $this->builder->orWhere($this->column, $this->operator, $this->search_text);
    }
}