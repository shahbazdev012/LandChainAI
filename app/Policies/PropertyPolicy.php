<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

/**
 * This is an officer-operated registry: both admins and officers manage and
 * verify all properties. Destructive actions are reserved for admins.
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
        return $this->isStaff($user);
    }

    public function verify(User $user, Property $property): bool
    {
        return $this->isStaff($user);
    }

    /**
     * Final human sign-off after a passing automated check.
     */
    public function approve(User $user, Property $property): bool
    {
        return $this->isStaff($user);
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->hasRole('admin');
    }

    private function isStaff(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'officer']);
    }
}
