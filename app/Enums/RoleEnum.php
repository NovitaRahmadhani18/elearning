<?php

namespace App\Enums;

use Illuminate\Contracts\View\View;

enum RoleEnum: string
{

    case ADMIN = 'admin';
    case TEACHER = 'teacher';
    case USER = 'user';

    // extra helper to allow for greater customization of displayed values, without disclosing the name/value data directly
    public function label(): string
    {
        return match ($this) {
            static::ADMIN => 'Admin',
            static::TEACHER => 'Teacher',
            static::USER => 'User',
        };
    }

    public function badge(): View
    {
        return view('components.role-badge', [
            'label' => $this->label(),
            'color' => match ($this) {
                static::ADMIN => 'bg-red-500',
                static::TEACHER => 'bg-blue-500',
                static::USER => 'bg-green-500',
            },
        ]);
    }
}
