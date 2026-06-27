<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

/**
 * Role rules:
 * - data_entry: create + view only (no update, no approve, no delete).
 * - officer:    create + update + approve.
 * - admin:      everything, including delete.
 */
class PropertyPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, Property $property): bool
    {
        return $this->isStaff($user);
    }

    public function create(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function update(User $user, Property $property): bool
    {
        return $user->hasAnyRole(['admin', 'officer']);
    }

    public function approve(User $user, Property $property): bool
    {
        return $user->hasAnyRole(['admin', 'officer']);
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->hasRole('admin');
    }

    private function isStaff(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'officer', 'data_entry']);
    }
}
