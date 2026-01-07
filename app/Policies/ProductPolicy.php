<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        // Both admin_scm and pic_sales can view products list
        return $user->isAdminSCM() || $user->isPICSales();
    }

    /**
     * Determine whether the user can view the product.
     */
    public function view(User $user, Product $product): bool
    {
        // Both roles can view product details
        return $user->isAdminSCM() || $user->isPICSales();
    }

    /**
     * Determine whether the user can create products.
     */
    public function create(User $user): bool
    {
        // Only pic_sales can create products
        return $user->isPICSales();
    }

    /**
     * Determine whether the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        // Only pic_sales can update products
        return $user->isPICSales();
    }

    /**
     * Determine whether the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        // Only pic_sales can delete products
        return $user->isPICSales();
    }
}
