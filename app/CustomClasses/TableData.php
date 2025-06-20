<?php

namespace App\CustomClasses;

use Illuminate\Database\Eloquent\Builder;

class TableData
{
    public function __construct(
        public Builder $query,
        public array $cols = [],
        public ?string $search = null,
        public ?string $sortBy = null,
        public ?string $sortDirection = null,
        public ?int $perPage = null,
        public ?string $id = 'table',
    ) {}

    static public function make(
        Builder $query,
        array $cols = [],
        ?string $search = null,
        ?string $sortBy = null,
        ?string $sortDirection = null,
        ?int $perPage = 10,
        ?string $id = 'table'
    ): self {
        return new self($query, $cols, $search, $sortBy, $sortDirection, $perPage, $id);
    }
}
