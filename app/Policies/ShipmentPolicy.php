<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShipmentPolicy
{
    /**
     * Determine whether the user can view any models.
     * Both Admin SCM and PIC Sales can view shipments list
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdminSCM() || $user->isPICSales();
    }

    /**
     * Determine whether the user can view the model.
     * Both Admin SCM and PIC Sales can view shipment details
     */
    public function view(User $user, Shipment $shipment): bool
    {
        return $user->isAdminSCM() || $user->isPICSales();
    }

    /**
     * Determine whether the user can create models.
     * NEW SYSTEM: Only PIC Sales can create shipments
     * Admin SCM is for monitoring only
     */
    public function create(User $user): bool
    {
        return $user->isPICSales();
    }

    /**
     * Determine whether the user can update the model (FULL EDIT).
     * Only PIC Sales can do full edit (all fields)
     */
    public function update(User $user, Shipment $shipment): bool
    {
        return $user->isPICSales();
    }

    /**
     * CRITICAL: Determine whether the user can update shipment status and monitoring fields
     * Only Admin SCM can update monitoring fields:
     * - ATA Port, ATA Customer
     * - Status
     * - Delivery Note Number, Supplier Invoice
     * - Shipping Cost, Customs Cost, Other Costs
     * 
     * This is for monitoring and tracking purposes only (FR-ST-03)
     */
    public function updateStatus(User $user, Shipment $shipment): bool
    {
        return $user->isAdminSCM();
    }

    /**
     * Determine whether the user can delete the model.
     * Only PIC Sales can delete shipments (they created them)
     */
    public function delete(User $user, Shipment $shipment): bool
    {
        // Delivered shipments cannot be deleted by anyone, regardless of role
        if ($shipment->status === 'Delivered') {
            return false;
        }

        return $user->isPICSales();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Shipment $shipment): bool
    {
        return $user->isPICSales();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Shipment $shipment): bool
    {
        return $user->isPICSales();
    }
}
