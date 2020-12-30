<?php

namespace App\Services\Datatables\Searches\Interfaces;

use App\Services\Datatables\Interfaces\ISearch;
use stdClass;

interface ISearchBuilder
{
    /** @return ISearch */
    public function searchType(): ISearch;

    /**
     * @param stdClass $search
     * @return void
     */
    public function setSearch(stdClass $search): void;
}