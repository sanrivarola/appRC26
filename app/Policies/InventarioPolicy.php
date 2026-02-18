<?php

namespace App\Policies;

use App\Models\User;

class InventarioPolicy
{
    public function admin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user): bool
    {
        return $user->hasAnyRole(['admin','editor','colaborador']);
    }
}
