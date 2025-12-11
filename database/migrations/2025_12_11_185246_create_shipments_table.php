<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            
            // Document Numbers
            $table->string('customer_po')->nullable();
            $table->string('scg_po')->nullable();
            $table->string('booking_number')->nullable();
            $table->string('delivery_note_number')->nullable();
            $table->string('supplier_invoice')->nullable();
            
            // Status
            $table->enum('status', ['Pending', 'In Transit', 'Delivered', 'Cancelled'])->default('Pending');
            
            // Critical Dates for OTD Calculation
            $table->date('etd_port')->nullable()->comment('Estimated Time Departure from Port');
            $table->date('eta_port')->nullable()->comment('Estimated Time Arrival at Port');
            $table->date('ata_port')->nullable()->comment('Actual Time Arrival at Port');
            $table->date('customer_receiving_schedule')->nullable()->comment('Planned delivery date - OTD baseline');
            $table->date('ata_customer')->nullable()->comment('Actual Time Arrival at Customer - OTD comparison');
            
            // Cost Structure
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('customs_cost', 15, 2)->default(0);
            $table->decimal('other_costs', 15, 2)->default(0);
            
            // Additional Info
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('status');
            $table->index('customer_id');
            $table->index('ata_customer');
            $table->index('customer_receiving_schedule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
