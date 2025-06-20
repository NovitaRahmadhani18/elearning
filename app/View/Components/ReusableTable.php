<?php

namespace App\View\Components;

use App\CustomClasses\TableData;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;

class ReusableTable extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public TableData $tableData,
        public ?string $title = null,
    ) {}

    public function getData()
    {
        return $this->tableData->query->simplePaginate($this->tableData->perPage)->withQueryString();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.reusable-table');
    }
}
