<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    /**
     * Determine whether the user can view any suppliers.
     */
    public function viewAny(User $user): bool
    {
        // Both admin_scm and pic_sales can view suppliers list
        return $user->isAdminSCM() || $user->isPICSales();
    }

    /**
     * Determine whether the user can view the supplier.
     */
    public function view(User $user, Supplier $supplier): bool
    {
        // Both roles can view supplier details
        return $user->isAdminSCM() || $user->isPICSales();
    }

    /**
     * Determine whether the user can create suppliers.
     */
    public function create(User $user): bool
    {
        // Only pic_sales can create suppliers
        return $user->isPICSales();
    }

    /**
     * Determine whether the user can update the supplier.
     */
    public function update(User $user, Supplier $supplier): bool
    {
        // Only pic_sales can update suppliers
        return $user->isPICSales();
    }

    /**
     * Determine whether the user can delete the supplier.
     */
    public function delete(User $user, Supplier $supplier): bool
    {
        // Only pic_sales can delete suppliers
        return $user->isPICSales();
    }
}
