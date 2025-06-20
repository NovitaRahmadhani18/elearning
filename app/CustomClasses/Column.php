<?php

namespace App\CustomClasses;

use Illuminate\Support\Facades\View;

class Column
{
    public View|string|null $view = 'reusable-table.column';

    public string $key;

    public string $label;

    public function __construct(
        $key,
        $label = '',
    ) {
        $this->key = $key;
        $this->label = $label ?: $key;
    }

    public function setView(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    public static function make(
        string $key,
        string $label = '',
    ): self {
        return new self($key, $label);
    }
}
