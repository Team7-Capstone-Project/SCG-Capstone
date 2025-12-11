<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShipmentPolicy
{
    /**
     * Determine whether the user can view any models.
     * Both Staf SCM and PIC/Sales can view shipments list
     */
    public function viewAny(User $user): bool
    {
        return $user->isStafSCM() || $user->isPICSales() || $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     * Both Staf SCM and PIC/Sales can view shipment details
     */
    public function view(User $user, Shipment $shipment): bool
    {
        return $user->isStafSCM() || $user->isPICSales() || $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     * Only Staf SCM can create shipments
     */
    public function create(User $user): bool
    {
        return $user->isStafSCM() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     * Only Staf SCM can update shipments
     */
    public function update(User $user, Shipment $shipment): bool
    {
        return $user->isStafSCM() || $user->isAdmin();
    }

    /**
     * CRITICAL: Determine whether the user can update shipment status
     * Only Staf SCM can update status (FR-ST-03)
     * PIC/Sales CANNOT update status (read-only access)
     */
    public function updateStatus(User $user, Shipment $shipment): bool
    {
        return $user->isStafSCM() || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     * Only Admin can delete shipments
     */
    public function delete(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin();
    }
}
