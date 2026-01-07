<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add unique constraints to prevent duplicate document numbers
     */
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Add unique constraints for document numbers
            // These fields must be globally unique across all shipments
            $table->unique('customer_po', 'shipments_customer_po_unique');
            $table->unique('scg_po', 'shipments_scg_po_unique');
            $table->unique('scg_so', 'shipments_scg_so_unique');
            $table->unique('booking_number', 'shipments_booking_number_unique');
            $table->unique('supplier_invoice', 'shipments_supplier_invoice_unique');
            $table->unique('delivery_note_number', 'shipments_delivery_note_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Drop unique constraints
            $table->dropUnique('shipments_customer_po_unique');
            $table->dropUnique('shipments_scg_po_unique');
            $table->dropUnique('shipments_scg_so_unique');
            $table->dropUnique('shipments_booking_number_unique');
            $table->dropUnique('shipments_supplier_invoice_unique');
            $table->dropUnique('shipments_delivery_note_unique');
        });
    }
};
