<?php

namespace App\Services\Datatables\Searches\ExtendedSearch\WhereBuilder;

use App\Services\Datatables\Constants;
use Illuminate\Database\Query\Builder;

class WhereRawQuery extends WhereBuilder
{

    /**
     * @return Builder
     */
    protected function likeOperator(): Builder
    {
        if ($this->andor === Constants::AND_OPERATOR) {
            return $this->builder->whereRaw($this->column . ' like "%' . $this->search_text . '%"');
        }

        return $this->builder->orWhereRaw($this->column . ' like "%' . $this->search_text . '%"');
    }

    /**
     * @return Builder
     */
    protected function notLikeOperator(): Builder
    {
        if ($this->andor === Constants::AND_OPERATOR) {
            return $this->builder->whereRaw($this->column . ' not like "%' . $this->search_text . '%"');
        }

        return $this->builder->orWhereRaw($this->column . ' not like "%' . $this->search_text . '%"');
    }

    /**
     * @return Builder
     */
    protected function nullOperator(): Builder
    {
        if ($this->andor === Constants::AND_OPERATOR) {
            return $this->builder->whereRaw($this->column . ' IS NULL');
        }

        return $this->builder->orWhereRaw($this->column . ' IS NULL');
    }

    /**
     * @return Builder
     */
    protected function notNullOperator(): Builder
    {
        if ($this->andor === Constants::AND_OPERATOR) {
            return $this->builder->whereRaw($this->column . ' IS NOT NULL');
        }

        return $this->builder->orWhereRaw($this->column . ' IS NOT NULL');
    }

    /**
     * @return Builder
     */
    protected function defaultOperator(): Builder
    {
        if ($this->andor === Constants::AND_OPERATOR) {
            return $this->builder->whereRaw($this->column . ' ' . $this->operator . ' "' . $this->search_text . '"');
        }

        return $this->builder->orWhereRaw($this->column . ' ' . $this->operator . ' "' . $this->search_text . '"');
    }
}