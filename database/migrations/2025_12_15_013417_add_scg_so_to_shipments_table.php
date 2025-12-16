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
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'delivery_status')) {
                $table->string('delivery_status')->nullable()->after('status');
            }
            
            // Update the status column if it exists and is an enum
            if (Schema::hasColumn('shipments', 'status') && Schema::getColumnType('shipments', 'status') === 'string') {
                $table->string('status')->default('draft')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'delivery_status')) {
                $table->dropColumn('delivery_status');
            }
        });
    }
};
