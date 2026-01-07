<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    /**
     * Determine whether the user can view any customers.
     */
    public function viewAny(User $user): bool
    {
        // Both admin_scm and pic_sales can view customers list
        return $user->isAdminSCM() || $user->isPICSales();
    }

    /**
     * Determine whether the user can view the customer.
     */
    public function view(User $user, Customer $customer): bool
    {
        // Both roles can view customer details
        return $user->isAdminSCM() || $user->isPICSales();
    }

    /**
     * Determine whether the user can create customers.
     */
    public function create(User $user): bool
    {
        // Only pic_sales can create customers
        return $user->isPICSales();
    }

    /**
     * Determine whether the user can update the customer.
     */
    public function update(User $user, Customer $customer): bool
    {
        // Only pic_sales can update customers
        return $user->isPICSales();
    }

    /**
     * Determine whether the user can delete the customer.
     */
    public function delete(User $user, Customer $customer): bool
    {
        // Only pic_sales can delete customers
        return $user->isPICSales();
    }
}
