<?php

namespace App\Services\Datatables\Interfaces;

use App\Services\Datatables\Searches\Interfaces\ISearchWrapper;
use Illuminate\Database\Query\Builder;

interface IWhereSearch
{
    public function search(ISearchWrapper $wrapper, Builder $builder);
}